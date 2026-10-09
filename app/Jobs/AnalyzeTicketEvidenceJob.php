<?php

namespace App\Jobs;

use App\Models\ReturnTicket;
use App\Services\AiVisionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AnalyzeTicketEvidenceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ReturnTicket $ticket
    ) {}

    public function handle(AiVisionService $visionService): void
    {
        $visionService->analyzeTicket($this->ticket);
    }
}
