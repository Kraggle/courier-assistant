<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('dsps', function (Blueprint $table) {
            $table->integer('in_hand')->default(2)->after('identifier');
            $table->integer('pay_day')->default(4)->after('in_hand');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('dsps', function (Blueprint $table) {
            $table->dropColumn('in_hand');
            $table->dropColumn('pay_day');
        });
    }
};
