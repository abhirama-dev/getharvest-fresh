<?php

namespace App\Models;

use CodeIgniter\Model;

class EscrowModel extends Model
{
    protected $table            = 'escrow';
    protected $primaryKey       = 'id_escrow';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_pesanan',
        'jumlah_escrow',
        'status',
        'tanggal_dilepas',
    ];

    protected $useTimestamps = false;

    public function getByPesanan(int $idPesanan)
    {
        return $this->where('id_pesanan', $idPesanan)->first();
    }

    public function totalDitahanByPetani(int $idPetani): int
    {
        $result = $this->select('SUM(escrow.jumlah_escrow) AS total')
            ->join('pesanan', 'pesanan.id_pesanan = escrow.id_pesanan')
            ->join('produk', 'produk.id_produk = pesanan.id_produk')
            ->where('produk.id_petani', $idPetani)
            ->where('escrow.status', 'ditahan')
            ->first();
        return (int) ($result['total'] ?? 0);
    }

    public function lepasDana(int $idEscrow)
    {
        return $this->update($idEscrow, [
            'status'          => 'dilepas',
            'tanggal_dilepas' => date('Y-m-d H:i:s'),
        ]);
    }

    public function kembalikanDana(int $idEscrow)
    {
        return $this->update($idEscrow, [
            'status'          => 'dikembalikan',
            'tanggal_dilepas' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getByPetani(int $idPetani)
    {
        return $this->select('escrow.*, pesanan.id_pedagang, produk.nama_produk, produk.gambar_produk,
            users.nama_lengkap AS nama_pedagang')
            ->join('pesanan', 'pesanan.id_pesanan = escrow.id_pesanan')
            ->join('produk', 'produk.id_produk = pesanan.id_produk')
            ->join('users', 'users.id_user = pesanan.id_pedagang')
            ->where('produk.id_petani', $idPetani)
            ->orderBy('escrow.tanggal_ditahan', 'DESC')
            ->findAll();
    }
    
}
