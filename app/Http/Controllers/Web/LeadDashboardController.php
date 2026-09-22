<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadDashboardController extends Controller
{
    /**
     * Show the public customer lead submission form.
     */
    public function create(): View
    {
        return view('leads.create');
    }

    /**
     * Show the supervisor triage dashboard.
     */
    public function index(Request $request): View
    {
        $query = Lead::with('analysis')->latest();

        // Optional filtering by classification or urgency
        if ($request->filled('filter')) {
            $filter = $request->get('filter');
            if ($filter === 'hot') {
                $query->whereHas('analysis', function ($q) {
                    $q->where('lead_score', '>=', 80);
                });
            } elseif ($filter === 'warm') {
                $query->whereHas('analysis', function ($q) {
                    $q->whereBetween('lead_score', [50, 79]);
                });
            } elseif ($filter === 'cold') {
                $query->whereHas('analysis', function ($q) {
                    $q->where('lead_score', '<', 50);
                });
            } elseif ($filter === 'pending') {
                $query->where('status', 'new')->whereDoesntHave('analysis');
            }
        }

        $leads = $query->paginate(20)->withQueryString();

        // High-level operational metrics
        $allLeads = Lead::with('analysis')->get();
        $metrics = [
            'total' => $allLeads->count(),
            'qualified' => $allLeads->where('status', 'qualified')->count(),
            'hot' => $allLeads->filter(fn ($l) => optional($l->analysis)->lead_score >= 80 || optional($l->analysis)->urgency === 'high')->count(),
            'pending' => $allLeads->where('automation_status', '!=', 'processed')->count(),
        ];

        return view('leads.index', compact('leads', 'metrics'));
    }

    /**
     * Update lead business status (e.g. from qualified to contacted or converted).
     */
    public function updateStatus(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:new,qualified,contacted,converted,rejected',
        ]);

        $lead->update(['status' => $validated['status']]);

        return back()->with('success', 'تم تحديث حالة الطلب بنجاح.');
    }
}
