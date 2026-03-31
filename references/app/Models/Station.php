<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Station extends Model
{
    protected $table = 'STATIONS';
    protected $primaryKey = 'ID_STATIONS';
    public $timestamps = false;

    public function terminals(): HasMany
    {
        return $this->hasMany(Terminal::class, 'StationsID', 'ID_STATIONS');
    }
}