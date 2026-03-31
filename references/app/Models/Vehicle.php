<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table = 'VEHICLES';
    protected $primaryKey = 'ID_VEHICLES';
    public $timestamps = false;

    protected $fillable = [
        'Number', 'Description', 'LicensePlate', 'Consumption', 'ConsumptionPercentage'
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class, 'VehiclesID', 'ID_VEHICLES');
    }

    public function vehicleGroup()
    {
        return $this->belongsTo(VehicleGroup::class, 'VehicleGroupsID', 'ID_VEHICLEGROUPS');
    }
}