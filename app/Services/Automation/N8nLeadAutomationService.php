<?php

namespace App\Services\Automation;

use App\Models\Lead;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class N8nLeadAutomationService
{
    /**
     * Send a lead to the n8n automation webhook.
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    public function send(Lead $lead): void
    {
        $webhookUrl = config('services.n8n.webhook_url');
        $secret = config('services.n8n.webhook_secret');

        if (! $webhookUrl) {
            throw new RuntimeException(
                'N8N webhook URL is not configured.'
            );
        }

        $idempotencyKey = "lead:{$lead->id}:automation";

        $payload = [
            'event' => 'lead.created',

            'lead' => [
                'id' => $lead->id,
                'name' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'company' => $lead->company,
                'message' => $lead->message,
                'source' => $lead->source,
                'created_at' => $lead->created_at?->toISOString(),
            ],
        ];

        $response = Http::asJson()
            ->acceptJson()
            ->timeout(10)
            ->connectTimeout(5)
            ->withHeaders([
                'X-Automation-Secret' => $secret,
                'Idempotency-Key' => $idempotencyKey,
            ])
            ->post($webhookUrl, $payload);

        if ($response->failed()) {
            Log::warning('n8n automation returned an error.', [
                'lead_id' => $lead->id,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            $response->throw();
        }

        Log::info('Lead successfully sent to n8n.', [
            'lead_id' => $lead->id,
            'status' => $response->status(),
        ]);
    }
}
