<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gates', function (Blueprint $table) {
            $table->id();
            $table->string('gate', 10);
            $table->string('point', 10); // foreign key ke source
            $table->time('timestart');
            $table->time('timeend');
            $table->integer('type');
            $table->integer('duration_minutes'); // durasi dalam menit
            $table->timestamps();

            // Relasi ke tabel source (optional, tergantung struktur table sources)
            $table->foreign('point')->references('id')->on('sources')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gates');
    }
};
