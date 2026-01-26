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

            // From RAW: Code
            $table->string('code')->unique();

            // From RAW: Description
            $table->string('description');

            // Business logic (added later, but schema-ready)
            $table->string('mapped_state_class')->nullable();
            $table->boolean('is_allowed')->default(false);

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
