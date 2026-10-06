<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('nama')->after('id');
        $table->string('alamat')->nullable()->after('nama');
        $table->string('no_ktp')->nullable()->after('alamat');
        $table->string('no_hp')->nullable()->after('no_ktp');
        $table->string('no_rm', 25)->nullable()->after('no_hp');
        $table->enum('role', ['admin', 'dokter', 'pasien'])->after('no_rm');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn([
            'nama',
            'alamat',
            'no_ktp',
            'no_hp',
            'no_rm',
            'role',
        ]);
    });
}
};
