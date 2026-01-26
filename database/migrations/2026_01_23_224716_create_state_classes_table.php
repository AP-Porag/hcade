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

            // RAW: Code
            $table->string('code')->unique();

            // RAW: Dept (grouping, NOT unique)
            $table->string('dept')->index();

            // RAW: Description
            $table->string('description');

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
