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
        Schema::create('data_tanahs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lokasi');
            $table->decimal('luas', 10, 2); // meter persegi
            $table->string('status_tanah'); // misalnya Hak Milik, Sewa, Hibah
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_tanahs');
    }
};
