<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gate_usage', function (Blueprint $table) {
            // tambahkan constraint unik agar tidak bisa ada duplikasi slot di gate yang sama
            $table->unique(
                ['gate', 'delivery_date', 'timestart', 'timeend'],
                'unique_gate_schedule'
            );

            // optional tapi disarankan: buat index untuk mempercepat query pencarian
            $table->index(['gate', 'delivery_date'], 'idx_gate_date');
        });
    }

    public function down(): void
    {
        Schema::table('gate_usage', function (Blueprint $table) {
            $table->dropUnique('unique_gate_schedule');
            $table->dropIndex('idx_gate_date');
        });
    }
};
