<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_costs', function (Blueprint $table) {
            $table->string('id', 10)->primary(); // SC00001
            $table->string('idvendor', 10);
            $table->string('route', 10);
            $table->string('type_truck', 10);
            $table->decimal('price_freight', 15, 2)->default(0);
            $table->decimal('price_driver', 15, 2)->default(0);
            $table->date('validity_start');
            $table->date('validity_end');
            $table->char('active', 1)->default('Y');
            $table->timestamps();

            // Optional FK (kalau tabel lain sudah ada)
            // $table->foreign('idvendor')->references('idvendor')->on('vendors')->cascadeOnUpdate()->restrictOnDelete();
            // $table->foreign('route')->references('id')->on('routes')->cascadeOnUpdate()->restrictOnDelete();
            // $table->foreign('type_truck')->references('id')->on('tonnages')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_costs');
    }
};
