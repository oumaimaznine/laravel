<?php
// database/migrations/xxxx_xx_xx_add_verification_code_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('verification_code')->nullable();
            $table->timestamp('email_verified_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('verification_code');
            $table->dropColumn('email_verified_at');
        });
    }
};
