<?php

namespace App\Libraries;

use App\Models\NegoHargaModel;
use App\Models\ProdukModel;
use App\Models\PesananModel;
use App\Models\UserModel;
use App\Models\NotifikasiModel;

/**
 * Logika bersama untuk alur negosiasi multi-putaran, dipakai oleh
 * Petani\NegoController maupun Pedagang\NegoController agar aksi
 * "terima" konsisten dari sisi manapun tawaran itu disetujui.
 */
class NegoService
{
    /**
     * Menerima tawaran nego yang sedang berjalan -> membuat pesanan baru (status Menunggu,
     * pedagang masih harus bayar via alur Pembelian) dan mengurangi stok produk.
     *
     * @throws \RuntimeException jika stok tidak cukup
     */
    public function terima(array $nego): int
    {
        $produkModel = new ProdukModel();
        $produk = $produkModel->find($nego['id_produk']);

        if (!$produk || $produk['stok_kg'] < $nego['jumlah_kebutuhan_kg']) {
            throw new \RuntimeException('Stok produk tidak mencukupi untuk menyetujui tawaran ini.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $produkModel->update($produk['id_produk'], [
            'stok_kg' => $produk['stok_kg'] - $nego['jumlah_kebutuhan_kg'],
        ]);

        $pesananModel = new PesananModel();
        $idPesanan = $pesananModel->insert([
            'id_pedagang'       => $nego['id_pedagang'],
            'id_produk'         => $nego['id_produk'],
            'jumlah_kg'         => $nego['jumlah_kebutuhan_kg'],
            'total_harga'       => $nego['jumlah_kebutuhan_kg'] * $nego['harga_tawaran'],
            'status_pengiriman' => 'Menunggu',
            'status_escrow'     => 'ditahan',
        ]);

        (new NegoHargaModel())->update($nego['id_nego'], ['status_nego' => 'Diterima']);

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \RuntimeException('Gagal memproses persetujuan nego, silakan coba lagi.');
        }

        // Beri tahu pedagang bahwa nego diterima & harus segera bayar
        $judul = 'Tawaran Nego Diterima';
        $pesan = 'Tawaran Anda untuk produk "' . $produk['nama_produk'] . '" sejumlah '
            . $nego['jumlah_kebutuhan_kg'] . ' kg telah diterima. Silakan selesaikan pembayaran.';

        model('NotifikasiModel')->kirim($nego['id_pedagang'], $judul, $pesan);
        $pedagang = model('UserModel')->find($nego['id_pedagang']);
        if (!empty($pedagang['email'])) {
            kirim_email_notifikasi($pedagang['email'], $judul, $pesan);
        }

        return $idPesanan;
    }

    /**
     * Menolak tawaran yang sedang berjalan.
     */
    public function tolak(array $nego, string $penerimaNotifId, string $namaProduk): void
    {
        (new NegoHargaModel())->update($nego['id_nego'], ['status_nego' => 'Ditolak']);

        $judul = 'Tawaran Nego Ditolak';
        $pesan = 'Tawaran untuk produk "' . $namaProduk . '" sejumlah ' . $nego['jumlah_kebutuhan_kg']
            . ' kg dengan harga Rp' . number_format($nego['harga_tawaran']) . '/kg telah ditolak.';

        model('NotifikasiModel')->kirim((int) $penerimaNotifId, $judul, $pesan);
    }

    /**
     * Membuat entri nego baru sebagai balasan (nego balik), menutup nego lama menjadi "Dibalas".
     */
    public function balas(array $negoLama, int $hargaBaru, int $jumlahBaru, string $pihakBerikutnya): int
    {
        $negoModel = new NegoHargaModel();

        $db = \Config\Database::connect();
        $db->transStart();

        $negoModel->update($negoLama['id_nego'], ['status_nego' => 'Dibalas']);

        $idNegoBaru = $negoModel->insert([
            'id_pedagang'         => $negoLama['id_pedagang'],
            'id_produk'           => $negoLama['id_produk'],
            'jumlah_kebutuhan_kg' => $jumlahBaru,
            'harga_tawaran'       => $hargaBaru,
            'status_nego'         => 'Menunggu',
            'pihak_selanjutnya'   => $pihakBerikutnya,
            'nego_induk'          => $negoLama['id_nego'],
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \RuntimeException('Gagal mengirim balasan nego, silakan coba lagi.');
        }

        return $idNegoBaru;
    }
}