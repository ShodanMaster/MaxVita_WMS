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
        Schema::create('receipt_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispatch_id')->constrained();
            $table->string('barcode');
            $table->foreignId('item_id')->constrained();
            $table->foreignId('bin_id')->constrained();
            $table->decimal('recieved_quantity', 11, 4);
            $table->foreignId('uom_id')->constrained();
            $table->dateTime('scan_time')->useCurrent();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_scans');
    }
};
