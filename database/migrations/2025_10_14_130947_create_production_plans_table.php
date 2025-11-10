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
        Schema::create('production_plans', function (Blueprint $table) {
            $table->id();
            $table->string(('plan_number'))->unique();
            $table->date('plan_date');
            $table->decimal('total_quantity',15, 4);
            $table->decimal('picked_quantity',15, 4)->default(0);
            $table->decimal('scanned_quantity',15, 4)->default(0);
            $table->foreignId('item_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('branch_id')->constrained();
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
        Schema::dropIfExists('production_plans');
    }
};
