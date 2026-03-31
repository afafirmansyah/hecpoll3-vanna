<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'WEBUSERS';

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role_id',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasPermission($permission): bool
    {
        return $this->role && $this->role->hasPermission($permission);
    }

    public function hasRole($role): bool
    {
        return $this->role && $this->role->name === $role;
    }

    public function userAccess(): HasOne
    {
        return $this->hasOne(UserAccess::class);
    }

    public function hasStationAccess($stationId): bool
    {
        if (!$this->userAccess || !$this->userAccess->station_ids) return true;
        return in_array($stationId, $this->userAccess->station_ids);
    }

    public function hasTerminalAccess($terminalId): bool
    {
        if (!$this->userAccess || !$this->userAccess->terminal_ids) return true;
        return in_array($terminalId, $this->userAccess->terminal_ids);
    }
}
