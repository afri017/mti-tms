<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('po_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nopo', 50);
            $table->integer('itempo');
            $table->string('material_code', 50);
            $table->decimal('qty', 15, 2);
            $table->string('uom', 10)->nullable();
            $table->string('created_by', 50)->nullable();
            $table->string('update_by', 50)->nullable();
            $table->timestamps(); // created_at dan updated_at
            $table->timestamp('last_update')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_items');
    }
};
