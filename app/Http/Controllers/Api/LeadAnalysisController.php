<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadAnalysisRequest;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;

class LeadAnalysisController extends Controller
{
    public function store(StoreLeadAnalysisRequest $request, Lead $lead): JsonResponse
    {
        $analysis = $lead->analysis()->updateOrCreate(
            ['lead_id' => $lead->id],
            $request->validated()
        );

        if ($lead->status === 'new') {
            $lead->update([
                'status' => 'qualified',
            ]);
        }

        return response()->json([
            'message' => 'Lead analysis saved successfully.',
            'data' => [
                'lead_id' => $lead->id,
                'lead_status' => $lead->status,
                'analysis' => $analysis,
            ],
        ], 200);
    }
}
