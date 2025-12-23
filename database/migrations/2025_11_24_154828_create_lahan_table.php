<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('lahan', function (Blueprint $table) {
            $table->id();
            $table->string('nop')->unique();
            $table->string('nama')->nullable();
            $table->double('luas')->nullable();
            $table->integer('klaster')->nullable();
            $table->double('estimasi_panen')->nullable();
            $table->string('jenis_tanah')->nullable();

            $table->double('lat')->nullable();
            $table->double('lon')->nullable();

            $table->longText('polygon')->nullable(); // GeoJSON geometry
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lahan');
    }
};
