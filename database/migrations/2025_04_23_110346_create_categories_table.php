Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->foreignId('parent_category_id')->nullable()->constrained('categories')->onDelete('cascade');
    $table->timestamps();
});
