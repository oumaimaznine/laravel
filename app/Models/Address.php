<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'address',
        'region',
        'city',
        'postal_code',
        'country',
    ];
    

    // Optionnel : relation avec utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
