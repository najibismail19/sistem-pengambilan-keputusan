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
        Schema::create('pengambilan_keputusan', function (Blueprint $table) {
            $table->id('id_pengambilan_keputusan');
            $table->string('title');
            $table->text('description');
            $table->date('date');
            $table->string('tujuan');
            $table->unsignedBigInteger('user_create');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengambilan_keputusan');
    }
};
