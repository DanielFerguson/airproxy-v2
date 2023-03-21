<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'table_id',
        'name',
        'type',
        'is_active',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
