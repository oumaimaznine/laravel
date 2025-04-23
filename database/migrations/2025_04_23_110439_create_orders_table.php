Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->string('status')->default('en_attente');
    $table->decimal('total', 8, 2);
    $table->text('shipping_address');
    $table->timestamps();
});
