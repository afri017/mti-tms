<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->string('idvendor', 10)->primary();
            $table->string('transporter_name', 150);
            $table->string('notelp', 20)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('npwp', 50)->nullable();
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps(); // created_at & updated_at
            $table->timestamp('last_update')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
