<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class DownloadAndStoreFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private readonly string $path;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly string $url,
        private readonly string $id,
        private readonly string $filename,
        private readonly int $size,
        private readonly string $user_uuid,
        private readonly string $base_id,
        private readonly string $table_id,
    ) {
        $this->path = "$user_uuid/$id-$filename"; // e.g. 1234/1234-foo.png
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Check if the file already exists
        if (Storage::exists($this->path)) {
            return;
        }

        // Download the file
        $file = file_get_contents($this->url);

        // If the file is empty, return
        if ($file === false) {
            return;
        }

        // Store the file
        Storage::put($this->path, $file);
    }
}
