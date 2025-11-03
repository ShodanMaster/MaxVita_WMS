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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('name');
            $table->string('prefix')->unique();
            $table->string('location_code');
            $table->enum('location_type', ['1', '2', '3', '4']);
            $table->text('description')->nullable();
            $table->tinyInteger('barcode_enabled')->default(0);
            $table->tinyInteger('bin_mandatory')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locaitons');
    }
};
