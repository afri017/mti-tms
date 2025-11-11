<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trucks', function (Blueprint $table) {
            $table->string('idtruck')->primary();
            $table->string('iddriver');
            $table->string('idvendor', 10)->nullable();
            $table->string('type_truck');
            $table->string('stnk')->nullable();
            $table->string('merk')->nullable();
            $table->string('nopol')->nullable();
            $table->date('expired_kir')->nullable();
            $table->string('created_by')->nullable();
            $table->string('update_by')->nullable();
            $table->timestamps(); // created_at, updated_at
            $table->timestamp('last_update')->nullable();

            // Relasi
            $table->foreign('iddriver')->references('iddriver')->on('drivers')->onDelete('cascade');
            $table->foreign('type_truck')->references('id')->on('tonnages')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trucks');
    }
};
