<?php

namespace App\Models;

use App\Enums\RoleType;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'unit_id', 'name', 'email', 'password',
        'role', 'provider', 'provider_id', 'provider_token'
    ];
    protected $hidden = ['password', 'provider_token'];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function leaves()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function roles() { return $this->belongsToMany(Role::class); }

    public function hasRole(RoleType $role): bool
    {
        $this->loadMissing('roles');
        return $this->roles->contains('name', $role->value);
    }
}
