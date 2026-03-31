<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleGroup extends Model
{
    protected $table = 'VEHICLEGROUPS';
    protected $primaryKey = 'ID_VEHICLEGROUPS';
    public $timestamps = false;

    protected $fillable = ['Description'];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'VehicleGroupsID', 'ID_VEHICLEGROUPS');
    }
}