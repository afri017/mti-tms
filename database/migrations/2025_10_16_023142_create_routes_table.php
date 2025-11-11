<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->string('route', 10)->primary();
            $table->string('source', 10);
            $table->string('destination', 10);
            $table->integer('leadtime')->nullable();
            $table->string('route_name')->nullable();
            $table->string('created_by')->nullable();
            $table->string('update_by')->nullable();
            $table->timestamp('last_update')->nullable();
            $table->timestamps();

            // relasi opsional
            // $table->foreign('source')->references('id')->on('sources');
            // $table->foreign('destination')->references('id')->on('sources');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
