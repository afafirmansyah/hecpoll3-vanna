<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $table = 'CARDS';
    protected $primaryKey = 'ID_CARDS';
    public $timestamps = false;

    protected $fillable = ['PAN', 'CardLayoutsID'];

    public function payments()
    {
        return $this->hasMany(Payment::class, 'CARDSID', 'ID_CARDS');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'VehiclesID', 'ID_VEHICLES');
    }
}