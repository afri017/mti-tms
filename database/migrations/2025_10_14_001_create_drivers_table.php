<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->string('iddriver')->primary();
            $table->string('name');
            $table->string('no_sim')->nullable();
            $table->string('typesim')->nullable();
            $table->string('notelp')->nullable();
            $table->text('address')->nullable();
            $table->string('created_by')->nullable();
            $table->string('update_by')->nullable();
            $table->timestamps(); // created_at, updated_at
            $table->timestamp('last_update')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
