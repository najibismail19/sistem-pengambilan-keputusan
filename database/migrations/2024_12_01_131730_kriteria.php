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
        Schema::create('kriteria', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pengambilan_keputusan');
            $table->string('kriteria');
            $table->string('nama');
            $table->float('bobot');
            $table->enum('jenis', ['benefit', 'cost']); // Kolom enum untuk jenis kriteria
            $table->timestamps();

            $table->foreign('id_pengambilan_keputusan')->references('id_pengambilan_keputusan')->on('pengambilan_keputusan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kriteria');
    }
};
