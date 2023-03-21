<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'base_id',
        'name',
        'is_active',
        'ttl',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'base_id',
    ];

    public function base()
    {
        return $this->belongsTo(Base::class);
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    public function views()
    {
        return $this->hasMany(View::class);
    }

    public function getIsActiveAttribute($value)
    {
        return (bool) $value;
    }
}
