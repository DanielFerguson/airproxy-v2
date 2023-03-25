<?php

namespace App\Jobs;

use App\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreRequestRecord implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $type,
        public string $id,
        public string $ip_address,
        public string $user_agent,
        public array $headers,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Request::create([
            'requestable_type' => $this->type,
            'requestable_id' => $this->id,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'headers' => $this->headers,
        ]);
    }
}
