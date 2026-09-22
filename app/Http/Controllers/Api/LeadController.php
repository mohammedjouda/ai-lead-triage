<?php

namespace App\Http\Controllers\Api;

use App\Events\LeadCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadRequest;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;

class LeadController extends Controller
{
    public function index(): JsonResponse
    {
        $leads = Lead::with('analysis')->latest()->paginate(20);

        return response()->json($leads);
    }

    public function show(Lead $lead): JsonResponse
    {
        return response()->json([
            'data' => $lead->load('analysis'),
        ]);
    }

    public function store(StoreLeadRequest $request): JsonResponse
    {
        $lead = Lead::create([
            ...$request->validated(),
            'source' => $request->validated('source') ?? 'website',
            'status' => 'new',
        ]);

        event(new LeadCreated($lead));

        return response()->json([
            'message' => 'Lead created successfully.',
            'data' => [
                'id' => $lead->id,
                'status' => $lead->status,
            ],
        ], 201);
    }
}
