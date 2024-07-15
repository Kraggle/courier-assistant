<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('review', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id');
            $table->string('fingerprint');
            $table->boolean('thumbs_up');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('review');
    }
};
