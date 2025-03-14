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
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itineraire_id')->constrained()->onDelete('cascade');
            $table->string('nom');
            $table->string('lieu_logement');
            $table->json('endroits_a_visiter')->nullable();
            $table->json('activites')->nullable();
            $table->json('plats_a_essayer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};