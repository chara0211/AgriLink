<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id(); // ID du commentaire
            $table->unsignedBigInteger('post_id'); // Référence au post
            $table->unsignedBigInteger('user_id'); // Référence à l'utilisateur
            $table->text('content'); // Contenu du commentaire
            $table->timestamps(); // Colonnes created_at et updated_at

            // Clés étrangères
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
}