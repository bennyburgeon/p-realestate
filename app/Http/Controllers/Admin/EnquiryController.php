<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\Status;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnquiryController extends Controller
{
    public function index(Request $request): View
    {
        $enquiries = Enquiry::query()
            ->with(['property', 'status'])
            ->when($request->filled('status'), fn ($q) => $q->where('status_id', $request->integer('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $statuses = Status::where('group', 'enquiry')->orderBy('sort_order')->get();

        return view('admin.enquiries.index', compact('enquiries', 'statuses'));
    }

    public function updateStatus(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $request->validate(['status_id' => ['required', 'integer', 'exists:statuses,id']]);

        $enquiry->update(['status_id' => $request->integer('status_id')]);

        return back()->with('status', 'Enquiry status updated.');
    }
}
