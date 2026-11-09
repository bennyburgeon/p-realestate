<?php

use App\Models\Status;
use App\Models\User;
use Database\Seeders\StatusSeeder;

it('seeds the user statuses with their stable ids', function () {
    $this->seed(StatusSeeder::class);

    expect(Status::find(Status::USER_INACTIVE)->only(['group', 'slug']))->toBe(['group' => 'user', 'slug' => 'inactive'])
        ->and(Status::find(Status::USER_ACTIVE)->only(['group', 'slug']))->toBe(['group' => 'user', 'slug' => 'active']);
});

it('does not duplicate statuses when run again', function () {
    $this->seed(StatusSeeder::class);
    $count = Status::count();

    $this->seed(StatusSeeder::class);

    expect(Status::count())->toBe($count);
});

it('links a user to their status', function () {
    $user = User::forceCreate(['phone' => '9876500000', 'status_id' => Status::USER_INACTIVE]);

    expect($user->status->slug)->toBe('inactive');
});
