<?php

namespace App\Models;

use CodeIgniter\Model;

class NotifikasiModel extends Model
{
    protected $table            = 'notifikasi';
    protected $primaryKey       = 'id_notif';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_user', 'judul', 'pesan', 'is_read',
    ];

    protected $useTimestamps = false;

    /**
     * Simpan notifikasi baru
     */
    public function kirim(int $idUser, string $judul, string $pesan)
    {
        return $this->insert([
            'id_user' => $idUser,
            'judul'   => $judul,
            'pesan'   => $pesan,
            'is_read' => 0,
        ]);
    }

    public function getByUser(int $idUser, int $limit = 10)
    {
        return $this->where('id_user', $idUser)
                    ->orderBy('created_at', 'DESC')
                    ->findAll($limit);
    }

    public function countUnread(int $idUser): int
    {
        return $this->where('id_user', $idUser)
                    ->where('is_read', 0)
                    ->countAllResults();
    }

    public function markAllRead(int $idUser)
    {
        return $this->where('id_user', $idUser)
                    ->set('is_read', 1)
                    ->update();
    }
}