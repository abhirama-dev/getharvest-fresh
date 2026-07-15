<?php

namespace App\Controllers\Pedagang;

use App\Controllers\BaseController;
use App\Models\NotifikasiModel;
use App\Models\PesananModel;
use App\Models\RatingReviewModel;

class RatingController extends BaseController
{
    protected RatingReviewModel $ratingModel;
    protected PesananModel $pesananModel;

    public function __construct()
    {
        $this->ratingModel  = new RatingReviewModel();
        $this->pesananModel = new PesananModel();
    }

    public function form(int $idPesanan)
    {
        $pesanan = $this->cekAkses($idPesanan);
        if ($pesanan instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $pesanan;
        }

        return view('pedagang/rating/form', [
            'pageTitle'    => 'Beri Rating & Ulasan',
            'pageSubtitle' => 'Bagikan pengalaman Anda dengan petani ini',
            'pesanan'      => $pesanan,
        ]);
    }

    public function store(int $idPesanan)
    {
        $pesanan = $this->cekAkses($idPesanan);
        if ($pesanan instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $pesanan;
        }

        $rules = [
            'rating' => 'required|numeric|greater_than[0]|less_than_equal_to[5]',
            'ulasan' => 'permit_empty|max_length[1000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Rating wajib dipilih (1-5 bintang).');
        }

        $this->ratingModel->insert([
            'id_pesanan'  => $idPesanan,
            'id_pemberi'  => session()->get('id_user'),
            'id_penerima' => $pesanan['id_petani'],
            'rating'      => $this->request->getPost('rating'),
            'ulasan'      => $this->request->getPost('ulasan'),
        ]);

        $notifModel = new NotifikasiModel();
        $notifModel->kirim(
            $pesanan['id_petani'],
            'Rating Baru Diterima',
            'Pedagang memberi rating ' . $this->request->getPost('rating') . ' bintang untuk transaksi "' . $pesanan['nama_produk'] . '".'
        );

        return redirect()->to('/pedagang/pesanan')->with('success', 'Terima kasih atas penilaian Anda!');
    }

    private function cekAkses(int $idPesanan)
    {
        $idPedagang = session()->get('id_user');
        $pesanan    = $this->pesananModel->getDetail($idPesanan);

        if (! $pesanan || $pesanan['id_pedagang'] != $idPedagang) {
            return redirect()->to('/pedagang/pesanan')->with('error', 'Pesanan tidak ditemukan.');
        }

        if ($pesanan['status_pengiriman'] !== 'Selesai') {
            return redirect()->to('/pedagang/pesanan')->with('error', 'Rating hanya bisa diberikan setelah pesanan berstatus Selesai.');
        }

        if ($this->ratingModel->sudahDirating($idPesanan)) {
            return redirect()->to('/pedagang/pesanan')->with('error', 'Anda sudah memberi rating untuk pesanan ini.');
        }

        return $pesanan;
    }
}