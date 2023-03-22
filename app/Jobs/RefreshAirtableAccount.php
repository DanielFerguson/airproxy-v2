<?php

namespace App\Jobs;

use App\Http\Controllers\AirtableController;
use App\Models\ApiToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshAirtableAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly ApiToken $token
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = $this->token->user;
        $airtable = new AirtableController($this->token->value);

        $bases = $airtable->listBases();

        foreach ($bases as $base) {
            $base_record = $user->bases()->updateOrCreate([
                'id' => $base['id'],
            ], [
                'name' => $base['name'],
                'api_token_id' => $this->token->id,
            ]);

            $tables = $airtable->listTables($base['id']);

            foreach ($tables as $table) {
                $table_record = $base_record->tables()->updateOrCreate([
                    'id' => $table['id'],
                ], [
                    'name' => $table['name'],
                ]);

                foreach ($table['views'] as $view) {
                    $view = $table_record->views()->updateOrCreate([
                        'id' => $view['id'],
                    ], [
                        'name' => $view['name'],
                        'type' => $view['type'],
                    ]);
                }
            }
        }
    }
}
