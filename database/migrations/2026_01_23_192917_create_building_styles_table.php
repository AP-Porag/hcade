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
        Schema::create('building_styles', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();        // 101
            $table->string('description');           // Residential 1 Family
            $table->string('mapped_state_class')->nullable();   // A1, B1 (derived)
            $table->string('is_allowed')->default(false);   // A1, B1 (derived)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('building_styles');
    }
};
