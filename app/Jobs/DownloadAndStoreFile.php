<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
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
        string $id,
        string $filename,
    ) {
        $this->path = "$id-$filename";
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

        ray($this->path);

        Storage::put($this->path, $file);
    }
}
