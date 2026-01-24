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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('acct', 20);
            $table->integer('tax_year');

            // Address (structured)
            $table->string('site_street')->nullable();
            $table->string('site_city')->nullable();
            $table->string('site_state', 10)->nullable();
            $table->string('site_zip', 20)->nullable();

            $table->string('mail_street')->nullable();
            $table->string('mail_city')->nullable();
            $table->string('mail_state', 10)->nullable();
            $table->string('mail_zip', 20)->nullable();

            // Ownership summary
            $table->string('owner_name_current')->nullable();
            $table->boolean('owner_occupied')->default(false);

            // Classification
            $table->string('state_class')->nullable();
            $table->string('neighborhood_code')->nullable();
            $table->string('neighborhood_group')->nullable();
            $table->string('market_area_1')->nullable();
            $table->string('market_area_2')->nullable();

            // Physical
            $table->decimal('land_area', 12, 2)->nullable();
            $table->decimal('building_area', 12, 2)->nullable();
            $table->decimal('acreage', 12, 4)->nullable();

            // Legal
            $table->text('legal_description')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['acct', 'tax_year']);
            $table->index('acct');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
