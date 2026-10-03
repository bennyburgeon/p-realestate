<?php

namespace App\Livewire;

use App\Models\Amenity;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Services\PropertyService;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;

class SellPropertyWizard extends Component
{
    use WithFileUploads;

    public int $step = 1;

    public const int TOTAL_STEPS = 8;

    public ?string $propertyId = null;

    private const array LAND_CATEGORY_SLUGS = ['plot-land', 'commercial-land'];

    // Step 1: Purpose / type
    public string $listing_type = 'sale';

    public string $property_nature = 'residential';

    public ?string $property_category_id = null;

    // Step 2: Location
    public ?string $location_id = null;

    public ?string $address = null;

    public ?string $landmark = null;

    public ?string $pin_code = null;

    public ?string $latitude = null;

    public ?string $longitude = null;

    public bool $address_visibility = true;

    // Step 3: Details
    public ?int $bedrooms = null;

    public ?int $bathrooms = null;

    public ?int $balconies = null;

    public ?string $built_up_area = null;

    public ?string $carpet_area = null;

    public ?string $plot_area = null;

    public string $area_unit = 'sqft';

    public ?string $furnishing_status = null;

    public ?string $parking_details = null;

    public ?string $facing = null;

    public ?int $floor_number = null;

    public ?int $total_floors = null;

    public ?int $construction_year = null;

    public ?string $possession_status = null;

    public ?string $availability_date = null;

    /** @var array<int, string> */
    public array $amenity_ids = [];

    // Step 4: Price
    public ?string $price = null;

    public ?string $rent_amount = null;

    public ?string $security_deposit = null;

    public ?string $maintenance_amount = null;

    public bool $is_negotiable = false;

    public ?string $price_per_sqft = null;

    public ?string $min_acceptable_price = null;

    // Step 5: Photos + documents
    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $photos = [];

    public int $coverIndex = 0;

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $documents = [];

    // Step 6: Owner / agent
    public string $contact_preference = 'phone';

    public string $contact_phone = '';

    public ?string $contact_email = null;

    public bool $isAgentListing = false;

    public ?string $agent_name = null;

    public ?string $agent_phone = null;

    public ?string $agent_email = null;

    public ?string $agency_name = null;

    public ?string $agent_license_number = null;

    // Step 7: Description
    public string $title = '';

    public ?string $description = null;

    public function mount(?Property $property = null): void
    {
        $user = auth()->user();
        $this->contact_phone = $user->phone ?? '';
        $this->contact_email = $user->email;

        if ($property && $property->exists) {
            abort_unless(auth()->id() === $property->user_id, 403);

            $this->propertyId = $property->id;
            $this->fill($property->only([
                'listing_type', 'property_nature', 'property_category_id',
                'location_id', 'address', 'landmark', 'pin_code', 'latitude', 'longitude', 'address_visibility',
                'bedrooms', 'bathrooms', 'balconies', 'built_up_area', 'carpet_area', 'plot_area', 'area_unit',
                'furnishing_status', 'parking_details', 'facing', 'floor_number', 'total_floors',
                'construction_year', 'possession_status', 'availability_date',
                'price', 'rent_amount', 'security_deposit', 'maintenance_amount', 'is_negotiable',
                'price_per_sqft', 'min_acceptable_price',
                'contact_preference', 'contact_phone', 'contact_email',
                'agent_name', 'agent_phone', 'agent_email', 'agency_name', 'agent_license_number',
                'title', 'description',
            ]));
            $this->amenity_ids = $property->amenities->pluck('id')->all();
            $this->isAgentListing = (bool) $property->agent_name;
        }
    }

    public function categories(): Collection
    {
        return PropertyCategory::where('nature', $this->property_nature)->orderBy('sort_order')->get();
    }

    public function cities(): Collection
    {
        return Location::where('type', Location::TYPE_CITY)->orderBy('name')->get();
    }

    public function amenities(): Collection
    {
        return Amenity::orderBy('sort_order')->get();
    }

