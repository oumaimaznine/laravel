<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Identifiant unique auto-incrémenté
            $table->string('name'); // Nom du produit
            $table->text('description')->nullable(); // Description longue (facultative)
            $table->decimal('price', 8, 2); // Prix avec 2 chiffres après la virgule
            $table->string('image')->nullable(); // URL ou nom de l’image (facultatif)
            $table->timestamps(); // created_at et updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('products'); 
    }
}
