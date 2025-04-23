Schema::create('product_images', function (Blueprint $table) {
    $table->id();
    $table->string('url');
    $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
    $table->timestamps();
});
