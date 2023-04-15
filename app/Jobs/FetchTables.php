<?php

namespace App\Jobs;

use App\Http\Controllers\AirtableController;
use App\Models\Base;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class FetchTables implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $cache_lock_key;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly AirtableController $airtable,
        private readonly Base $base
    ) {
        $this->cache_lock_key = "fetch-tables-from-base:" . $this->base->id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Cache::lock($this->cache_lock_key, 1)->get(function () {
            $tables = $this->airtable->listTables($this->base->id);

            foreach ($tables as $table) {
                $this->base->tables()->updateOrCreate([
                    'id' => $table['id'],
                ], [
                    'name' => $table['name'],
                ]);
            }
        });
    }
}
