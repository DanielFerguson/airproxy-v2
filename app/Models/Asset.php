<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'base_id',
        'table_id',
        'path',
        'filename',
        'size',
    ];

    public function requests()
    {
        return $this->morphMany(Request::class, 'requestable');
    }

    public function base()
    {
        return $this->belongsTo(Base::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
