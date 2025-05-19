<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use TCG\Voyager\Contracts\User as VoyagerUserContract; 
use Illuminate\Contracts\Auth\MustVerifyEmail;


use TCG\Voyager\Traits\VoyagerUser;

class User extends \TCG\Voyager\Models\User implements JWTSubject, VoyagerUserContract, MustVerifyEmail

{
    use Notifiable, VoyagerUser;

    protected $fillable = [
        'name',
        'email',
        'password',
       
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
     //  relation avec adress
    public function address()
    {
        return $this->hasOne(\App\Models\Address::class);
    }
    public function reviews()
{
    return $this->hasMany(Review::class);
}


}
