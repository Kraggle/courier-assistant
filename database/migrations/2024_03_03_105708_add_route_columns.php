<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('routes', function (Blueprint $table) {
            $table->boolean('vat')->default(0)->after('type');
            $table->integer('ttfs')->default(60)->after('vat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropColumn([
                'vat',
                'ttfs'
            ]);
        });
    }
};
