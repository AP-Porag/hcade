<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * protected $fillable = [
     * 'acct',
     * 'tax_year',
     * 'site_street',
     * 'mail_city',
     * 'mail_state',
     * 'mail_zip',
     * 'mail_street',
     * 'address',
     * 'owner_name',
     * 'state_class',
     * 'neighborhood_code',
     * 'neighborhood_group',
     * 'market_area_1',
     * 'market_area_2',
     * 'land_area',
     * 'building_area',
     * 'acreage',
     * 'legal_description',
     * 'market_value',
     * 'is_active',
     * ];
     */
    public function up(): void
    {
        Schema::create('property_masters', function (Blueprint $table) {
            $table->id();
            $table->string('acct');
            $table->integer('tax_year');

            // Searchable address
            $table->string('address')->nullable();

            // Owner
            $table->string('owner_name')->nullable();

            // Classification
            $table->string('state_class')->nullable();
            $table->string('neighborhood_code')->nullable();
            $table->string('neighborhood_group')->nullable();
            $table->string('market_area_1')->nullable();
            $table->string('market_area_2')->nullable();

            // Physical summary
            $table->decimal('land_area', 12, 2)->nullable();
            $table->decimal('building_area', 12, 2)->nullable();
            $table->decimal('acreage', 12, 4)->nullable();

            // Valuation (denormalized)
            $table->decimal('market_value', 14, 2)->nullable();

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
        Schema::dropIfExists('property_masters');
    }
};
