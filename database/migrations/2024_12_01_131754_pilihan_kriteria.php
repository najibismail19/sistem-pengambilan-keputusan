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
        Schema::create('pilihan_kriteria', function (Blueprint $table) {
            $table->id(); // Kolom id otomatis
            $table->string('value'); // Kolom value
            $table->string('name'); // Kolom name
            $table->unsignedBigInteger('id_kriteria'); // Kolom id_kriteria
            $table->foreign('id_kriteria')->references('id')->on('kriteria'); // Menambahkan foreign key jika tabel kriteria ada
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pilihan_kriteria');

    }
};
