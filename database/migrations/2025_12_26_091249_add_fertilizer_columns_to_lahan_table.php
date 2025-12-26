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
        Schema::table('lahan', function (Blueprint $table) {
            // Kita letakkan setelah produktivitas agar rapi
            $table->double('urea')->nullable()->after('produktivitas');
            $table->double('npk')->nullable()->after('urea');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lahan', function (Blueprint $table) {
            $table->dropColumn(['urea', 'npk']);
        });
    }
};
