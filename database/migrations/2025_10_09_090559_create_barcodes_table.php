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
        Schema::create('barcodes', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->foreignId('grn_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('location_id')->constrained();
            $table->foreignId('bin_id')->nullable()->constrained();
            // $table->foreignId('pallet_id')->nullable()->constrained();
            $table->foreignId('item_id')->constrained();
            $table->date('date_of_manufacture');
            $table->date('best_before_date');
            $table->string('batch_number');
            $table->decimal('price', 11,3);
            $table->decimal('total_price', 11, 3);
            $table->integer('shelf_life')->default(0);
            $table->integer('net_weight')->default(0);
            $table->integer('grn_net_weight')->default(0);
            $table->dateTime('barcode_time')->useCurrent();
            $table->enum('status', ['-1', '0', '1', '2', '3', '4', '5', '6', '8'])->comment('-1=>NIS; 0=>purchase return; 1=>stock; 2=>Dispatch; 4=>Transit; 5=>production 6=>Rejection; 8=>stock_out; 9=>repacked;');
            $table->foreignId('user_id')->constrained();
            $table->enum('qc_approval_status', ['0', '1', '2'])->default('0')->comment('0=>QC not Done; 1=>QC Done; 2=>QC rejected');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barcodes');
    }
};
