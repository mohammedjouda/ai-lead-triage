<?php

use App\Events\LeadCreated;
use App\Jobs\ProcessLeadAutomation;
use App\Listeners\DispatchLeadAutomation;
use App\Models\Lead;
use App\Services\Automation\N8nLeadAutomationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

test('it dispatches LeadCreated event when lead is created via API', function () {
    Event::fake([LeadCreated::class]);

    $response = $this->postJson('/api/leads', [
        'name' => 'Kareem',
        'email' => 'kareem@example.com',
        'message' => 'Need an AI triage system for our sales team.',
    ]);

    $response->assertStatus(201);
    $leadId = $response->json('data.id');

    Event::assertDispatched(LeadCreated::class, function (LeadCreated $event) use ($leadId) {
        return $event->lead->id === $leadId
            && $event->lead->automation_status === 'pending';
    });
});

test('it dispatches ProcessLeadAutomation job from DispatchLeadAutomation listener', function () {
    Queue::fake();

    $lead = Lead::create([
        'name' => 'Mona',
        'email' => 'mona@example.com',
        'message' => 'Looking for workflow automation.',
        'source' => 'website',
        'status' => 'new',
    ]);

    $listener = new DispatchLeadAutomation();
    $listener->handle(new LeadCreated($lead));

    Queue::assertPushed(ProcessLeadAutomation::class, function (ProcessLeadAutomation $job) use ($lead) {
        return $job->leadId === $lead->id;
    });
});

test('ProcessLeadAutomation job successfully sends lead to n8n and updates status to processed', function () {
    Config::set('services.n8n.webhook_url', 'https://n8n.example.com/webhook/lead-triage');
    Config::set('services.n8n.webhook_secret', 'super-secret-key-123');

    Http::fake([
        'https://n8n.example.com/webhook/lead-triage' => Http::response(['received' => true], 200),
    ]);

    $lead = Lead::create([
        'name' => 'Zaid',
        'email' => 'zaid@example.com',
        'company' => 'Tech Corp',
        'message' => 'We need an e-commerce platform with AI integration.',
        'source' => 'website',
        'status' => 'new',
    ]);

    $job = new ProcessLeadAutomation($lead->id);
    $job->handle(app(N8nLeadAutomationService::class));

    $lead->refresh();

    expect($lead->automation_status)->toBe('processed')
        ->and($lead->automation_attempts)->toBe(1)
        ->and($lead->automation_last_attempted_at)->not->toBeNull()
        ->and($lead->automation_processed_at)->not->toBeNull();

    Http::assertSent(function (Request $request) use ($lead) {
        return $request->url() === 'https://n8n.example.com/webhook/lead-triage'
            && $request->header('X-Automation-Secret')[0] === 'super-secret-key-123'
            && $request->header('Idempotency-Key')[0] === "lead:{$lead->id}:automation"
            && $request['event'] === 'lead.created'
            && $request['lead']['id'] === $lead->id
            && $request['lead']['email'] === 'zaid@example.com';
    });
});

test('ProcessLeadAutomation prevents duplicate execution if already processed', function () {
    Http::fake();

    $lead = Lead::create([
        'name' => 'Huda',
        'email' => 'huda@example.com',
        'message' => 'Consulting inquiry regarding AI agents.',
        'source' => 'website',
        'status' => 'new',
        'automation_status' => 'processed',
    ]);

    $job = new ProcessLeadAutomation($lead->id);
    $job->handle(app(N8nLeadAutomationService::class));

    Http::assertNothingSent();
});

test('ProcessLeadAutomation marks automation_status as failed upon permanent failure', function () {
    $lead = Lead::create([
        'name' => 'Omar',
        'email' => 'omar@example.com',
        'message' => 'Requesting urgent support with integration.',
        'source' => 'website',
        'status' => 'new',
        'automation_status' => 'processing',
    ]);

    $job = new ProcessLeadAutomation($lead->id);
    $job->failed(new RuntimeException('Permanent webhook connection failure'));

    $lead->refresh();

    expect($lead->automation_status)->toBe('failed');
});

test('N8nLeadAutomationService throws exception when webhook url is missing', function () {
    Config::set('services.n8n.webhook_url', null);

    $lead = Lead::create([
        'name' => 'Layla',
        'message' => 'Need pricing details for migration.',
        'source' => 'website',
        'status' => 'new',
    ]);

    $service = new N8nLeadAutomationService();

    expect(fn () => $service->send($lead))
        ->toThrow(RuntimeException::class, 'N8N webhook URL is not configured.');
});
