<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_order_attachments', function (Blueprint $table) {
            $table->id();
            $table->string('nodo');              // Relasi ke DO
            $table->string('filename');          // Nama file yang disimpan
            $table->string('filepath')->nullable(); // Path penyimpanan file
            $table->string('uploaded_by')->nullable();
            $table->timestamps();

            // Optional: index untuk optimasi relasi
            $table->index('nodo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_order_attachments');
    }
};
