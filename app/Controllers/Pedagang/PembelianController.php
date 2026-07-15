<?php

namespace App\Controllers\Pedagang;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\PesananModel;
use App\Models\EscrowModel;
use App\Models\RekeningModel;
use App\Models\NotifikasiModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class PembelianController extends BaseController
{
    /**
     * Dipanggil dari modal "Beli" di halaman detail produk.
     * Membuat record pesanan (status Menunggu) dan mengurangi stok produk.
     * Uang belum berpindah tangan di sini — pedagang masih harus transfer & upload bukti.
     */
    public function store()
    {
        $idPedagang = session()->get('id_user');

        $rules = [
            'id_produk' => 'required|is_natural_no_zero',
            'jumlah_kg' => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $idProduk = (int) $this->request->getPost('id_produk');
        $jumlahKg = (int) $this->request->getPost('jumlah_kg');

        $produkModel = new ProdukModel();
        $produk = $produkModel->find($idProduk);

        if (!$produk) {
            throw PageNotFoundException::forPageNotFound('Produk tidak ditemukan');
        }

        if ($jumlahKg > $produk['stok_kg']) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi. Sisa stok: ' . $produk['stok_kg'] . ' kg.');
        }

        // Syarat: petani harus punya minimal 1 rekening tervalidasi agar dana bisa dilepas nanti
        $rekeningModel = new RekeningModel();
        $adaRekeningValid = $rekeningModel
            ->where('id_user', $produk['id_petani'])
            ->where('status_validasi', 'verified')
            ->countAllResults() > 0;

        if (!$adaRekeningValid) {
            return redirect()->back()->with('error', 'Petani belum memiliki rekening tervalidasi. Pembelian tidak dapat dilanjutkan.');
        }

        $pesananModel = new PesananModel();
        $db = \Config\Database::connect();
        $db->transStart();

        // Kurangi stok
        $produkModel->update($idProduk, ['stok_kg' => $produk['stok_kg'] - $jumlahKg]);

        // Buat pesanan
        $idPesanan = $pesananModel->insert([
            'id_pedagang'       => $idPedagang,
            'id_produk'         => $idProduk,
            'jumlah_kg'         => $jumlahKg,
            'total_harga'       => $jumlahKg * $produk['harga_per_kg'],
            'status_pengiriman' => 'Menunggu',
            'status_escrow'     => 'ditahan',
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memproses pesanan, silakan coba lagi.');
        }

        return redirect()->to('pedagang/pembelian/bayar/' . $idPesanan)
            ->with('success', 'Pesanan dibuat. Silakan selesaikan pembayaran.');
    }

    /**
     * Halaman instruksi pembayaran: menampilkan daftar rekening admin (tujuan transfer escrow)
     * dan form upload bukti bayar.
     */
    public function bayar(int $idPesanan)
    {
        $idPedagang = session()->get('id_user');

        $pesananModel = new PesananModel();
        $pesanan = $pesananModel
            ->select('pesanan.*, produk.nama_produk, produk.gambar_produk, users.nama_lengkap AS nama_petani')
            ->join('produk', 'produk.id_produk = pesanan.id_produk')
            ->join('users', 'users.id_user = produk.id_petani')
            ->where('pesanan.id_pesanan', $idPesanan)
            ->where('pesanan.id_pedagang', $idPedagang)
            ->first();

        if (!$pesanan) {
            throw PageNotFoundException::forPageNotFound('Pesanan tidak ditemukan');
        }

        if ($pesanan['status_pengiriman'] !== 'Menunggu') {
            return redirect()->to('pedagang/pesanan')->with('info', 'Pesanan ini sudah diproses sebelumnya.');
        }

        $rekeningAdmin = model('RekeningAdminModel')->findAll();

        return view('pedagang/pembelian/bayar', [
            'title'         => 'Pembayaran Pesanan',
            'pesanan'       => $pesanan,
            'rekeningAdmin' => $rekeningAdmin,
        ]);
    }

    /**
     * Upload bukti transfer -> status pesanan jadi Dibayar, escrow tercatat "ditahan",
     * notifikasi + email dikirim ke petani.
     */
    public function uploadBukti(int $idPesanan)
    {
        $idPedagang = session()->get('id_user');

        $pesananModel = new PesananModel();
        $pesanan = $pesananModel
            ->where('id_pesanan', $idPesanan)
            ->where('id_pedagang', $idPedagang)
            ->first();

        if (!$pesanan) {
            throw PageNotFoundException::forPageNotFound('Pesanan tidak ditemukan');
        }

        if ($pesanan['status_pengiriman'] !== 'Menunggu') {
            return redirect()->to('pedagang/pesanan')->with('info', 'Pesanan ini sudah diproses sebelumnya.');
        }

        $rules = [
            'metode_pembayaran' => 'required|in_list[transfer_bank,e_wallet]',
            'bukti_bayar'       => 'uploaded[bukti_bayar]|is_image[bukti_bayar]|max_size[bukti_bayar,2048]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('bukti_bayar');
        $namaFile = $file->getRandomName();
        $file->move(WRITEPATH . '../public/assets/uploads/bukti_bayar', $namaFile);

        $db = \Config\Database::connect();
        $db->transStart();

        $pesananModel->update($idPesanan, [
            'bukti_bayar'        => $namaFile,
            'metode_pembayaran'  => $this->request->getPost('metode_pembayaran'),
            'status_pengiriman'  => 'Dibayar',
        ]);

        $idEscrow = model('EscrowModel')->insert([
            'id_pesanan'    => $idPesanan,
            'jumlah_escrow' => $pesanan['total_harga'],
            'status'        => 'ditahan',
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal menyimpan bukti pembayaran.');
        }

        // Notifikasi ke petani
        $produkModel = new ProdukModel();
        $produk = $produkModel->find($pesanan['id_produk']);
        $petani = model('UserModel')->find($produk['id_petani']);

        $judul = 'Pesanan Baru Sudah Dibayar';
        $pesan = 'Pesanan #' . $idPesanan . ' untuk produk "' . $produk['nama_produk'] . '" sejumlah '
            . $pesanan['jumlah_kg'] . ' kg telah dibayar. Silakan proses pengemasan.';

        model('NotifikasiModel')->kirim($produk['id_petani'], $judul, $pesan);
        if (!empty($petani['email'])) {
            kirim_email_notifikasi($petani['email'], $judul, $pesan);
        }

        return redirect()->to('pedagang/pesanan')->with('success', 'Pembayaran berhasil dikonfirmasi. Menunggu petani memproses pesanan.');
    }
}