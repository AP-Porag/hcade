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

            $table->string('acct');
            $table->integer('tax_year');

            // Site address
            $table->string('site_addr_1')->nullable();
            $table->string('site_addr_2')->nullable();
            $table->string('site_addr_3')->nullable();

            // Mailing address
            $table->string('mail_addr_1')->nullable();
            $table->string('mail_addr_2')->nullable();
            $table->string('mail_city')->nullable();
            $table->string('mail_state', 10)->nullable();
            $table->string('mail_zip', 20)->nullable();

            // Classification
            $table->string('state_class')->nullable();
            $table->string('neighborhood_code')->nullable();
            $table->string('neighborhood_group')->nullable();
            $table->string('market_area_1')->nullable();
            $table->string('market_area_2')->nullable();

            // Area
            $table->decimal('land_ar', 14, 2)->nullable();
            $table->decimal('bld_ar', 14, 2)->nullable();
            $table->decimal('acreage', 14, 4)->nullable();

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
