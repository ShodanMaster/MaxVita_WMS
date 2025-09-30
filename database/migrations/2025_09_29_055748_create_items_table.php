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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('sub_category_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('uom_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name')->unique();
            $table->string('item_code')->unique();
            $table->integer('in_stock')->nullable();
            $table->string('gst_rate')->nullable();
            $table->string('sku_code')->nullable();
            $table->enum('item_type', ['RM', 'FG']);
            $table->decimal('spq_quantity', 10, 3)->nullable();
            $table->decimal('net_weight', 10, 3)->nullable();
            $table->decimal('gross_weight', 10, 3)->nullable();
            $table->integer('primary_shelf_life')->nullable();
            $table->integer('secondary_shelf_life')->nullable();
            $table->integer('best_before_type')->nullable()->comment("1 => Days, 2 => Months, 3 => Years");
            $table->integer('best_before_value')->nullable();
            $table->integer('dispatch_alert')->nullable();
            $table->string('hsn_code')->nullable();
            $table->decimal('single_packet_weight', 10, 3)->nullable();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
