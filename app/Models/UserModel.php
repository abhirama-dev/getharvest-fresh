<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id_user';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'nama_lengkap',
        'alamat',
        'email',
        'password',
        'role',
        'no_hp',
        'is_premium',
        'status_verifikasi',
        'alamat_toko',
        'foto_toko',
        'koordinat',
        'alasan_tolak',
    ];

    protected $useTimestamps = false; // ditangani oleh MySQL DEFAULT/ON UPDATE

    protected $validationRules = [
        'nama_lengkap' => 'required|min_length[3]|max_length[100]',
        'alamat'       => 'required',
        'email'        => 'required|valid_email|is_unique[users.email,id_user,{id_user}]',
        'password'     => 'permit_empty|min_length[6]',
        'role'         => 'required|in_list[petani,pedagang,admin]',
        'no_hp'        => 'required|min_length[9]|max_length[15]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Email sudah terdaftar, silakan gunakan email lain.',
        ],
    ];

    protected $skipValidation = false;

    /**
     * Cari user berdasarkan email
     */
    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Ambil semua pedagang yang statusnya pending untuk diverifikasi admin
     */
    public function getPedagangPending()
    {
        return $this->where('role', 'pedagang')
            ->where('status_verifikasi', 'pending')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Ambil semua petani
     */
    public function getPetani()
    {
        return $this->where('role', 'petani')->findAll();
    }

    /**
     * Ambil semua pedagang yang sudah disetujui
     */
    public function getPedagangAktif()
    {
        return $this->where('role', 'pedagang')
            ->where('status_verifikasi', 'disetujui')
            ->findAll();
    }

    /**
     * Set status premium user
     */
    public function setPremium(int $idUser, bool $status = true)
    {
        return $this->update($idUser, ['is_premium' => $status ? 1 : 0]);
    }

    public function updateProfil(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }
}
