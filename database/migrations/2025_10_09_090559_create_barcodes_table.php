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
            $table->unsignedBigInteger('transaction_id');
            $table->enum('transaction_type', ['1', '2', '3', '4'])->comment('1=>GRN,2=>Production,3=>opening,4=>rejection');
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('location_id')->constrained();
            $table->foreignId('brand_id')->nullable()->constrained();
            $table->foreignId('bin_id')->nullable()->constrained();
            $table->foreignId('uom_id')->constrained();
            // $table->foreignId('pallet_id')->nullable()->constrained();
            $table->foreignId('item_id')->constrained();
            $table->date('date_of_manufacture');
            $table->date('best_before_date');
            $table->string('batch_number');
            $table->decimal('price', 11,3)->nullable();
            $table->decimal('total_price', 11, 3)->nullable();
            $table->decimal('shelf_life', 11,3)->default(0);
            $table->decimal('net_weight', 11,3)->default(0);
            $table->decimal('grn_net_weight', 11,3)->default(0);
            $table->decimal('spq_quantity', 11,3)->default(0);
            $table->decimal('grn_spq_quantity', 11,3)->default(0);
            $table->dateTime('barcode_time')->useCurrent();
            $table->enum('status', ['-1', '0', '1', '2', '3', '4', '5', '6', '8'])->comment('-1=>NIS; 0=>purchase return; 1=>stock; 2=>Dispatch; 4=>Transit; 5=>production 6=>Rejection; 8=>stock_out; 9=>repacked;');
            $table->foreignId('user_id')->constrained();
            $table->enum('qc_approval_status', ['0', '1', '2'])->default('0')->comment('0=>QC not Done; 1=>QC Done; 2=>QC rejected');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['transaction_type', 'transaction_id']);
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
