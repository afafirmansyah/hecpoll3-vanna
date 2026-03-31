<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelRatio extends Model
{
    protected $table = 'fuelratio';
    public $timestamps = false;

    protected $fillable = ['date', 'OB_BISM', 'COAL_BISM', 'PORT_BISM'];

    protected $casts = [
        'date' => 'date',
    ];
}