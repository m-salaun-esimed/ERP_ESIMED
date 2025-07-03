<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('second_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable(); // <-- ajout ici
            $table->string('phone_number')->nullable();
            $table->bigInteger('max_annual_revenue')->nullable();
            $table->string('password');
            $table->string('token')->nullable();
            $table->string('street')->nullable();          // Rue, numéro, etc.
            $table->string('postal_code')->nullable();     // Code postal
            $table->string('city')->nullable();            // Ville
            $table->string('region')->nullable();          // Région, département (si utile)
            $table->string('country')->nullable();         // Pays
            $table->bigInteger('charge_rate')->nullable();
            $table->boolean('admin')->nullable();
            $table->rememberToken()->nullable();
            $table->timestamps();
            $table->boolean('first_login_completed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
