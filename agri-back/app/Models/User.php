<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    // Les colonnes modifiables
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'vip_status',
    ];

    // Les colonnes cachées lors de la sérialisation
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Mutateur pour crypter le mot de passe
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * Obtenir l'identifiant pour JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Obtenir des informations supplémentaires pour JWT.
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
