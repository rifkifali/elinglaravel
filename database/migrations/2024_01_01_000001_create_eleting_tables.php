<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel users (ganti tabel users bawaan Laravel)
        Schema::table('users', function (Blueprint $table) {
            $table->string('nama_lengkap')->nullable()->after('name');
            $table->string('nip', 18)->unique()->nullable()->after('nama_lengkap');
            $table->enum('role', ['Admin', 'Perancang', 'Kasubbag', 'Kabag', 'Super Admin'])->default('Admin')->after('nip');
        });

        // Tabel dokumen_induk
        Schema::create('dokumen_induk', function (Blueprint $table) {
            $table->id();
            $table->string('judul_draf');
            $table->string('instansi_pemohon');
            $table->string('file_surat_awal');
            $table->enum('status_global', [
                'Menunggu Disposisi',
                'Proses Drafting',
                'Review Kasubbag',
                'Review Kabag',
                'Final'
            ])->default('Menunggu Disposisi');
            $table->timestamp('tanggal_masuk')->useCurrent();
            $table->timestamps();
        });

        // Tabel penugasan
        Schema::create('penugasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dokumen_id')->constrained('dokumen_induk')->onDelete('cascade');
            $table->foreignId('perancang_id')->constrained('users')->onDelete('cascade');
            $table->text('catatan_disposisi')->nullable();
            $table->date('deadline');
            $table->timestamps();
        });

        // Tabel draf_versi
        Schema::create('draf_versi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dokumen_id')->constrained('dokumen_induk')->onDelete('cascade');
            $table->integer('versi_ke')->default(1);
            $table->string('file_draf');
            $table->text('catatan_revisi')->nullable();
            $table->timestamp('waktu_upload')->useCurrent();
            $table->timestamps();
        });

        // Tabel lembar_kendali
        Schema::create('lembar_kendali', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dokumen_id')->constrained('dokumen_induk')->onDelete('cascade');
            $table->foreignId('aktor_id')->constrained('users')->onDelete('cascade');
            $table->text('tindakan');
            $table->timestamp('waktu_tindakan')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lembar_kendali');
        Schema::dropIfExists('draf_versi');
        Schema::dropIfExists('penugasan');
        Schema::dropIfExists('dokumen_induk');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nama_lengkap', 'nip', 'role']);
        });
    }
};