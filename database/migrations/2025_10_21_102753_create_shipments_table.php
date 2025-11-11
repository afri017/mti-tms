<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('noshipment', 20)->unique();
            $table->string('route', 10);
            $table->string('shipcost', 10);
            $table->string('truck_id', 10);
            $table->string('driver', 10);
            $table->string('transporter', 10);
            $table->string('noseal', 20)->nullable();
            $table->date('delivery_date');
            $table->string('gate', 10)->nullable();
            $table->time('timestart')->nullable();
            $table->time('timeend')->nullable();
            $table->string('status', 20)->default('Open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
