S<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('en_attente');
            $table->decimal('total', 8, 2);
            $table->text('shipping_address');
            $table->string('payment_method')->default('paypal');
            $table->string('payment_status')->default('paid');
            $table->string('transaction_id')->nullable();
        
            $table->timestamps();
        });
        
     
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}

