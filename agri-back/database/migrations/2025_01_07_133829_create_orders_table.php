<?php

// Migration pour la table orders
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('total_amount');  // Montant total de la commande
            $table->enum('status', ['pending', 'completed', 'canceled'])->default('pending');
            $table->boolean('is_regular')->default(false);  // Champ pour indiquer si la commande est régulière
            $table->enum('regularity_type', ['daily', 'weekly', 'monthly'])->nullable();  // Fréquence de la commande
            $table->integer('interval')->nullable();  // Intervalle entre les commandes
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};