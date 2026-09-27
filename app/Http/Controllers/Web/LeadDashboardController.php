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

        // High-level operational metrics calculated at database level
        $metrics = [
            'total' => Lead::count(),
            'qualified' => Lead::where('status', 'qualified')->count(),
            'hot' => Lead::whereHas('analysis', function ($q) {
                $q->where('lead_score', '>=', 80)->orWhere('urgency', 'high');
            })->count(),
            'pending' => Lead::where('automation_status', '!=', 'processed')->count(),
        ];

        return view('leads.index', compact('leads', 'metrics'));
    }

    /**
     * Store a lead from the web form submission.
     */
    public function store(\App\Http\Requests\StoreLeadRequest $request)
    {
        $lead = Lead::create([
            ...$request->validated(),
            'source' => $request->validated('source') ?? 'website',
            'status' => 'new',
        ]);

        event(new \App\Events\LeadCreated($lead));

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'تم استلام طلبكم بنجاح.',
                'data' => [
                    'id' => $lead->id,
                    'status' => $lead->status,
                ],
            ], 201);
        }

        return redirect()->route('leads.dashboard')->with('success', "تم تسجيل الطلب برقم #{$lead->id} وتوجيهه للفرز الآلي بنجاح.");
    }

    /**
     * Retry sending lead to n8n automation pipeline.
     */
    public function retry(Lead $lead, \App\Services\Automation\N8nLeadAutomationService $automationService)
    {
        try {
            $automationService->send($lead);

            $lead->update([
                'automation_status' => 'processed',
                'automation_processed_at' => now(),
                'automation_attempts' => $lead->automation_attempts + 1,
                'automation_last_attempted_at' => now(),
            ]);

            return back()->with('success', "تم إرسال الطلب رقم #{$lead->id} بنجاح إلى n8n.");
        } catch (\Throwable $e) {
            $lead->update([
                'automation_status' => 'failed',
                'automation_attempts' => $lead->automation_attempts + 1,
                'automation_last_attempted_at' => now(),
            ]);

            return back()->with('error', "فشل إرسال الطلب رقم #{$lead->id} إلى n8n: {$e->getMessage()}");
        }
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
