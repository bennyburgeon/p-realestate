<?php

namespace App\Livewire;

use App\Models\Amenity;
use App\Models\Location;
use App\Models\PropertyCategory;
use App\Models\PropertyRequirement;
use App\Services\PropertyRequirementService;
use Illuminate\Support\Collection;
use Livewire\Component;

class PostRequirementWizard extends Component
{
    public int $step = 1;

    public const int TOTAL_STEPS = 4;

    public ?string $requirementId = null;

    // Step 1: basics
    public string $title = '';

    public string $intent = 'buy';

    public string $property_nature = 'residential';

    public ?string $property_category_id = null;

    public ?string $city_id = null;

    /** @var array<int, string> */
    public array $preferred_locality_ids = [];

    public ?string $budget_min = null;

    public ?string $budget_max = null;

    public ?string $area_min = null;

    public ?string $area_max = null;

    public string $area_unit = 'sqft';

    // Step 2: preferences
    public bool $showAdvanced = false;

    public ?int $bedrooms = null;

    public ?int $bathrooms = null;

    public ?string $furnishing_status = null;

    public bool $parking_required = false;

    public ?string $floor_preference = null;

    public ?string $facing_preference = null;

    public ?string $property_age = null;

    public ?string $possession_requirement = null;

    public ?string $move_in_date = null;

    /** @var array<int, string> */
    public array $amenity_ids = [];

    public ?string $special_requirements = null;

    public ?string $additional_notes = null;

    // Step 3: contact
    public string $contact_name = '';

    public ?string $contact_email = null;

    public string $contact_phone = '';

    public ?string $preferred_contact_method = 'phone';

    public ?string $preferred_contact_time = null;

    public function mount(?PropertyRequirement $propertyRequirement = null): void
    {
        $user = auth()->user();
        $this->contact_name = $user->name;
        $this->contact_email = $user->email;
        $this->contact_phone = $user->phone ?? '';

        if ($propertyRequirement && $propertyRequirement->exists) {
            abort_unless(auth()->id() === $propertyRequirement->user_id, 403);

            $this->requirementId = $propertyRequirement->id;
            $this->fill($propertyRequirement->only([
                'title', 'intent', 'property_nature', 'property_category_id', 'city_id',
                'budget_min', 'budget_max', 'area_min', 'area_max', 'area_unit',
                'bedrooms', 'bathrooms', 'furnishing_status', 'parking_required',
                'floor_preference', 'facing_preference', 'property_age', 'possession_requirement',
                'move_in_date', 'special_requirements', 'additional_notes',
                'contact_name', 'contact_email', 'contact_phone',
                'preferred_contact_method', 'preferred_contact_time',
            ]));
            $this->preferred_locality_ids = $propertyRequirement->preferred_locality_ids ?? [];
            $this->amenity_ids = $propertyRequirement->amenity_ids ?? [];
            $this->parking_required = (bool) $propertyRequirement->parking_required;
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

    public function localities(): Collection
    {
        if (! $this->city_id) {
            return collect();
        }

        return Location::where('type', Location::TYPE_LOCALITY)->where('parent_id', $this->city_id)->orderBy('name')->get();
    }

    public function amenities(): Collection
    {
        return Amenity::orderBy('sort_order')->get();
    }

    /**
     * @return array<string, mixed>
     */
    private function rulesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'title' => ['required', 'string', 'min:10', 'max:150'],
                'intent' => ['required', 'in:buy,rent,lease'],
                'property_nature' => ['required', 'in:residential,commercial'],
                'property_category_id' => ['required', 'exists:property_categories,id'],
                'city_id' => ['required', 'exists:locations,id'],
                'budget_min' => ['nullable', 'numeric', 'min:0'],
                'budget_max' => ['nullable', 'numeric', 'min:0', 'gte:budget_min'],
                'area_min' => ['nullable', 'numeric', 'min:0'],
                'area_max' => ['nullable', 'numeric', 'min:0', 'gte:area_min'],
            ],
            2 => [
                'bedrooms' => ['nullable', 'integer', 'min:0', 'max:20'],
                'bathrooms' => ['nullable', 'integer', 'min:0', 'max:20'],
                'move_in_date' => ['nullable', 'date', 'after_or_equal:today'],
                'special_requirements' => ['nullable', 'string', 'max:1000'],
                'additional_notes' => ['nullable', 'string', 'max:1000'],
            ],
            3 => [
                'contact_name' => ['required', 'string', 'max:100'],
                'contact_email' => ['nullable', 'email', 'max:150'],
                'contact_phone' => ['required', 'string', 'max:20'],
                'preferred_contact_method' => ['nullable', 'in:phone,email,whatsapp'],
                'preferred_contact_time' => ['nullable', 'string', 'max:50'],
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
            'title' => $this->title,
            'intent' => $this->intent,
            'property_nature' => $this->property_nature,
            'property_category_id' => $this->property_category_id,
            'city_id' => $this->city_id,
            'preferred_locality_ids' => $this->preferred_locality_ids,
            'budget_min' => $this->budget_min ?: null,
            'budget_max' => $this->budget_max ?: null,
            'area_min' => $this->area_min ?: null,
            'area_max' => $this->area_max ?: null,
            'area_unit' => $this->area_unit,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'furnishing_status' => $this->furnishing_status,
            'parking_required' => $this->parking_required,
            'floor_preference' => $this->floor_preference,
            'facing_preference' => $this->facing_preference,
            'property_age' => $this->property_age,
            'possession_requirement' => $this->possession_requirement,
            'move_in_date' => $this->move_in_date ?: null,
            'amenity_ids' => $this->amenity_ids,
            'special_requirements' => $this->special_requirements,
            'additional_notes' => $this->additional_notes,
            'contact_name' => $this->contact_name,
            'contact_email' => $this->contact_email ?: null,
            'contact_phone' => $this->contact_phone,
            'preferred_contact_method' => $this->preferred_contact_method,
            'preferred_contact_time' => $this->preferred_contact_time,
        ];
    }

    private function saveDraft(): void
    {
        $service = app(PropertyRequirementService::class);

        if ($this->requirementId) {
            $service->update(PropertyRequirement::find($this->requirementId), $this->payload());

            return;
        }

        if ($this->step >= 1 && $this->title && $this->property_category_id && $this->city_id) {
            $requirement = $service->create(auth()->user(), $this->payload(), submit: false);
            $this->requirementId = $requirement->id;
        }
    }

    public function submit(PropertyRequirementService $service): void
    {
        $this->validate([
            ...$this->rulesForStep(1),
            ...$this->rulesForStep(2),
            ...$this->rulesForStep(3),
        ]);

        if ($this->requirementId) {
            $requirement = $service->update(PropertyRequirement::find($this->requirementId), $this->payload());
            $requirement = $service->submit($requirement);
        } else {
            $requirement = $service->create(auth()->user(), $this->payload(), submit: true);
        }

        session()->flash('status', 'Your requirement has been posted! Reference: '.$requirement->reference_number);

        $this->redirect(route('requirements.show', $requirement), navigate: false);
    }

    public function render()
    {
        return view('livewire.post-requirement-wizard', [
            'categories' => $this->categories(),
            'cities' => $this->cities(),
            'localities' => $this->localities(),
            'amenitiesList' => $this->amenities(),
        ]);
    }
}
