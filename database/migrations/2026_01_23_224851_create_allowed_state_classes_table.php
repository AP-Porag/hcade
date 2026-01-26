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
        Schema::create('allowed_state_classes', function (Blueprint $table) {
            $table->id();

            // FK → state_classes.code
            $table->string('state_class_code')->unique();

            $table->boolean('is_allowed')->default(false);

            $table->timestamps();

            $table->foreign('state_class_code')
                ->references('code')
                ->on('state_classes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allowed_state_classes');
    }
};
