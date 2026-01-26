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
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();

            $table->string('context')->nullable(); // domain
            $table->integer('tax_year')->nullable();

            // service-level progress
            $table->integer('total_services')->default(0);
            $table->integer('current_service_index')->default(0);
            $table->string('current_service')->nullable();
            $table->integer('service_progress')->default(0);

            // chunk-level progress
            $table->bigInteger('total_chunks')->default(0);
            $table->bigInteger('current_chunk')->default(0);
            $table->integer('chunk_progress')->default(0);

            $table->json('state_classes')->nullable();
            $table->string('last_key')->nullable(); // acct

            $table->string('message')->nullable();

            $table->enum('status', ['running','success','failed'])->default('running');
            $table->text('error')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};
