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
            $table->string('state_class_code')->unique(); // A1, B1, C1
            $table->boolean('is_allowed')->default(false);
            $table->timestamps();

            $table->foreign('state_class_code')
                ->references('code')
                ->on('state_classes')
                ->cascadeOnDelete();
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
