<?php

namespace App\Models;

use CodeIgniter\Model;

class NegoHargaModel extends Model
{
    protected $table            = 'nego_harga';
    protected $primaryKey       = 'id_nego';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_pedagang', 'id_produk', 'jumlah_kebutuhan_kg', 'harga_tawaran',
        'status_nego', 'pihak_selanjutnya', 'nego_induk',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'jumlah_kebutuhan_kg' => 'required|numeric|greater_than[0]',
        'harga_tawaran'       => 'required|numeric|greater_than[0]',
    ];

    /**
     * Nego aktif milik pedagang (belum final)
     */
    public function getByPedagang(int $idPedagang)
    {
        return $this->select('nego_harga.*, produk.nama_produk, produk.gambar_produk, produk.harga_per_kg AS harga_awal,
                produk.id_petani, users.nama_lengkap AS nama_petani')
                    ->join('produk', 'produk.id_produk = nego_harga.id_produk')
                    ->join('users', 'users.id_user = produk.id_petani')
                    ->where('nego_harga.id_pedagang', $idPedagang)
                    ->orderBy('nego_harga.tanggal_nego', 'DESC')
                    ->findAll();
    }

    /**
     * Semua nego untuk produk milik petani tertentu
     */
    public function getByPetani(int $idPetani)
    {
        return $this->select('nego_harga.*, produk.nama_produk, produk.gambar_produk, produk.harga_per_kg AS harga_awal,
                produk.stok_kg, users.nama_lengkap AS nama_pedagang')
                    ->join('produk', 'produk.id_produk = nego_harga.id_produk')
                    ->join('users', 'users.id_user = nego_harga.id_pedagang')
                    ->where('produk.id_petani', $idPetani)
                    ->orderBy('nego_harga.tanggal_nego', 'DESC')
                    ->findAll();
    }

    public function countAktifByPedagang(int $idPedagang): int
    {
        return $this->where('id_pedagang', $idPedagang)
                    ->whereIn('status_nego', ['Menunggu', 'Dibalas'])
                    ->countAllResults();
    }

    /**
     * Rantai riwayat nego (dari induk hingga balasan terbaru)
     */
    public function getRiwayatChain(int $idNego)
    {
        $chain = [];
        $current = $this->find($idNego);

        // mundur ke induk paling awal
        while ($current && $current['nego_induk']) {
            $current = $this->find($current['nego_induk']);
        }

        // maju kumpulkan seluruh rantai
        while ($current) {
            $chain[] = $current;
            $current = $this->where('nego_induk', $current['id_nego'])->first();
        }

        return $chain;
    }
}