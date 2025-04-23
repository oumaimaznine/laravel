<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTable extends Migration
{
    public function up()
    {
        Schema::create('Order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('total', 8, 2);
            $table->string('statut')->default('en_attente'); // en_attente, validée, annulée    
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('Order');
    }
}
