Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->decimal('price', 8, 2);
    $table->integer('stock');
    $table->float('weight')->nullable();
    $table->boolean('is_active')->default(true);
    $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
    $table->timestamps();
});
