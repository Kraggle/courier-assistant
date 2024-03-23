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
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dsp_id');
            $table->unsignedBigInteger('depot_id');
            $table->date('date');
            $table->enum('type', Lists::rateTypes()->keys()->toArray());
            $table->decimal('amount', 7, 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('rates');
    }
};
