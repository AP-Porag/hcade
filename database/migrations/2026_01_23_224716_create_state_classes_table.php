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
        Schema::create('state_classes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description');      // Residential Single Family
            $table->string('dept')->nullable(); // A1, B1, C1
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('state_classes');
    }
};
