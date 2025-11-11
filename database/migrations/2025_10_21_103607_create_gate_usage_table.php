<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gate_usage', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('gate', 10);
            $table->string('noshipment', 20);
            $table->date('delivery_date');
            $table->time('timestart');
            $table->time('timeend');
            $table->timestamps();

            // optional foreign keys jika dibutuhkan:
            // $table->foreign('gate')->references('gate')->on('gates')->onDelete('cascade');
            // $table->foreign('noshipment')->references('noshipment')->on('shipments')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gate_usage');
    }
};
