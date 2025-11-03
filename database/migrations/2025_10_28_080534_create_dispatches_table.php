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
        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();
            $table->string('dispatch_number')->unique();
            $table->date('dispatch_date');
            $table->foreignId('from_branch_id')->contrained('branches', 'id');
            $table->foreignId('from_location_id')->contrained('locations', 'id');
            $table->morphs('dispatch_to');
            $table->dateTime('dispatch_time')->useCurrent();
            $table->enum('dispatch_type', ['sales', 'transfer']);
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->integer('status')->default(0)->comment("0 => pending, 1 => closed, 3 => canceled");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatches');
    }
};
