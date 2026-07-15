<?php

namespace App\Controllers\Petani;

use App\Controllers\BaseController;
use App\Models\PengeluaranTaniModel;
use App\Models\PesananModel;
use App\Models\ProdukModel;
use App\Models\NegoHargaModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $idPetani = session()->get('id_user');

        $pesananModel     = new PesananModel();
        $produkModel      = new ProdukModel();
        $pengeluaranModel = new PengeluaranTaniModel();
        $negoModel        = new NegoHargaModel();

        $totalPendapatan = $pesananModel->totalPendapatanPetani($idPetani);
        $totalModal      = $pengeluaranModel->totalModal($idPetani);
        $produkSaya       = $produkModel->getByPetani($idPetani);
        $jumlahProduk     = count($produkSaya);
        $totalStok        = array_sum(array_column($produkSaya, 'stok_kg'));

        $pesananTerbaru = array_slice($pesananModel->getByPetani($idPetani), 0, 5);
        $negoAktif      = $negoModel->getByPetani($idPetani);
        $negoAktif      = array_filter($negoAktif, fn ($n) => in_array($n['status_nego'], ['Menunggu', 'Dibalas'], true));

        $pesananMenunggu = count(array_filter($pesananTerbaru, fn ($p) => $p['status_pengiriman'] === 'Menunggu'));

        return view('petani/dashboard', [
            'pageTitle'       => 'Dashboard Petani',
            'pageSubtitle'    => 'Ringkasan aktivitas toko Anda',
            'totalPendapatan' => $totalPendapatan,
            'totalModal'      => $totalModal,
            'jumlahProduk'    => $jumlahProduk,
            'totalStok'       => $totalStok,
            'pesananTerbaru'  => $pesananTerbaru,
            'jumlahNegoAktif' => count($negoAktif),
            'pesananMenunggu' => $pesananMenunggu,
        ]);
    }
}