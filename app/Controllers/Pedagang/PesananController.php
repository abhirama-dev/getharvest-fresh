<?php

namespace App\Controllers\Pedagang;

use App\Controllers\BaseController;
use App\Models\PesananModel;
use App\Models\EscrowModel;
use App\Models\ProdukModel;
use App\Models\UserModel;
use App\Models\NotifikasiModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class PesananController extends BaseController
{
    public function index()
    {
        $idPedagang = session()->get('id_user');

        $pesananModel = new PesananModel();
        $status = $this->request->getGet('status');

        $builder = $pesananModel
            ->select('pesanan.*, produk.nama_produk, produk.gambar_produk, users.nama_lengkap AS nama_petani')
            ->join('produk', 'produk.id_produk = pesanan.id_produk')
            ->join('users', 'users.id_user = produk.id_petani')
            ->where('pesanan.id_pedagang', $idPedagang);

        if (!empty($status)) {
            $builder->where('pesanan.status_pengiriman', $status);
        }

        $pesanan = $builder->orderBy('pesanan.tanggal_pesan', 'DESC')->find();

        return view('pedagang/pesanan/index', [
            'title'   => 'Riwayat Belanja',
            'pesanan' => $pesanan,
            'status'  => $status,
        ]);
    }

    /**
     * Dipanggil saat pedagang menekan "Konfirmasi Terima" pada pesanan berstatus Dikirim.
     * Melepas dana escrow ke petani & menandai pesanan Selesai.
     */
    public function konfirmasiTerima(int $idPesanan)
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

        if ($pesanan['status_pengiriman'] !== 'Dikirim') {
            return redirect()->back()->with('error', 'Pesanan belum berada pada status Dikirim.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $pesananModel->update($idPesanan, [
            'status_pengiriman' => 'Selesai',
            'status_escrow'     => 'dilepas',
        ]);

        $escrowModel = new EscrowModel();
        $escrowModel
            ->where('id_pesanan', $idPesanan)
            ->set([
                'status'          => 'dilepas',
                'tanggal_dilepas' => date('Y-m-d H:i:s'),
            ])
            ->update();

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memproses konfirmasi, silakan coba lagi.');
        }

        // Notifikasi + email ke petani bahwa dana sudah dilepas
        $produkModel = new ProdukModel();
        $produk = $produkModel->find($pesanan['id_produk']);
        $petani = model('UserModel')->find($produk['id_petani']);

        $judul = 'Dana Escrow Telah Dilepas';
        $pesan = 'Pembeli telah mengonfirmasi penerimaan pesanan #' . $idPesanan . '. Dana sebesar '
            . format_rupiah($pesanan['total_harga']) . ' telah diteruskan ke rekening Anda.';

        model('NotifikasiModel')->kirim($produk['id_petani'], $judul, $pesan);
        if (!empty($petani['email'])) {
            kirim_email_notifikasi($petani['email'], $judul, $pesan);
        }

        return redirect()->to('pedagang/pesanan')->with('success', 'Pesanan diselesaikan. Terima kasih!');
    }
}