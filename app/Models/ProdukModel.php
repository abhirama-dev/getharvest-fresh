<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    protected $table            = 'produk';
    protected $primaryKey       = 'id_produk';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_petani', 'nama_produk', 'kategori', 'harga_per_kg', 'stok_kg',
        'status_panen', 'tanggal_estimasi_panen', 'gambar_produk', 'grade', 'sertifikat',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'nama_produk'   => 'required|min_length[3]|max_length[100]',
        'kategori'      => 'permit_empty|max_length[50]',
        'harga_per_kg'  => 'required|numeric|greater_than[0]',
        'stok_kg'       => 'required|numeric|greater_than_equal_to[0]',
        'status_panen'  => 'required|in_list[Siap Jual,Pre-Order]',
        'grade'         => 'permit_empty|in_list[A,B,C,Organik,Biasa]',
    ];

    /**
     * Katalog produk untuk pedagang, lengkap dengan rata-rata rating petani
     */
    public function getKatalog(array $filters = [])
    {
        $builder = $this->select('
                produk.*,
                users.nama_lengkap AS nama_petani,
                users.alamat_toko,
                (SELECT ROUND(AVG(rr.rating),1)
                    FROM rating_review rr
                    WHERE rr.id_penerima = produk.id_petani) AS rata_rating,
                (SELECT COUNT(*)
                    FROM rating_review rr
                    WHERE rr.id_penerima = produk.id_petani) AS jumlah_rating
            ')
            ->join('users', 'users.id_user = produk.id_petani')
            ->where('produk.stok_kg >', 0);

        if (! empty($filters['kategori'])) {
            $builder->where('produk.kategori', $filters['kategori']);
        }
        if (! empty($filters['grade'])) {
            $builder->where('produk.grade', $filters['grade']);
        }
        if (! empty($filters['keyword'])) {
            $builder->like('produk.nama_produk', $filters['keyword']);
        }

        return $builder->orderBy('produk.created_at', 'DESC')->findAll();
    }

    public function getByPetani(int $idPetani)
    {
        return $this->where('id_petani', $idPetani)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getDetailProduk(int $idProduk)
    {
        return $this->select('produk.*, users.nama_lengkap AS nama_petani, users.alamat_toko, users.no_hp')
                    ->join('users', 'users.id_user = produk.id_petani')
                    ->where('produk.id_produk', $idProduk)
                    ->first();
    }

    /**
     * Kurangi stok (dipanggil dalam DB transaction)
     */
    public function kurangiStok(int $idProduk, int $jumlah): bool
    {
        $produk = $this->find($idProduk);
        if (! $produk || $produk['stok_kg'] < $jumlah) {
            return false;
        }
        return $this->update($idProduk, ['stok_kg' => $produk['stok_kg'] - $jumlah]);
    }

    /**
     * Kembalikan stok (retur/batal)
     */
    public function tambahStok(int $idProduk, int $jumlah): bool
    {
        $produk = $this->find($idProduk);
        if (! $produk) {
            return false;
        }
        return $this->update($idProduk, ['stok_kg' => $produk['stok_kg'] + $jumlah]);
    }
}