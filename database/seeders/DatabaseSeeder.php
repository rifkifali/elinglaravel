<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Admin TU (Tata Usaha)
        User::create([
            'nip' => '198001012005011001',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Budi Santoso',
            'role' => 'Admin'
        ]);

        // 2. Akun Perancang 1 (Legal Drafter)
        User::create([
            'nip' => '198502022010011002',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Andi Wijaya, S.H.',
            'role' => 'Perancang'
        ]);

        // 3. Akun Perancang 2 (Legal Drafter)
        User::create([
            'nip' => '198703032012012003',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Siti Nurhaliza, S.H., M.H.',
            'role' => 'Perancang'
        ]);

        // 4. Akun Kasubbag Hukum
        User::create([
            'nip' => '197504042000011004',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Dr. Hendra Gunawan',
            'role' => 'Kasubbag'
        ]);

        // 5. Akun Kabag Hukum (Kepala Bagian)
        User::create([
            'nip' => '197005051995011005',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Drs. Ahmad Dahlan, M.Si.',
            'role' => 'Kabag'
        ]);

        // 6. Akun Super Admin
        User::create([
            'nip' => 'admin', // Menggunakan NIP sederhana untuk kemudahan login Super Admin
            'password' => Hash::make('superadmin'),
            'nama_lengkap' => 'Administrator Sistem',
            'role' => 'Super Admin'
        ]);
        
        $this->command->info('Database berhasil di-seed dengan akun dummy (Password default: password123)');
    }
}