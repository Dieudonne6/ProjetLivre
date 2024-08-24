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
        Schema::create('livres', function (Blueprint $table) {
            $table->id();
            $table->string('nomL');
            $table->foreignId('categorieL')->constrained()->onDelete('cascade'); // Clé étrangère vers categories
            // $table->string('categorieL');
            $table->longText('description');
            $table->binary('path');
            $table->string('statutL');
            $table->date('date');
            $table->integer('prixL');
            $table->integer('id_vendeur');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livres');
    }
};
