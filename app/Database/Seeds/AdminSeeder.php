<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'nama_lengkap'      => 'Administrator GetHarvest',
            'alamat'            => 'Kantor Pusat GetHarvest',
            'email'             => 'admin@getharvest.test',
            'password'          => password_hash('admin123', PASSWORD_DEFAULT),
            'role'              => 'admin',
            'no_hp'             => '081234567890',
            'is_premium'        => 0,
            'status_verifikasi' => 'disetujui',
        ];

        $this->db->table('users')->insert($data);

        $rekAdmin = [
            [
                'tipe'           => 'bank',
                'nama_bank'      => 'Bank BCA',
                'nomor_rekening' => '1234567890',
                'atas_nama'      => 'PT GetHarvest Indonesia',
            ],
            [
                'tipe'           => 'e_wallet',
                'nama_bank'      => 'OVO',
                'nomor_rekening' => '081234567890',
                'atas_nama'      => 'PT GetHarvest Indonesia',
            ],
        ];

        $this->db->table('rekening_admin')->insertBatch($rekAdmin);
    }
}