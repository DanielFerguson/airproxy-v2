<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CacheStaticFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly array $data,
        private readonly string $user_uuid,
        private readonly string $base_id,
        private readonly string $table_id,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // If the data is empty, return
        if (empty($this->data)) {
            return;
        }

        // Find the assets record
        foreach ($this->data as $record) {
            foreach ($record as $k => $v) {
                if (gettype($v) !== 'array') continue;

                foreach ($v as $k => $v) {
                    if (gettype($v) !== 'object') continue;

                    // Check whether the record has id, filename and url
                    if (!isset($v->id) || !isset($v->filename) || !isset($v->url)) continue;

                    DownloadAndStoreFile::dispatch(
                        url: $v->url,
                        id: $v->id,
                        filename: $v->filename,
                        size: $v->size,
                        user_uuid: $this->user_uuid,
                        base_id: $this->base_id,
                        table_id: $this->table_id,
                    );
                }
            }
        }
    }
}
