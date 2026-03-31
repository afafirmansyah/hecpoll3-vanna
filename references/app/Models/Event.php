<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'EVENTS';
    protected $primaryKey = 'ID_EVENTS';
    public $timestamps = false;

    protected $fillable = ['EventDateTime', 'Description', 'Details', 'confirmation'];

    protected $casts = [
        'EventDateTime' => 'datetime',
    ];
}