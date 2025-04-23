<?php

namespace App\Models; // Le namespace (espace de nom) du modèle

use Illuminate\Database\Eloquent\Factories\HasFactory; // Pour utiliser les "factories" de Laravel
use Illuminate\Database\Eloquent\Model; // On hérite de la classe Model de Laravel

class Product extends Model // Le modèle Product représente la table "products"
{
    use HasFactory; // Inclusion du trait HasFactory (optionnel mais utile pour les tests et les seeds)

    // Liste des colonnes que l’on autorise à remplir avec Product::create([...])
    protected $fillable = [
        'name',         // Nom du produit
        'description',  // Description du produit (peut être vide)
        'price',        // Prix du produit (ex: 39.99)
        'image',        // Nom ou URL de l’image du produit
    ];
}
