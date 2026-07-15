<?php

namespace App\Controllers\Pedagang;

use App\Controllers\BaseController;
use App\Models\PesananModel;
use App\Models\ProdukModel;
use App\Models\NegoHargaModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $idPedagang = session()->get('id_user');

        $pesananModel = new PesananModel();
        $produkModel  = new ProdukModel();
        $negoModel    = new NegoHargaModel();

        // Total belanja: akumulasi pesanan yang sudah dibayar (tidak termasuk yang masih Menunggu/dibatalkan)
        $totalBelanja = $pesananModel
            ->where('id_pedagang', $idPedagang)
            ->whereNotIn('status_pengiriman', ['Menunggu', 'Retur'])
            ->selectSum('total_harga')
            ->first()['total_harga'] ?? 0;

        // Produk tersedia di seluruh katalog (stok > 0)
        $produkTersedia = $produkModel->where('stok_kg >', 0)->countAllResults();

        // Nego yang masih berjalan milik pedagang ini
        $negoAktif = $negoModel
            ->where('id_pedagang', $idPedagang)
            ->whereIn('status_nego', ['Menunggu', 'Dibalas'])
            ->countAllResults();

        // Pesanan yang masih dalam proses (belum selesai/retur)
        $pesananAktif = $pesananModel
            ->where('id_pedagang', $idPedagang)
            ->whereNotIn('status_pengiriman', ['Selesai', 'Retur'])
            ->countAllResults();

        // 5 aktivitas pesanan terbaru
        $riwayatTerbaru = $pesananModel
            ->select('pesanan.*, produk.nama_produk, produk.gambar_produk, produk.id_petani, users.nama_lengkap AS nama_petani')
            ->join('produk', 'produk.id_produk = pesanan.id_produk')
            ->join('users', 'users.id_user = produk.id_petani')
            ->where('pesanan.id_pedagang', $idPedagang)
            ->orderBy('pesanan.tanggal_pesan', 'DESC')
            ->limit(5)
            ->find();

        // 3 nego terbaru yang masih menunggu respon
        $negoTerbaru = $negoModel
            ->select('nego_harga.*, produk.nama_produk, produk.gambar_produk')
            ->join('produk', 'produk.id_produk = nego_harga.id_produk')
            ->where('nego_harga.id_pedagang', $idPedagang)
            ->whereIn('nego_harga.status_nego', ['Menunggu', 'Dibalas'])
            ->orderBy('nego_harga.tanggal_nego', 'DESC')
            ->limit(3)
            ->find();

        return view('pedagang/dashboard', [
            'title'          => 'Dashboard Pedagang',
            'totalBelanja'   => $totalBelanja,
            'produkTersedia' => $produkTersedia,
            'negoAktif'      => $negoAktif,
            'pesananAktif'   => $pesananAktif,
            'riwayatTerbaru' => $riwayatTerbaru,
            'negoTerbaru'    => $negoTerbaru,
        ]);
    }
}