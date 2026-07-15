<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EscrowModel;
use App\Models\PermintaanUpgradeModel;
use App\Models\PesananModel;
use App\Models\RekeningModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $userModel     = new UserModel();
        $pesananModel  = new PesananModel();
        $escrowModel   = new EscrowModel();
        $rekeningModel = new RekeningModel();
        $upgradeModel  = new PermintaanUpgradeModel();

        $totalPetani   = $userModel->where('role', 'petani')->countAllResults();
        $totalPedagang = $userModel->where('role', 'pedagang')->where('status_verifikasi', 'disetujui')->countAllResults();
        $pedagangPending = $userModel->getPedagangPending();

        $totalTransaksi = $pesananModel->selectSum('total_harga', 'total')
            ->where('status_pengiriman', 'Selesai')->first();

        $totalEscrowDitahan = $escrowModel->selectSum('jumlah_escrow', 'total')
            ->where('status', 'ditahan')->first();

        $rekeningPending = $rekeningModel->getPendingValidasi();
        $upgradePending  = $upgradeModel->getPending();

        $db = \Config\Database::connect();
        $pesananTerbaru = $db->table('pesanan')
            ->select('pesanan.*, produk.nama_produk, users.nama_lengkap AS nama_pedagang')
            ->join('produk', 'produk.id_produk = pesanan.id_produk')
            ->join('users', 'users.id_user = pesanan.id_pedagang')
            ->orderBy('pesanan.tanggal_pesan', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        return view('admin/dashboard', [
            'pageTitle'          => 'Dashboard Admin',
            'pageSubtitle'       => 'Ringkasan aktivitas platform GetHarvest',
            'totalPetani'        => $totalPetani,
            'totalPedagang'      => $totalPedagang,
            'jumlahPending'      => count($pedagangPending),
            'totalTransaksi'     => (int) ($totalTransaksi['total'] ?? 0),
            'totalEscrowDitahan' => (int) ($totalEscrowDitahan['total'] ?? 0),
            'jumlahRekeningPending' => count($rekeningPending),
            'jumlahUpgradePending'  => count($upgradePending),
            'pesananTerbaru'     => $pesananTerbaru,
        ]);
    }
}