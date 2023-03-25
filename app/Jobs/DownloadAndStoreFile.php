<?php

namespace App\Jobs;

use App\Models\Asset;
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
        private readonly string $id,
        private readonly string $filename,
        private readonly int $size,
        private readonly string $base_id,
        private readonly string $table_id,
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

        Storage::put($this->path, $file);

        // Store the file metadata
        Asset::updateOrCreate(
            [
                'id' => $this->id,
            ],
            [
                'filename' => $this->filename,
                'size' => $this->size,
                'path' => $this->path,
                'base_id' => $this->base_id,
                'table_id' => $this->table_id,
            ]
        );
    }
}
