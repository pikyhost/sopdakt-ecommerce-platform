<?php

namespace App\Jobs;

use App\Services\WAPilotWhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, Queueable;

    protected $phone;
    protected $message;

    public function __construct(string $phone, string $message)
    {
        $this->phone = $phone;
        $this->message = $message;
    }

    public function handle(WAPilotWhatsAppService $whatsAppService)
    {
        $whatsAppService->sendMessage($this->phone, $this->message);
    }
}
