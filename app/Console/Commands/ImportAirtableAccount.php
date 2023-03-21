<?php

namespace App\Console\Commands;

use App\Http\Controllers\AirtableController;
use App\Models\ApiToken;
use App\Models\Base;
use App\Models\User;
use Illuminate\Console\Command;

class ImportAirtableAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-airtable-account {--userId=} {--apiTokenId=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import an Airtable account into the database.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $apiTokenId = $this->option('apiTokenId');
        $userId = $this->option('userId');

        if (empty($apiTokenId) || !ApiToken::where('id', $apiTokenId)->exists()) {
            $this->error('You must provide an Airtable API key.');
            return Command::FAILURE;
        }

        if (empty($userId) || !User::where('id', $userId)->exists()) {
            $this->error('You must provide a user ID.');
            return Command::FAILURE;
        }

        $token = ApiToken::findOrFail($apiTokenId);
        $user = User::findOrFail($userId);

        $this->info('Importing Airtable account...');

        $airtable = new AirtableController($token->value);

        $bases = $airtable->listBases();

        foreach ($bases as $base) {
            $this->info("Importing base {$base['name']}...");

            $base_record = $user->bases()->updateOrCreate([
                'id' => $base['id'],
            ], [
                'name' => $base['name'],
                'api_token_id' => $token->id,
            ]);

            $tables = $airtable->listTables($base['id']);

            foreach ($tables as $table) {
                $table_record = $base_record->tables()->updateOrCreate([
                    'id' => $table['id'],
                ], [
                    'name' => $table['name'],
                ]);

                $this->info("Importing table {$table['name']}...");

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

        $this->info('Done!');

        return Command::SUCCESS;
    }
}
