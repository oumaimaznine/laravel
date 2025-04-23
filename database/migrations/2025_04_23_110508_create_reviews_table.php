Schema::create('reviews', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
    $table->tinyInteger('rating'); // 1 à 5
    $table->text('comment')->nullable();
    $table->timestamps();
});
