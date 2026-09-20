<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Services\Automation\N8nLeadAutomationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessLeadAutomation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [10, 30, 90];

    public function __construct(
        public int $leadId
    ) {
    }

    public function handle(
        N8nLeadAutomationService $automationService
    ): void {
        $lead = Lead::find($this->leadId);

        if (! $lead) {
            Log::warning('Lead not found while processing automation.', [
                'lead_id' => $this->leadId,
            ]);

            return;
        }

        /*
         * Prevent duplicate processing after successful delivery.
         */
        if ($lead->automation_status === 'processed') {
            return;
        }

        $lead->update([
            'automation_status' => 'processing',
            'automation_attempts' => $lead->automation_attempts + 1,
            'automation_last_attempted_at' => now(),
        ]);

        try {
            $automationService->send($lead);

            $lead->update([
                'automation_status' => 'processed',
                'automation_processed_at' => now(),
            ]);
        } catch (Throwable $exception) {
            Log::error('Lead automation failed.', [
                'lead_id' => $lead->id,
                'attempt' => $this->attempts(),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function failed(Throwable $exception): void
    {
        $lead = Lead::find($this->leadId);

        if (! $lead) {
            return;
        }

        $lead->update([
            'automation_status' => 'failed',
        ]);

        Log::error('Lead automation permanently failed.', [
            'lead_id' => $lead->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
