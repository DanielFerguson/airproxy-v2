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

class FetchBases implements ShouldQueue
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

            FetchTables::dispatch($airtable, $base_record);
        }

        $user->airtable_imported = true;
        $user->save();
    }
}
