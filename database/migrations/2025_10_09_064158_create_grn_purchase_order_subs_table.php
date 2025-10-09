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
        Schema::create('grn_purchase_order_subs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grn_purchase_order_id')->constrained();
            $table->string('purchase_number');
            $table->foreignId('item_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grn_purchase_order_subs');
    }
};
