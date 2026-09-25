<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\PropertyRequirement;
use App\Models\Status;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'total_properties' => Property::count(),
            'published_properties' => Property::whereIn('status_id', Status::publicPropertyStatuses())->count(),
            'pending_approvals' => Property::where('status_id', Status::PROPERTY_PENDING_REVIEW)->count(),
            'sold_properties' => Property::where('status_id', Status::PROPERTY_SOLD)->count(),
            'rented_properties' => Property::where('status_id', Status::PROPERTY_RENTED)->count(),
            'total_users' => User::count(),
            'new_users_7d' => User::where('created_at', '>=', now()->subDays(7))->count(),
            'active_requirements' => PropertyRequirement::where('status_id', Status::REQUIREMENT_ACTIVE)->count(),
            'unresolved_enquiries' => Enquiry::where('status_id', Status::ENQUIRY_NEW)->count(),
        ];

        $categoryDistribution = PropertyCategory::withCount('properties')->orderByDesc('properties_count')->take(6)->get();

        $topLocations = Property::query()
            ->selectRaw('location_id, count(*) as total')
            ->groupBy('location_id')
            ->orderByDesc('total')
            ->with('location')
            ->take(6)
            ->get();

        $recentProperties = Property::with(['owner', 'status'])->latest()->take(6)->get();
        $recentRequirements = PropertyRequirement::with(['user', 'status'])->latest()->take(6)->get();

        return view('admin.dashboard', compact('stats', 'categoryDistribution', 'topLocations', 'recentProperties', 'recentRequirements'));
    }
}
