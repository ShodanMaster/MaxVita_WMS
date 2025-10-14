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
        Schema::create('production_plan_subs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_plan_id')->constrained();
            $table->foreignId('item_id')->constrained();
            $table->integer('total_quantity');
            $table->integer('picked_quantity')->default(0);
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
        Schema::dropIfExists('production_plan_subs');
    }
};
