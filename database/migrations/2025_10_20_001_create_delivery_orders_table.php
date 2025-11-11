<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryOrdersTable extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('noshipment', 50)->nullable();
            $table->string('nodo')->unique()->comment('Nomor DO');
            $table->string('nopo')->nullable()->comment('Nomor PO');
            $table->date('delivery_date')->nullable();
            $table->string('source', 50)->nullable();
            $table->string('destination', 50)->nullable();
            $table->decimal('tara_weight', 10, 2)->nullable();
            $table->decimal('gross_weight', 10, 2)->nullable();
            $table->datetime('checkin')->nullable();
            $table->datetime('checkout')->nullable();
            $table->datetime('start_loading')->nullable();
            $table->datetime('end_loading')->nullable();
            $table->datetime('receipt_date')->nullable();
            $table->string('created_by', 50)->nullable();
            $table->string('update_by', 50)->nullable();
            $table->timestamps();
            $table->timestamp('last_update')->nullable();
            $table->string('idtruck', 10)->nullable();

            $table->index(['nodo', 'nopo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_orders');
    }
}
