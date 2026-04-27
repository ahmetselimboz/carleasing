<?php

namespace App\Jobs;

use App\Services\LeadMailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendLeadCreatedEmailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public string $type,
        public int $id,
    ) {
    }

    public function handle(LeadMailService $leadMailService): void
    {
        $leadMailService->handleCreatedLead($this->type, $this->id);
    }
}
