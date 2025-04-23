Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->string('phone')->nullable();
    $table->text('address')->nullable();
    $table->string('role')->default('client');
    $table->timestamps();
});
