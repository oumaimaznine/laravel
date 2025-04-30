<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    // Les colonnes autorisées à l'insertion (fillables)
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'address',
        'city',
        'postal_code',
        'country',
        'phone',
    ];

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
