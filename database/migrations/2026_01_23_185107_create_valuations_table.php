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
        Schema::create('valuations', function (Blueprint $table) {
            $table->id();
            $table->string('acct')->index();
            $table->integer('tax_year')->index();
            $table->decimal('land_value',14,2)->nullable();
            $table->decimal('building_value',14,2)->nullable();
            $table->decimal('market_value',14,2)->nullable();
            $table->decimal('appraised_value',14,2)->nullable();
            $table->timestamps();

            $table->unique(['acct','tax_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('valuations');
    }
};
