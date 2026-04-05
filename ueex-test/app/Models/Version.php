<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Version extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'versionable_id',
        'versionable_type',
        'version',
        'data',
        'created_at',
    ];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->created_at = now();
        });
    }

    public function versionable(): MorphTo
    {
        return $this->morphTo();
    }
}
