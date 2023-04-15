<?php

namespace App\Models;

use App\Http\Controllers\AirtableController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'value',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function importAccount()
    {
        $airtable = new AirtableController($this->value);

        foreach ($airtable->listBases() as $base) {
            $base_record = $this->user->bases()->updateOrCreate([
                'id' => $base['id'],
            ], [
                'name' => $base['name'],
                'api_token_id' => $this->id,
            ]);

            foreach ($airtable->listTables($base['id']) as $table) {
                $base_record->tables()->updateOrCreate([
                    'id' => $table['id'],
                ], [
                    'name' => $table['name'],
                ]);
            }
        }

        $this->user->airtable_imported = true;
        $this->user->save();
    }
}
