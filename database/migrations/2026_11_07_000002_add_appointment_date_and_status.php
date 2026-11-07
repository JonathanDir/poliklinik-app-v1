<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daftar_poli', function (Blueprint $table) {
            $table->date('tanggal_periksa')->nullable()->after('id_jadwal');
            $table->string('status', 20)->default('menunggu')->after('no_antrian');
            $table->index(['tanggal_periksa', 'id_jadwal', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('daftar_poli', function (Blueprint $table) {
            $table->dropIndex(['tanggal_periksa', 'id_jadwal', 'status']);
            $table->dropColumn(['tanggal_periksa', 'status']);
        });
    }
};
