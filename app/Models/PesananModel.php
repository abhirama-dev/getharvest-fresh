<?php

namespace App\Models;

use CodeIgniter\Model;

class PesananModel extends Model
{
    protected $table            = 'pesanan';
    protected $primaryKey       = 'id_pesanan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_pedagang', 'id_produk', 'jumlah_kg', 'total_harga', 'status_pengiriman',
        'bukti_bayar', 'nomor_resi', 'metode_pembayaran', 'status_escrow',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'jumlah_kg'   => 'required|numeric|greater_than[0]',
        'total_harga' => 'required|numeric|greater_than[0]',
    ];

    public function getByPedagang(int $idPedagang)
    {
        return $this->select('pesanan.*, produk.nama_produk, produk.gambar_produk, produk.id_petani, users.nama_lengkap AS nama_petani')
                    ->join('produk', 'produk.id_produk = pesanan.id_produk')
                    ->join('users', 'users.id_user = produk.id_petani')
                    ->where('pesanan.id_pedagang', $idPedagang)
                    ->orderBy('pesanan.tanggal_pesan', 'DESC')
                    ->findAll();
    }

    public function getByPetani(int $idPetani)
    {
        return $this->select('pesanan.*, produk.nama_produk, produk.gambar_produk, users.nama_lengkap AS nama_pedagang, users.no_hp')
                    ->join('produk', 'produk.id_produk = pesanan.id_produk')
                    ->join('users', 'users.id_user = pesanan.id_pedagang')
                    ->where('produk.id_petani', $idPetani)
                    ->orderBy('pesanan.tanggal_pesan', 'DESC')
                    ->findAll();
    }

    public function getDetail(int $idPesanan)
    {
        return $this->select('pesanan.*, produk.nama_produk, produk.gambar_produk, produk.id_petani,
                pedagang.nama_lengkap AS nama_pedagang, pedagang.no_hp AS hp_pedagang,
                petani.nama_lengkap AS nama_petani, petani.no_hp AS hp_petani')
                    ->join('produk', 'produk.id_produk = pesanan.id_produk')
                    ->join('users AS pedagang', 'pedagang.id_user = pesanan.id_pedagang')
                    ->join('users AS petani', 'petani.id_user = produk.id_petani')
                    ->where('pesanan.id_pesanan', $idPesanan)
                    ->first();
    }

    public function totalPendapatanPetani(int $idPetani): int
    {
        $result = $this->select('SUM(pesanan.total_harga) AS total')
                        ->join('produk', 'produk.id_produk = pesanan.id_produk')
                        ->where('produk.id_petani', $idPetani)
                        ->where('pesanan.status_pengiriman', 'Selesai')
                        ->first();
        return (int) ($result['total'] ?? 0);
    }

    public function totalBelanjaPedagang(int $idPedagang): int
    {
        $result = $this->selectSum('total_harga', 'total')
                        ->where('id_pedagang', $idPedagang)
                        ->where('status_pengiriman', 'Selesai')
                        ->first();
        return (int) ($result['total'] ?? 0);
    }

    public function cariByResi(string $resi, int $idPedagang)
{
    return $this->select('pesanan.*, produk.nama_produk, produk.gambar_produk,
            users.nama_lengkap AS nama_petani, users.no_hp AS hp_petani')
                ->join('produk', 'produk.id_produk = pesanan.id_produk')
                ->join('users', 'users.id_user = produk.id_petani')
                ->where('pesanan.nomor_resi', $resi)
                ->where('pesanan.id_pedagang', $idPedagang)
                ->first();
}

public function getSedangDikirim(int $idPedagang)
{
    return $this->select('pesanan.*, produk.nama_produk, produk.gambar_produk,
            users.nama_lengkap AS nama_petani')
                ->join('produk', 'produk.id_produk = pesanan.id_produk')
                ->join('users', 'users.id_user = produk.id_petani')
                ->where('pesanan.id_pedagang', $idPedagang)
                ->whereIn('pesanan.status_pengiriman', ['Dikemas', 'Dikirim'])
                ->orderBy('pesanan.tanggal_pesan', 'DESC')
                ->findAll();
}
}