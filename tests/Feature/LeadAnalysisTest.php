<?php

use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

uses(RefreshDatabase::class);

test('it stores lead analysis successfully and updates lead status to qualified', function () {
    Config::set('services.n8n.webhook_secret', 'secret-key-test');

    $lead = Lead::create([
        'name' => 'Farah',
        'email' => 'farah@example.com',
        'company' => 'Design Studio',
        'message' => 'Need an AI workflow setup by next week.',
        'source' => 'website',
        'status' => 'new',
    ]);

    $payload = [
        'intent' => 'ai_integration',
        'urgency' => 'high',
        'lead_score' => 94,
        'summary' => 'Client needs an AI workflow integration next week.',
        'recommended_action' => 'Call lead within 24 hours.',
        'confidence' => 0.965,
        'raw_response' => [
            'classification' => 'HOT',
            'model' => 'gemini-3.5-flash-lite',
        ],
    ];

    $response = $this->withHeaders([
        'X-Automation-Secret' => 'secret-key-test',
    ])->postJson("/api/leads/{$lead->id}/analysis", $payload);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Lead analysis saved successfully.',
            'data' => [
                'lead_id' => $lead->id,
                'lead_status' => 'qualified',
                'analysis' => [
                    'intent' => 'ai_integration',
                    'urgency' => 'high',
                    'lead_score' => 94,
                ],
            ],
        ]);

    $this->assertDatabaseHas('lead_analyses', [
        'lead_id' => $lead->id,
        'intent' => 'ai_integration',
        'urgency' => 'high',
        'lead_score' => 94,
    ]);

    expect($lead->fresh()->status)->toBe('qualified');
});

test('it rejects lead analysis request without valid automation secret', function () {
    Config::set('services.n8n.webhook_secret', 'expected-secret');

    $lead = Lead::create([
        'name' => 'Tariq',
        'message' => 'Looking for pricing.',
    ]);

    $responseNoSecret = $this->postJson("/api/leads/{$lead->id}/analysis", [
        'intent' => 'ecommerce',
        'urgency' => 'low',
        'lead_score' => 40,
        'summary' => 'Basic inquiry.',
        'recommended_action' => 'Send email.',
    ]);

    $responseNoSecret->assertStatus(401);

    $responseInvalidSecret = $this->withHeaders([
        'X-Automation-Secret' => 'wrong-secret',
    ])->postJson("/api/leads/{$lead->id}/analysis", [
        'intent' => 'ecommerce',
        'urgency' => 'low',
        'lead_score' => 40,
        'summary' => 'Basic inquiry.',
        'recommended_action' => 'Send email.',
    ]);

    $responseInvalidSecret->assertStatus(401);
});

test('it validates analysis payload properly', function () {
    Config::set('services.n8n.webhook_secret', 'valid-secret');

    $lead = Lead::create([
        'name' => 'Rana',
        'message' => 'Inquiry regarding services.',
    ]);

    $response = $this->withHeaders([
        'X-Automation-Secret' => 'valid-secret',
    ])->postJson("/api/leads/{$lead->id}/analysis", [
        'intent' => 'invalid_intent_value',
        'urgency' => 'critical', // not in low,medium,high
        'lead_score' => 150, // max is 100
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['intent', 'urgency', 'lead_score', 'summary', 'recommended_action']);
});

test('it allows retrieving a lead with its analysis', function () {
    $lead = Lead::create([
        'name' => 'Salma',
        'message' => 'Need website redesign.',
        'status' => 'new',
    ]);

    $lead->analysis()->create([
        'intent' => 'website_development',
        'urgency' => 'medium',
        'lead_score' => 75,
        'summary' => 'Wants a modern redesign.',
        'recommended_action' => 'Send portfolio.',
        'confidence' => 0.88,
    ]);

    $response = $this->getJson("/api/leads/{$lead->id}");

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $lead->id)
        ->assertJsonPath('data.analysis.intent', 'website_development')
        ->assertJsonPath('data.analysis.lead_score', 75);
});
