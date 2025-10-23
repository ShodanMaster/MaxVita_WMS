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
        Schema::create('production_issues', function (Blueprint $table) {
            $table->id();
            $table->string('barcode');
            $table->foreignId('item_id')->constrained();
            $table->foreignId('production_plan_id')->constrained();
            $table->decimal('scanned_quantity', 10,4);
            $table->decimal('net_weight', 10,4);
            $table->foreignId('user_id')->constrained();
            $table->dateTime('scan_time')->useCurrent();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_issues');
    }
};
