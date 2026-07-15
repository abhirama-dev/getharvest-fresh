<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class TransaksiController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $status = $this->request->getGet('status') ?: '';

        $builder = $db->table('pesanan')
            ->select('pesanan.*, produk.nama_produk, pedagang.nama_lengkap AS nama_pedagang, petani.nama_lengkap AS nama_petani')
            ->join('produk', 'produk.id_produk = pesanan.id_produk')
            ->join('users AS pedagang', 'pedagang.id_user = pesanan.id_pedagang')
            ->join('users AS petani', 'petani.id_user = produk.id_petani');

        if ($status) {
            $builder->where('pesanan.status_pengiriman', $status);
        }

        $transaksi = $builder->orderBy('pesanan.tanggal_pesan', 'DESC')->get()->getResultArray();

        $ringkasan = $db->table('pesanan')
            ->selectSum('total_harga', 'total')
            ->where('status_pengiriman', 'Selesai')
            ->get()->getRowArray();

        return view('admin/transaksi/index', [
            'pageTitle'      => 'Data Transaksi',
            'pageSubtitle'   => 'Rekapitulasi seluruh transaksi platform',
            'transaksi'      => $transaksi,
            'totalOmzet'     => (int) ($ringkasan['total'] ?? 0),
            'filterStatus'   => $status,
        ]);
    }
}