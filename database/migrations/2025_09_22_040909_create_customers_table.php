<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto increment
            $table->string('idcustomer', 50)->unique();
            $table->string('customer_name', 255);
            $table->text('address')->nullable();
            $table->string('notelp', 50)->nullable();
            $table->enum('is_active', ['Y', 'N'])->default('Y');
            $table->string('created_by', 100)->nullable();
            $table->string('update_by', 100)->nullable();
            $table->timestamp('last_update')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('update_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
