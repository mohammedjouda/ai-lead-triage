<?php

use App\Jobs\ProcessLeadAutomation;
use App\Models\Lead;
use App\Models\LeadAnalysis;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake([ProcessLeadAutomation::class]);
});

test('it creates a lead successfully with full details', function () {
    $payload = [
        'name' => 'Ahmad',
        'email' => 'ahmad@example.com',
        'phone' => '+962791234567',
        'company' => 'ABC Store',
        'message' => 'Hi, I need an e-commerce website for my store. We are launching new products in two weeks. Can you tell me the price and timeline?',
        'source' => 'website',
    ];

    $response = $this->postJson('/api/leads', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Lead created successfully.',
            'data' => [
                'status' => 'new',
            ],
        ])
        ->assertJsonStructure([
            'message',
            'data' => ['id', 'status'],
        ]);

    $this->assertDatabaseHas('leads', [
        'name' => 'Ahmad',
        'email' => 'ahmad@example.com',
        'phone' => '+962791234567',
        'company' => 'ABC Store',
        'source' => 'website',
        'status' => 'new',
    ]);
});

test('it creates a lead with minimal details and applies defaults', function () {
    $payload = [
        'message' => 'Looking for a reliable web development service for my startup.',
    ];

    $response = $this->postJson('/api/leads', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Lead created successfully.',
            'data' => [
                'status' => 'new',
            ],
        ]);

    $this->assertDatabaseHas('leads', [
        'name' => null,
        'email' => null,
        'source' => 'website',
        'status' => 'new',
    ]);
});

test('it validates that message is required and at least 5 characters', function () {
    $responseEmpty = $this->postJson('/api/leads', []);
    $responseEmpty->assertStatus(422)
        ->assertJsonValidationErrors(['message']);

    $responseTooShort = $this->postJson('/api/leads', [
        'message' => 'Hi',
    ]);
    $responseTooShort->assertStatus(422)
        ->assertJsonValidationErrors(['message']);
});

test('it validates allowed source values', function () {
    $responseInvalidSource = $this->postJson('/api/leads', [
        'message' => 'Need consultation about my digital marketing campaign.',
        'source' => 'billboard',
    ]);

    $responseInvalidSource->assertStatus(422)
        ->assertJsonValidationErrors(['source']);

    $responseValidSource = $this->postJson('/api/leads', [
        'message' => 'Need consultation about my digital marketing campaign.',
        'source' => 'whatsapp',
    ]);

    $responseValidSource->assertStatus(201);
    $this->assertDatabaseHas('leads', [
        'source' => 'whatsapp',
    ]);
});

test('lead and lead analysis relationship operates correctly with cascade delete', function () {
    $lead = Lead::create([
        'name' => 'Sara',
        'email' => 'sara@example.com',
        'message' => 'We want to automate our customer service workflow.',
        'source' => 'api',
        'status' => 'new',
    ]);

    $analysis = LeadAnalysis::create([
        'lead_id' => $lead->id,
        'intent' => 'automation',
        'urgency' => 'high',
        'lead_score' => 95,
        'summary' => 'Client wants customer service automation.',
        'recommended_action' => 'Schedule discovery call within 24 hours.',
        'confidence' => 0.965,
        'raw_response' => [
            'intent' => 'automation',
            'score' => 95,
        ],
    ]);

    expect($lead->analysis)->not->toBeNull()
        ->and($lead->analysis->id)->toBe($analysis->id)
        ->and($analysis->lead->id)->toBe($lead->id)
        ->and($analysis->lead_score)->toBe(95)
        ->and($analysis->raw_response)->toBeArray();

    // Verify cascade on delete
    $lead->delete();

    $this->assertDatabaseMissing('leads', ['id' => $lead->id]);
    $this->assertDatabaseMissing('lead_analyses', ['id' => $analysis->id]);
});
