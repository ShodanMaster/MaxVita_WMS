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
        Schema::create('dispatch_subs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispatch_id')->constrained();
            $table->foreignId('item_id')->constrained();
            $table->foreignId('uom_id')->constrained();
            $table->decimal('total_quantity', 15, 4);
            $table->decimal('dispatch_quantity', 15, 4)->default(0);
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatch_subs');
    }
};
