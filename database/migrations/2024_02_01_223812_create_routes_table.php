<?php

use App\Helpers\Lists;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('depot_id');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->integer('start_mileage')->nullable();
            $table->integer('end_mileage')->nullable();
            $table->integer('stops')->nullable();
            $table->integer('invoice_mileage')->nullable();
            $table->decimal('bonus')->nullable();
            $table->text('note')->nullable();
            $table->enum('type', Lists::routeTypes()->keys()->toArray());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('routes');
    }
};
