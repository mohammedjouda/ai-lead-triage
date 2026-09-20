<?php

namespace App\Listeners;

use App\Events\LeadCreated;
use App\Jobs\ProcessLeadAutomation;

class DispatchLeadAutomation
{
    public function handle(LeadCreated $event): void
    {
        ProcessLeadAutomation::dispatch(
            $event->lead->id
        );
    }
}
