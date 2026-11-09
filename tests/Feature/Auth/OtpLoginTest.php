<?php

use App\Contracts\OtpServiceContract;
use App\Models\OneTimePassword;
use App\Models\Status;
use App\Models\User;
use App\Services\Otp\AbstractOtpService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

/**
 * Binds an OTP driver that hands out the given codes in order and can simulate a
 * failed delivery, so tests can tell one code from the next.
 *
 * @param  array<int, string>  $codes
 */
function fakeOtpDriver(array $codes, bool $deliverySucceeds = true): void
{
    app()->instance(OtpServiceContract::class, new class($codes, $deliverySucceeds) extends AbstractOtpService
    {
        /** @param  array<int, string>  $codes */
        public function __construct(private array $codes, private bool $deliverySucceeds) {}

        protected function generateCode(): string
        {
            return array_shift($this->codes);
        }

        protected function dispatch(string $phone, string $code): bool
        {
            return $this->deliverySucceeds;
        }
    });
}

it('renders the otp login screen', function () {
    $this->get('/login')->assertStatus(200);
});

it('creates an inactive user and stores a hashed otp before verification', function () {
    $this->post('/login/otp', ['phone' => '9876543210'])->assertRedirect(route('login'));

    $user = User::where('phone', '9876543210')->sole();
    $otp = OneTimePassword::sole();

    expect($user->status_id)->toBe(Status::USER_INACTIVE)
        ->and($user->phone_verified_at)->toBeNull()
        ->and($otp->user_id)->toBe($user->id)
        ->and($otp->code_hash)->not->toBe('1234')
        ->and(Hash::check('1234', $otp->code_hash))->toBeTrue();
    $this->assertGuest();
});

it('rejects an invalid phone format', function () {
    $this->post('/login/otp', ['phone' => '12345'])
        ->assertSessionHasErrors('phone');
});

it('is rate limited after sending too many otps', function () {
    config(['otp.max_sends_per_window' => 2]);

    $this->post('/login/otp', ['phone' => '9876543211']);
    $this->post('/login/otp', ['phone' => '9876543211']);

    $this->post('/login/otp', ['phone' => '9876543211'])
        ->assertSessionHasErrors('phone');
});

it('logs in an existing user by phone with the correct code', function () {
    $user = User::factory()->create(['phone' => '9876543212']);

    $this->post('/login/otp', ['phone' => '9876543212']);

    $this->post('/login/otp/verify', ['phone' => '9876543212', 'code' => '1234'])
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
});

it('activates a new user and logs them in once the otp is verified', function () {
    $this->post('/login/otp', ['phone' => '9876543213']);

    $this->post('/login/otp/verify', ['phone' => '9876543213', 'code' => '1234'])
        ->assertRedirect(route('profile.edit'));

    $user = User::where('phone', '9876543213')->sole();
    expect($user->status_id)->toBe(Status::USER_ACTIVE)
        ->and($user->phone_verified_at)->not->toBeNull()
        ->and(OneTimePassword::sole()->consumed_at)->not->toBeNull();
    $this->assertAuthenticatedAs($user);
});

it('rejects an incorrect otp code', function () {
    $this->post('/login/otp', ['phone' => '9876543214']);

    $this->post('/login/otp/verify', ['phone' => '9876543214', 'code' => '0000'])
        ->assertSessionHasErrors('code');

    $this->assertGuest();
});

it('is rate limited after too many verify attempts', function () {
    config(['otp.max_verify_attempts' => 2]);

    $this->post('/login/otp', ['phone' => '9876543215']);

    $this->post('/login/otp/verify', ['phone' => '9876543215', 'code' => '0000']);
    $this->post('/login/otp/verify', ['phone' => '9876543215', 'code' => '0000']);

    $this->post('/login/otp/verify', ['phone' => '9876543215', 'code' => '1234'])
        ->assertSessionHasErrors('code');

    $this->assertGuest();
});

it('reuses an existing user without changing their status', function () {
    $user = User::factory()->create(['phone' => '9876543216']);

    $this->post('/login/otp', ['phone' => '9876543216']);
    $this->post('/login/otp/verify', ['phone' => '9876543216', 'code' => '1234']);

    expect(User::where('phone', '9876543216')->count())->toBe(1)
        ->and($user->fresh()->status_id)->toBe(Status::USER_ACTIVE);
    $this->assertAuthenticatedAs($user);
});

it('shows the same otp screen for new and existing numbers', function () {
    User::factory()->create(['phone' => '9876543217']);

    $this->post('/login/otp', ['phone' => '9876543217']);
    $existing = $this->get('/login')->assertOk()->getContent();

    session()->flush();
    $this->post('/login/otp', ['phone' => '9876543218']);
    $new = $this->get('/login')->assertOk()->getContent();

    $normalise = fn (string $html, string $phone): string => preg_replace('/[A-Za-z0-9]{40}/', 'TOKEN', str_replace($phone, 'X', $html));

    expect($normalise($new, '9876543218'))->toBe($normalise($existing, '9876543217'));
});

it('normalises a formatted number to the stored 10-digit form', function () {
    $this->post('/login/otp', ['phone' => '+91 98765-43219'])->assertRedirect(route('login'));

    expect(User::where('phone', '9876543219')->exists())->toBeTrue();
});

