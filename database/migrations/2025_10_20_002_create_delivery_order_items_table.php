<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryOrderItemsTable extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_order_items', function (Blueprint $table) {
            $table->id();
            $table->string('nodo')->index();
            $table->integer('doitem')->comment('Item number in DO');
            $table->string('material_code', 50);
            $table->decimal('qty_plan', 12, 2)->nullable();
            $table->decimal('qty_act', 12, 2)->nullable();
            $table->string('uom', 10)->nullable();
            $table->decimal('qty_receipt', 12, 2)->nullable();
            $table->decimal('qty_reject', 12, 2)->nullable();
            $table->string('created_by', 50)->nullable();
            $table->string('update_by', 50)->nullable();
            $table->timestamps();
            $table->timestamp('last_update')->nullable();

            // Relasi ke delivery_orders (optional)
            $table->foreign('nodo')
                ->references('nodo')
                ->on('delivery_orders')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_order_items');
    }
}
