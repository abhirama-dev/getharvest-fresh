<?php

namespace App\Models;

use CodeIgniter\Model;

class ReturModel extends Model
{
    protected $table            = 'retur';
    protected $primaryKey       = 'id_retur';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_pesanan', 'alasan', 'foto_bukti', 'status', 'tanggal_selesai',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'alasan' => 'required|min_length[10]',
    ];

    public function getDetail(int $idRetur)
    {
        return $this->select('retur.*, pesanan.id_produk, pesanan.jumlah_kg, pesanan.total_harga, pesanan.id_pedagang,
                produk.nama_produk, produk.id_petani')
                    ->join('pesanan', 'pesanan.id_pesanan = retur.id_pesanan')
                    ->join('produk', 'produk.id_produk = pesanan.id_produk')
                    ->where('retur.id_retur', $idRetur)
                    ->first();
    }

    public function getByPetani(int $idPetani)
    {
        return $this->select('retur.*, pesanan.id_produk, produk.nama_produk, users.nama_lengkap AS nama_pedagang')
                    ->join('pesanan', 'pesanan.id_pesanan = retur.id_pesanan')
                    ->join('produk', 'produk.id_produk = pesanan.id_produk')
                    ->join('users', 'users.id_user = pesanan.id_pedagang')
                    ->where('produk.id_petani', $idPetani)
                    ->orderBy('retur.tanggal_pengajuan', 'DESC')
                    ->findAll();
    }
}