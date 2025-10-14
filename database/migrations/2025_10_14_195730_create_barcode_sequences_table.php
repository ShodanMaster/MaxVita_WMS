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
        Schema::create('barcode_sequences', function (Blueprint $table) {
            $table->string('prefix_key');
            $table->date('sequence_date');     
            $table->unsignedBigInteger('current_number')->default(0);
            $table->timestamps();

            $table->primary(['prefix_key', 'sequence_date']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barcode_sequences', function (Blueprint $table) {
            //
        });
    }
};
