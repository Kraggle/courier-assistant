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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->string('describe');
            $table->decimal('cost', 8, 2)->default(0);
            $table->boolean('ignore')->default(0);
            $table->enum('type', Lists::expenseTypes()->keys()->toArray());
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('expenses');
    }
};
