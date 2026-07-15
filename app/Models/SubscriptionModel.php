<?php

namespace App\Models;

use CodeIgniter\Model;

class SubscriptionModel extends Model
{
    protected $table            = 'subscriptions';
    protected $primaryKey       = 'id_subscription';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_user', 'tipe', 'tanggal_mulai', 'tanggal_akhir', 'status',
    ];

    protected $useTimestamps = false;

    public function getAktif(int $idUser)
    {
        return $this->where('id_user', $idUser)
                    ->where('status', 'aktif')
                    ->orderBy('tanggal_akhir', 'DESC')
                    ->first();
    }

    public function buatSubscription(int $idUser, string $tipe)
    {
        $mulai = date('Y-m-d');
        $akhir = $tipe === 'tahunan'
            ? date('Y-m-d', strtotime('+1 year'))
            : date('Y-m-d', strtotime('+1 month'));

        return $this->insert([
            'id_user'       => $idUser,
            'tipe'          => $tipe,
            'tanggal_mulai' => $mulai,
            'tanggal_akhir' => $akhir,
            'status'        => 'aktif',
        ]);
    }
}