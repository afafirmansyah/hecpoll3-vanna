<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'PAYMENTS';
    protected $primaryKey = 'ID_Payments';
    public $timestamps = false;

    protected $fillable = [
        'transnumber', 'transdatetime', 'TransQuantity', 'CardPAN', 
        'vehiclelicenseplate', 'Mileage', 'AdditionalEntry'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'VehiclesID', 'ID_VEHICLES');
    }

    public function terminal()
    {
        return $this->belongsTo(Terminal::class, 'terminalsID', 'ID_TERMINALS');
    }

    public function card()
    {
        return $this->belongsTo(Card::class, 'CARDSID', 'ID_CARDS');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contractsid', 'ID_CONTRACTS');
    }
}