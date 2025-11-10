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
        Schema::create('grn_subs', function (Blueprint $table) {
            $table->id();
            $table->foreignId(('grn_id'))->constrained();
            $table->foreignId('item_id')->constrained();
            $table->string('batch_number');
            $table->date('date_of_manufacture');
            $table->date('best_before_date');
            $table->decimal('total_quantity',15, 4);
            $table->decimal('number_of_barcodes',15, 4);
            $table->decimal('scanned_quantity',15, 4)->default(0);
            $table->decimal('rejected_quantity',15, 4)->default(0);
            $table->integer('grn_status')->default(0);
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
        Schema::dropIfExists('grn_subs');
    }
};
