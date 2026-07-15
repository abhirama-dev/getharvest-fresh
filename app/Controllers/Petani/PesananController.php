<?php

namespace App\Controllers\Petani;

use App\Controllers\BaseController;
use App\Models\PesananModel;
use App\Models\ProdukModel;
use App\Models\UserModel;
use App\Models\NotifikasiModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class PesananController extends BaseController
{
    public function index()
    {
        $idPetani = session()->get('id_user');
        $status = $this->request->getGet('status');

        $pesananModel = new PesananModel();

        $builder = $pesananModel
            ->select('pesanan.*, produk.nama_produk, produk.gambar_produk, users.nama_lengkap AS nama_pedagang, users.no_hp')
            ->join('produk', 'produk.id_produk = pesanan.id_produk')
            ->join('users', 'users.id_user = pesanan.id_pedagang')
            ->where('produk.id_petani', $idPetani)
            ->where('pesanan.status_pengiriman !=', 'Menunggu'); // hanya yang sudah dibayar ke atas

        if (!empty($status)) {
            $builder->where('pesanan.status_pengiriman', $status);
        }

        $pesanan = $builder->orderBy('pesanan.tanggal_pesan', 'DESC')->find();

        return view('petani/pesanan/index', [
            'title'   => 'Pesanan Masuk',
            'pesanan' => $pesanan,
            'status'  => $status,
        ]);
    }

    /**
     * Ubah status Dibayar -> Dikemas
     */
    public function kemas(int $idPesanan)
    {
        $pesanan = $this->pesananMilikSaya($idPesanan);

        if ($pesanan['status_pengiriman'] !== 'Dibayar') {
            return redirect()->back()->with('error', 'Pesanan belum berada pada status Dibayar.');
        }

        (new PesananModel())->update($idPesanan, ['status_pengiriman' => 'Dikemas']);

        return redirect()->back()->with('success', 'Pesanan ditandai sedang dikemas.');
    }

    /**
     * Ubah status Dikemas -> Dikirim, wajib input nomor resi. Kirim notifikasi ke pedagang.
     */
    public function kirim(int $idPesanan)
    {
        $pesanan = $this->pesananMilikSaya($idPesanan);

        if ($pesanan['status_pengiriman'] !== 'Dikemas') {
            return redirect()->back()->with('error', 'Pesanan belum berada pada status Dikemas.');
        }

        $rules = ['nomor_resi' => 'required|min_length[3]|max_length[50]'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $pesananModel = new PesananModel();
        $pesananModel->update($idPesanan, [
            'status_pengiriman' => 'Dikirim',
            'nomor_resi'        => $this->request->getPost('nomor_resi'),
        ]);

        $pedagang = model('UserModel')->find($pesanan['id_pedagang']);
        $judul = 'Pesanan Anda Sedang Dikirim';
        $pesan = 'Pesanan #' . $idPesanan . ' sudah dikirim dengan nomor resi '
            . $this->request->getPost('nomor_resi') . '. Segera konfirmasi setelah barang diterima.';

        model('NotifikasiModel')->kirim($pesanan['id_pedagang'], $judul, $pesan);
        if (!empty($pedagang['email'])) {
            kirim_email_notifikasi($pedagang['email'], $judul, $pesan);
        }

        return redirect()->back()->with('success', 'Nomor resi disimpan, pesanan ditandai Dikirim.');
    }

    private function pesananMilikSaya(int $idPesanan): array
    {
        $idPetani = session()->get('id_user');

        $pesanan = (new PesananModel())
            ->select('pesanan.*, produk.id_petani')
            ->join('produk', 'produk.id_produk = pesanan.id_produk')
            ->where('pesanan.id_pesanan', $idPesanan)
            ->where('produk.id_petani', $idPetani)
            ->first();

        if (!$pesanan) {
            throw PageNotFoundException::forPageNotFound('Pesanan tidak ditemukan');
        }

        return $pesanan;
    }
    
}  