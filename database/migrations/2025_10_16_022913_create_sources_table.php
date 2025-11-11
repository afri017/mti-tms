<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sources', function (Blueprint $table) {
            $table->string('id', 10)->primary(); // contoh: P00001 / Q00001
            $table->enum('type', ['Source', 'Destination']);
            $table->string('location_name');
            $table->integer('capacity')->default(0);
            $table->string('created_by')->nullable();
            $table->string('update_by')->nullable();
            $table->timestamps(); // created_at & updated_at
            $table->timestamp('last_update')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sources');
    }
};
