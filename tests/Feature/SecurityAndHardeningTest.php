<?php

use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('security headers are present on all web and api responses', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    $response->assertHeader('X-XSS-Protection', '1; mode=block');
    $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
});

test('rate limiting throttles abusive lead submissions', function () {
    Http::fake();

    // 15 requests allowed per minute on lead-intake
    for ($i = 0; $i < 15; $i++) {
        $this->postJson('/api/leads', [
            'name' => "User {$i}",
            'message' => 'Legitimate inquiry message.',
        ])->assertStatus(201);
    }

    // 16th request should be throttled (HTTP 429)
    $response = $this->postJson('/api/leads', [
        'name' => 'Abusive Spammer',
        'message' => 'Spam flood message.',
    ]);

    $response->assertStatus(429);
});

test('input validation rejects token bombing and oversized payloads', function () {
    $hugeMessage = str_repeat('A', 5001); // Exceeds max:5000

    $response = $this->postJson('/api/leads', [
        'name' => 'Oversized Attacker',
        'message' => $hugeMessage,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['message']);
});

test('it handles potential XSS payloads safely without corrupting data', function () {
    Http::fake();

    $xssPayload = '<script>alert("XSS")</script><img src=x onerror=alert(1)>';

    $response = $this->postJson('/api/leads', [
        'name' => 'Malicious Tester',
        'company' => $xssPayload,
        'message' => 'Hello, I need assistance with ' . $xssPayload,
    ]);

    $response->assertStatus(201);
    $leadId = $response->json('data.id');

    $lead = Lead::find($leadId);
    expect($lead->company)->toBe($xssPayload);

    // Ensure it renders safely on dashboard view (Blade auto-escapes HTML)
    $dashboardResponse = $this->get('/dashboard');
    $dashboardResponse->assertStatus(200);
    $dashboardResponse->assertDontSee('<script>alert("XSS")</script>', false);
    $dashboardResponse->assertSee('&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', false);
});

test('analysis callback does not demote contacted or converted leads back to qualified', function () {
    Config::set('services.n8n.webhook_secret', 'valid-secret');

    $lead = Lead::create([
        'name' => 'Ahmed',
        'message' => 'Urgent requirements.',
        'status' => 'contacted', // Already contacted by sales agent
    ]);

    $response = $this->withHeaders([
        'X-Automation-Secret' => 'valid-secret',
    ])->postJson("/api/leads/{$lead->id}/analysis", [
        'intent' => 'website_development',
        'urgency' => 'high',
        'lead_score' => 95,
        'summary' => 'Urgent website build.',
        'recommended_action' => 'Follow up.',
    ]);

    $response->assertStatus(200);
    expect($lead->fresh()->status)->toBe('contacted');
});

test('it rejects unauthorized or forged callback requests', function () {
    Config::set('services.n8n.webhook_secret', 'real-secret-123');

    $lead = Lead::create([
        'name' => 'Sara',
        'message' => 'Inquiry details.',
        'status' => 'new',
    ]);

    // Missing header
    $this->postJson("/api/leads/{$lead->id}/analysis", [
        'intent' => 'ecommerce',
        'urgency' => 'low',
        'lead_score' => 50,
        'summary' => 'Summary.',
        'recommended_action' => 'Action.',
    ])->assertStatus(401);

    // Tampered header
    $this->withHeaders([
        'X-Automation-Secret' => 'forged-secret',
    ])->postJson("/api/leads/{$lead->id}/analysis", [
        'intent' => 'ecommerce',
        'urgency' => 'low',
        'lead_score' => 50,
        'summary' => 'Summary.',
        'recommended_action' => 'Action.',
    ])->assertStatus(401);
});
