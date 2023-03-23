<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use \TANIOS\Airtable\Airtable;

class AirtableController extends Controller
{
    private const API_ENDPOINT = 'https://api.airtable.com/v0';

    public function __construct(
        private readonly string $token,
    ) {
    }

    public function listBases(): array
    {
        $response = Http::withToken($this->token)
            ->get(self::API_ENDPOINT . '/meta/bases');

        if ($response->failed()) {
            Log::error('Failed to get bases', [
                'response' => $response->json(),
            ]);

            return [];
        }

        return $response->json()['bases'];
    }

    public function listTables(string $base_id): array
    {
        $response = Http::withToken($this->token)
            ->get(self::API_ENDPOINT . "/meta/bases/{$base_id}/tables");

        if ($response->failed()) {
            Log::error('Failed to get tables', [
                'response' => $response->json(),
            ]);

            return [];
        }

        return $response->json()['tables'];
    }

    // TODO: Work out how we're going to do the $page variable.
    public function getRecords(string $base_id, string $table_id, string|null $view_id = null, int $page = 1, int $per_page = 100, string|null $filter = null): array
    {
        $params = [
            "maxRecords" => 100,
            "pageSize" => $per_page,
            "view" => $view_id
        ];

        if ($filter) {
            $params['filterByFormula'] = $filter;
        }

        $airtable = new Airtable([
            'api_key' => $this->token,
            'base'    => $base_id
        ]);

        $records = [];
        $request = $airtable->getContent($table_id, $params);

        do {
            $response = $request->getResponse();
            $records = array_merge($records, $response->records);
        } while ($request = $response->next());

        $records = array_map(function ($record) {
            return $record->fields;
        }, $records);

        return $records;
    }
}
