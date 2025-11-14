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
        Schema::create('grns', function (Blueprint $table) {
            $table->id();
            $table->string('grn_number')->unique();
            $table->string('purchase_number')->nullable();
            $table->string('invoice_number')->nullable();
            $table->date('invoice_date')->nullable();
            $table->foreignId('vendor_id')->constrained();
            $table->foreignId('location_id')->constrained();
            $table->enum('grn_type', ['RM','FG']);
            $table->foreignId('brand_id')->nullable()->constrained();
            $table->text('remarks')->nullable();
            $table->foreignId('branch_id')->constrained();
            $table->integer('status')->default(0)->comment('0=>storage scan pending; 1=>storage scan completed;');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grns');
    }
};
