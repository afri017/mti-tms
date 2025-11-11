<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('material_code', 50)->unique();
            $table->string('material_desc', 255);
            $table->string('uom', 10);
            $table->decimal('konversi_ton', 10, 2)->default(0);
            $table->string('created_by', 100)->nullable();
            $table->string('update_by', 100)->nullable();
            $table->timestamp('last_update')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('update_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
