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
        Schema::create('opening_stock_subs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opening_stock_id')->constrained();
            $table->foreignId('item_id')->constrained();
            $table->date('manufacture_date');
            $table->date('best_before');
            $table->foreignId('bin_id')->constrained();
            $table->decimal('total_quantity',15, 4);
            $table->integer('number_of_barcodes');
            $table->string('batch');
            $table->integer('status')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opening_stock_subs');
    }
};
