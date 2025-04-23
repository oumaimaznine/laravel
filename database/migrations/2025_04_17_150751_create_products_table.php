public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->id(); // Identifiant unique auto-incrémenté
        $table->string('name'); // Nom du produit (ex: Croquettes pour chat)
        $table->text('description')->nullable(); // Description longue du produit (facultative)
        $table->decimal('price', 8, 2); // Prix avec deux chiffres après la virgule (ex: 29.99)
        $table->string('image')->nullable(); // URL ou nom de fichier image (facultatif)
        $table->timestamps(); // Colonnes created_at et updated_at
    });
}
