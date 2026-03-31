<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $table = 'CONTRACTS';
    protected $primaryKey = 'ID_CONTRACTS';
    public $timestamps = false;

    protected $fillable = ['Description'];

    public function payments()
    {
        return $this->hasMany(Payment::class, 'contractsid', 'ID_CONTRACTS');
    }
}