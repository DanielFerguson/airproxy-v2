<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'requestable_type',
        'requestable_id',
        'ip_address',
        'user_agent',
        'headers',
        'country',
    ];

    protected $casts = [
        'headers' => 'array',
    ];

    public function requestable()
    {
        return $this->morphTo();
    }
}
