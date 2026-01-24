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
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();

            $table->string('acct');
            $table->integer('bld_num');

            // Core building info
            $table->string('property_use_cd')->nullable();
            $table->string('impr_tp')->nullable();
            $table->string('impr_mdl_cd')->nullable();

            // Description / classification
            $table->string('structure')->nullable();
            $table->string('structure_dscr')->nullable();
            $table->string('quality_code')->nullable(); // qa_cd
            $table->string('description')->nullable();  // dscr

            // Dates
            $table->integer('year_built')->nullable();  // date_erected
            $table->integer('year_remodel')->nullable();
            $table->integer('year_roll')->nullable();

            // Area / size
            $table->decimal('gross_area', 12, 2)->nullable();
            $table->decimal('effective_area', 12, 2)->nullable();
            $table->decimal('base_area', 12, 2)->nullable();
            $table->decimal('heated_area', 12, 2)->nullable();

            // Valuation
            $table->decimal('replacement_cost', 14, 2)->nullable();
            $table->decimal('depreciated_value', 14, 2)->nullable();
            $table->decimal('depreciation_pct', 6, 2)->nullable();

            // Income (commercial)
            $table->decimal('total_income', 14, 2)->nullable();
            $table->decimal('occupancy_rate', 6, 2)->nullable();

            // Aggregated child data (JSON)
            $table->json('exterior')->nullable();
            $table->json('fixtures')->nullable();
            $table->json('structural_elements')->nullable();
            $table->json('extra_features')->nullable();

            $table->timestamps();

            $table->unique(['acct', 'bld_num']);
            $table->index('acct');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
