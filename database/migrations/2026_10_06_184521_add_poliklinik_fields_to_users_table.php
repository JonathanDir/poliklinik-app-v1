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
            if (! Schema::hasColumn('users', 'nama')) $table->string('nama')->after('id');
            if (! Schema::hasColumn('users', 'alamat')) $table->string('alamat')->nullable()->after('nama');
            if (! Schema::hasColumn('users', 'no_ktp')) $table->string('no_ktp')->nullable()->after('alamat');
            if (! Schema::hasColumn('users', 'no_hp')) $table->string('no_hp')->nullable()->after('no_ktp');
            if (! Schema::hasColumn('users', 'no_rm')) $table->string('no_rm', 25)->nullable()->after('no_hp');
            if (! Schema::hasColumn('users', 'role')) $table->enum('role', ['admin', 'dokter', 'pasien'])->after('no_rm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // These fields are part of the original users table definition.
    }
};
