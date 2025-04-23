<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTable extends Migration
{
    public function up()
    {
        Schema::create('Payment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained()->onDelete('cascade');
            $table->string('mode_paiement'); // carte, paypal, etc
            $table->string('statut')->default('en_attente'); // en_attente, payé, échoué        
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('Payment');
    }
}
