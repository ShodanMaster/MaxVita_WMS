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
        Schema::create('production_barcodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_plan_id')->constrained();
            $table->string('barcode');
            $table->foreignId('item_id')->constrained();
            $table->decimal('total_quantity',15, 4);
            $table->decimal('balance_quantity',15, 4);
            $table->decimal('generated_quantity',15, 4);
            $table->date('date_of_manufacture');
            $table->date('best_before_date');
            $table->foreignId('uom_id')->constrained();
            $table->dateTime('gerated_time')->useCurrent();
            $table->foreignId('user_id')->constrained();
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_barcodes');
    }
};
