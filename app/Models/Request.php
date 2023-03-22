<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'base_id',
        'table_id',
        'view_id',
        'page',
        'per_page',
        'ip_address',
        'user_agent',
        'referrer',
        'headers',
        'asn',
        'country',
        'region',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
