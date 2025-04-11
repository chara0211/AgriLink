<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Colonne id auto-incrémentée
            $table->string('name'); // Colonne name
            $table->string('email')->unique(); // Colonne email unique
            $table->string('password'); // Colonne password
            $table->enum('role', ['admin', 'farmer', 'client']); // Colonne role avec des valeurs spécifiques
            $table->boolean('vip_status')->default(false); // Colonne vip_status avec une valeur par défaut
            $table->timestamps(); // Colonnes created_at et updated_at gérées automatiquement
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

