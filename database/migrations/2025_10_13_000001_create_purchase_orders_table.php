<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nopo', 50);
            $table->string('idcustomer', 50);
            $table->date('podate');
            $table->date('valid_to');
            $table->string('created_by', 100)->nullable();
            $table->string('update_by', 100)->nullable();
            $table->timestamp('last_update')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('update_at')->useCurrent()->useCurrentOnUpdate();

            // Optional: jika kamu ingin relasi dengan tabel customers
            // $table->foreign('idcustomer')->references('idcustomer')->on('customers');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
