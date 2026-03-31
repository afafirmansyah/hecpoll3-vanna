<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Terminal extends Model
{
    protected $table = 'TERMINALS';
    protected $primaryKey = 'ID_TERMINALS';
    public $timestamps = false;

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class, 'StationsID', 'ID_STATIONS');
    }
}