    public function isLandCategory(): bool
    {
        if (! $this->property_category_id) {
            return false;
        }

        $category = PropertyCategory::find($this->property_category_id);

        return $category && in_array($category->slug, self::LAND_CATEGORY_SLUGS, true);
    }

    public function useMyLocation(float $lat, float $lng): void
    {
        $this->latitude = (string) $lat;
        $this->longitude = (string) $lng;
    }

    public function updatedBuiltUpArea(): void
    {
        $this->recalculatePricePerSqft();
    }

    public function updatedPrice(): void
    {
        $this->recalculatePricePerSqft();
    }

    private function recalculatePricePerSqft(): void
    {
        if ($this->price && $this->built_up_area && (float) $this->built_up_area > 0) {
            $this->price_per_sqft = number_format((float) $this->price / (float) $this->built_up_area, 2, '.', '');
        }
    }

    public function moveCoverTo(int $index): void
    {
        if (isset($this->photos[$index])) {
            $this->coverIndex = $index;
        }
    }

    public function removePhoto(int $index): void
    {
        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);
        $this->coverIndex = 0;
    }

    public function removeDocument(int $index): void
    {
        unset($this->documents[$index]);
        $this->documents = array_values($this->documents);
    }

    /**
     * @return array<string, mixed>
     */
    private function rulesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'listing_type' => ['required', 'in:sale,rent,lease'],
                'property_nature' => ['required', 'in:residential,commercial'],
                'property_category_id' => ['required', 'exists:property_categories,id'],
            ],
            2 => [
                'location_id' => ['required', 'exists:locations,id'],
                'address' => ['required', 'string', 'max:500'],
                'landmark' => ['nullable', 'string', 'max:150'],
                'pin_code' => ['nullable', 'string', 'max:10'],
                'latitude' => ['nullable', 'numeric', 'between:-90,90'],
                'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            ],
            3 => [
                'bedrooms' => ['nullable', 'integer', 'min:0', 'max:20'],
                'bathrooms' => ['nullable', 'integer', 'min:0', 'max:20'],
                'balconies' => ['nullable', 'integer', 'min:0', 'max:20'],
                'built_up_area' => ['nullable', 'numeric', 'min:0'],
                'carpet_area' => ['nullable', 'numeric', 'min:0'],
                'plot_area' => ['nullable', 'numeric', 'min:0'],
                'floor_number' => ['nullable', 'integer', 'min:0'],
                'total_floors' => ['nullable', 'integer', 'min:0'],
                'construction_year' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 5)],
            ],
            4 => [
                'price' => ['nullable', 'numeric', 'min:0', 'required_if:listing_type,sale'],
                'rent_amount' => ['nullable', 'numeric', 'min:0', 'required_if:listing_type,rent,lease'],
                'security_deposit' => ['nullable', 'numeric', 'min:0'],
                'maintenance_amount' => ['nullable', 'numeric', 'min:0'],
                'min_acceptable_price' => ['nullable', 'numeric', 'min:0'],
            ],
            5 => $this->propertyId ? [] : [
                'photos' => ['required', 'array', 'min:3'],
                'photos.*' => ['image', 'max:5120'],
                'documents.*' => ['nullable', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            ],
            6 => [
                'contact_phone' => ['required', 'string', 'max:20'],
                'contact_email' => ['nullable', 'email', 'max:150'],
                'agent_name' => ['required_if:isAgentListing,true', 'nullable', 'string', 'max:150'],
                'agent_phone' => ['required_if:isAgentListing,true', 'nullable', 'string', 'max:20'],
            ],
            7 => [
                'title' => ['required', 'string', 'min:10', 'max:150'],
                'description' => ['nullable', 'string', 'max:5000'],
            ],
            default => [],
        };
    }

    public function nextStep(): void
    {
        $this->validate($this->rulesForStep($this->step));
        $this->saveDraft();
        $this->step = min($this->step + 1, self::TOTAL_STEPS);
    }

    public function previousStep(): void
    {
        $this->step = max($this->step - 1, 1);
    }

    public function goToStep(int $step): void
    {
        if ($step < $this->step) {
            $this->step = $step;
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(): array
    {
        return [
            'listing_type' => $this->listing_type,
            'property_nature' => $this->property_nature,
            'property_category_id' => $this->property_category_id,
            'location_id' => $this->location_id,
            'address' => $this->address,
            'landmark' => $this->landmark,
            'pin_code' => $this->pin_code,
            'latitude' => $this->latitude ?: null,
            'longitude' => $this->longitude ?: null,
            'address_visibility' => $this->address_visibility,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'balconies' => $this->balconies,
            'built_up_area' => $this->built_up_area ?: null,
            'carpet_area' => $this->carpet_area ?: null,
            'plot_area' => $this->plot_area ?: null,
            'area_unit' => $this->area_unit,
            'furnishing_status' => $this->furnishing_status,
            'parking_details' => $this->parking_details,
            'facing' => $this->facing,
            'floor_number' => $this->floor_number,
            'total_floors' => $this->total_floors,
            'construction_year' => $this->construction_year,
            'possession_status' => $this->possession_status,
            'availability_date' => $this->availability_date ?: null,
            'amenity_ids' => $this->amenity_ids,
            'price' => $this->price ?: null,
            'rent_amount' => $this->rent_amount ?: null,
            'security_deposit' => $this->security_deposit ?: null,
            'maintenance_amount' => $this->maintenance_amount ?: null,
            'is_negotiable' => $this->is_negotiable,
            'price_per_sqft' => $this->price_per_sqft ?: null,
            'min_acceptable_price' => $this->min_acceptable_price ?: null,
            'contact_preference' => $this->contact_preference,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email ?: null,
            'agent_name' => $this->isAgentListing ? $this->agent_name : null,
            'agent_phone' => $this->isAgentListing ? $this->agent_phone : null,
            'agent_email' => $this->isAgentListing ? $this->agent_email : null,
            'agency_name' => $this->isAgentListing ? $this->agency_name : null,
            'agent_license_number' => $this->isAgentListing ? $this->agent_license_number : null,
            'title' => $this->title,
            'description' => $this->description,
        ];
    }

    private function saveDraft(): void
    {
        $service = app(PropertyService::class);

        if ($this->propertyId) {
            $service->update(Property::find($this->propertyId), $this->payload());

            return;
        }

        // A draft needs at least a title, category, and location to be meaningful —
        // title is only collected on step 7, so the draft row is created once the
        // user reaches that point rather than from step 1 (mirrors the requirement
        // wizard's "enough fields present" gate, just reached later in this flow).
        if ($this->title && $this->property_category_id && $this->location_id) {
            $property = $service->create(auth()->user(), $this->payload());
            $this->propertyId = $property->id;
        }
    }

    public function submit(PropertyService $service): void
    {
        $this->validate([
            ...$this->rulesForStep(1),
            ...$this->rulesForStep(2),
            ...$this->rulesForStep(3),
            ...$this->rulesForStep(4),
            ...$this->rulesForStep(5),
            ...$this->rulesForStep(6),
            ...$this->rulesForStep(7),
        ]);

        $property = $this->propertyId
            ? $service->update(Property::find($this->propertyId), $this->payload())
            : $service->create(auth()->user(), $this->payload());

        foreach ($this->photos as $index => $upload) {
            $media = $property->addMedia($upload->getRealPath())
                ->usingFileName($upload->getClientOriginalName())
                ->toMediaCollection('images');

            if ($index === $this->coverIndex) {
                $media->setCustomProperty('is_cover', true)->save();
            }
        }

        foreach ($this->documents as $upload) {
            $property->addMedia($upload->getRealPath())
                ->usingFileName($upload->getClientOriginalName())
                ->toMediaCollection('documents');
        }

        $service->submitForReview($property);

        session()->flash('status', 'Your property has been submitted and is pending admin review.');

        $this->redirect(route('properties.show', $property), navigate: false);
    }

    public function render()
    {
        return view('livewire.sell-property-wizard', [
            'categories' => $this->categories(),
            'cities' => $this->cities(),
            'amenitiesList' => $this->amenities(),
        ]);
    }
}
