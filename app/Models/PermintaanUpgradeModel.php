<?php

namespace App\Models;

use CodeIgniter\Model;

class PermintaanUpgradeModel extends Model
{
    protected $table            = 'permintaan_upgrade';
    protected $primaryKey       = 'id_permintaan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_user', 'tipe', 'bukti_bayar', 'status', 'alasan_tolak',
    ];

    protected $useTimestamps = false;

    public function getPending()
    {
        return $this->select('permintaan_upgrade.*, users.nama_lengkap, users.email')
                    ->join('users', 'users.id_user = permintaan_upgrade.id_user')
                    ->where('permintaan_upgrade.status', 'Menunggu')
                    ->orderBy('tanggal_permintaan', 'ASC')
                    ->findAll();
    }

    public function getByUser(int $idUser)
    {
        return $this->where('id_user', $idUser)
                    ->orderBy('tanggal_permintaan', 'DESC')
                    ->findAll();
    }
}