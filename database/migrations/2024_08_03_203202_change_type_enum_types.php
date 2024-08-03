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
        Schema::table('routes', function (Blueprint $table) {
            $table->string('type')->change();
        });

        Schema::table('rates', function (Blueprint $table) {
            $table->string('type')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('routes', function (Blueprint $table) {
            $table->enum('type', Lists::routeTypes()->keys()->toArray())->change();
        });

        Schema::table('rates', function (Blueprint $table) {
            $table->enum('type', Lists::rateTypes()->keys()->toArray())->change();
        });
    }
};
