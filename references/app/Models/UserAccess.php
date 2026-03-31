<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAccess extends Model
{
    protected $table = 'USER_ACCESS';
    protected $fillable = ['user_id', 'station_ids', 'terminal_ids'];
    protected $casts = [
        'station_ids' => 'array',
        'terminal_ids' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}