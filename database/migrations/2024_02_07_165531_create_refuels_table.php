<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('refuels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->date('date');
            $table->bigInteger('mileage');
            $table->integer('miles')->default(0);
            $table->decimal('cost', 5, 2)->default(0);
            $table->decimal('fuel_rate', 5, 4)->default(0.2200);
            $table->boolean('first')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('refuels');
    }
};