it('keeps a new user inactive and counts the attempt when the code is wrong', function () {
    $this->post('/login/otp', ['phone' => '9876543220']);

    $this->post('/login/otp/verify', ['phone' => '9876543220', 'code' => '0000'])
        ->assertSessionHasErrors(['code' => 'The OTP is incorrect.']);

    expect(OneTimePassword::sole()->attempts)->toBe(1)
        ->and(User::where('phone', '9876543220')->sole()->status_id)->toBe(Status::USER_INACTIVE);
});

it('rejects an expired otp', function () {
    $this->post('/login/otp', ['phone' => '9876543221']);
    $this->travel(config('otp.ttl_seconds') + 1)->seconds();

    $this->post('/login/otp/verify', ['phone' => '9876543221', 'code' => '1234'])
        ->assertSessionHasErrors(['code' => 'This OTP has expired. Please request a new one.']);

    $this->assertGuest();
});

it('rejects an otp that has already been used', function () {
    $this->post('/login/otp', ['phone' => '9876543222']);
    $this->post('/login/otp/verify', ['phone' => '9876543222', 'code' => '1234']);
    auth()->logout();

    $this->post('/login/otp/verify', ['phone' => '9876543222', 'code' => '1234'])
        ->assertSessionHasErrors(['code' => 'This OTP is no longer valid. Please request a new one.']);

    $this->assertGuest();
});

it('invalidates the otp after the maximum number of wrong codes', function () {
    config(['otp.max_verify_attempts' => 2]);
    $this->post('/login/otp', ['phone' => '9876543223']);

    $this->post('/login/otp/verify', ['phone' => '9876543223', 'code' => '0000']);
    $this->post('/login/otp/verify', ['phone' => '9876543223', 'code' => '0000'])
        ->assertSessionHasErrors(['code' => 'Too many incorrect attempts. Please request a new OTP.']);

    expect(OneTimePassword::sole()->invalidated_at)->not->toBeNull();
});

it('rejects the verification endpoint when no otp was requested', function () {
    User::factory()->create(['phone' => '9876543224']);

    $this->post('/login/otp/verify', ['phone' => '9876543224', 'code' => '1234'])
        ->assertSessionHasErrors('code');

    $this->assertGuest();
});

it('enforces the resend cooldown', function () {
    $this->post('/login/otp', ['phone' => '9876543225']);

    $this->post('/login/otp', ['phone' => '9876543225'])
        ->assertSessionHasErrors('phone');

    expect(OneTimePassword::count())->toBe(1);
});

it('accepts only the newest code after a resend', function () {
    fakeOtpDriver(['1111', '2222']);

    $this->post('/login/otp', ['phone' => '9876543226']);
    $this->travel(config('otp.resend_cooldown_seconds') + 1)->seconds();
    $this->post('/login/otp', ['phone' => '9876543226'])->assertSessionHasNoErrors();

    $this->post('/login/otp/verify', ['phone' => '9876543226', 'code' => '1111'])
        ->assertSessionHasErrors('code');
    $this->post('/login/otp/verify', ['phone' => '9876543226', 'code' => '2222'])
        ->assertSessionHasNoErrors();

    expect(OneTimePassword::orderBy('id')->first()->invalidated_at)->not->toBeNull();
    $this->assertAuthenticated();
});

it('leaves no usable otp and allows an immediate retry when delivery fails', function () {
    fakeOtpDriver(['1111'], deliverySucceeds: false);

    $this->post('/login/otp', ['phone' => '9876543227'])
        ->assertSessionHasErrors(['phone' => 'We could not send the OTP right now. Please try again.']);

    expect(OneTimePassword::count())->toBe(0)
        ->and(User::where('phone', '9876543227')->sole()->status_id)->toBe(Status::USER_INACTIVE);

    fakeOtpDriver(['2222']);
    $this->post('/login/otp', ['phone' => '9876543227'])->assertSessionHasNoErrors();
    expect(User::where('phone', '9876543227')->count())->toBe(1);
});

it('refuses to send while another request for the same number holds the lock', function () {
    $lock = Cache::lock('otp-login:9876543228', 15);
    $lock->get();

    $this->post('/login/otp', ['phone' => '9876543228'])
        ->assertSessionHasErrors(['phone' => 'Please wait a moment and try again.']);

    expect(User::where('phone', '9876543228')->exists())->toBeFalse();
    $lock->release();
});

it('does not log in or reactivate a suspended user with a valid otp', function () {
    $user = User::factory()->create(['phone' => '9876543229']);
    $user->forceFill(['is_active' => false])->save();

    $this->post('/login/otp', ['phone' => '9876543229']);

    $this->post('/login/otp/verify', ['phone' => '9876543229', 'code' => '1234'])
        ->assertSessionHasErrors(['code' => 'This account has been suspended. Please contact support.']);

    expect($user->fresh()->is_active)->toBeFalse();
    $this->assertGuest();
});

it('does not activate a suspended user whose phone was never verified', function () {
    $user = User::forceCreate(['phone' => '9876543230', 'status_id' => Status::USER_INACTIVE, 'is_active' => false]);

    $this->post('/login/otp', ['phone' => '9876543230']);
    $this->post('/login/otp/verify', ['phone' => '9876543230', 'code' => '1234']);

    expect($user->fresh()->status_id)->toBe(Status::USER_INACTIVE);
    $this->assertGuest();
});

it('lets a new user who has not set a name open the dashboard', function () {
    $this->post('/login/otp', ['phone' => '9876543231']);
    $this->post('/login/otp/verify', ['phone' => '9876543231', 'code' => '1234']);

    $this->get(route('dashboard'))->assertOk()->assertSee('Welcome back');
});
