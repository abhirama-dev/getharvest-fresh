<?php

namespace App\Controllers\Pedagang;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\RatingReviewModel;
use App\Models\RekeningModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class KatalogController extends BaseController
{
    /**
     * Grid katalog produk untuk pedagang, lengkap dengan filter & rata-rata rating petani.
     */
    public function index()
    {
        $produkModel = new ProdukModel();

        $keyword  = $this->request->getGet('q');
        $kategori = $this->request->getGet('kategori');
        $grade    = $this->request->getGet('grade');
        $status   = $this->request->getGet('status');

        $builder = $produkModel
            ->select('
                produk.*,
                users.nama_lengkap AS nama_petani,
                users.alamat_toko,
                ROUND(AVG(rating_review.rating), 1) AS rata_rating,
                COUNT(DISTINCT rating_review.id_rating) AS jumlah_rating
            ')
            ->join('users', 'users.id_user = produk.id_petani')
            ->join('pesanan', 'pesanan.id_produk = produk.id_produk', 'left')
            ->join('rating_review', 'rating_review.id_pesanan = pesanan.id_pesanan', 'left')
            ->where('produk.stok_kg >', 0)
            ->groupBy('produk.id_produk');

        if (!empty($keyword)) {
            $builder->like('produk.nama_produk', $keyword);
        }
        if (!empty($kategori)) {
            $builder->where('produk.kategori', $kategori);
        }
        if (!empty($grade)) {
            $builder->where('produk.grade', $grade);
        }
        if (!empty($status)) {
            $builder->where('produk.status_panen', $status);
        }

        $produk = $builder->orderBy('produk.created_at', 'DESC')->findAll();

        $kategoriList = $produkModel
            ->distinct()
            ->select('kategori')
            ->where('kategori IS NOT NULL')
            ->orderBy('kategori', 'ASC')
            ->findAll();

        return view('pedagang/katalog/index', [
            'title'        => 'Katalog Produk',
            'produk'       => $produk,
            'kategoriList' => $kategoriList,
            'keyword'      => $keyword,
            'kategori'     => $kategori,
            'grade'        => $grade,
            'status'       => $status,
        ]);
    }

    /**
     * Detail satu produk: info lengkap, rating & ulasan, serta rekening petani yang sudah tervalidasi
     * (dipakai untuk pilihan tujuan pembayaran saat Beli).
     */
    public function show(int $id)
    {
        $produkModel = new ProdukModel();
        $ratingModel = new RatingReviewModel();
        $rekeningModel = new RekeningModel();

        $produk = $produkModel
            ->select('produk.*, users.nama_lengkap AS nama_petani, users.alamat_toko, users.foto_toko, users.no_hp')
            ->join('users', 'users.id_user = produk.id_petani')
            ->where('produk.id_produk', $id)
            ->first();

        if (!$produk) {
            throw PageNotFoundException::forPageNotFound('Produk tidak ditemukan');
        }

        $rataRating = $ratingModel
            ->select('ROUND(AVG(rating_review.rating), 1) AS rata_rating, COUNT(rating_review.id_rating) AS jumlah_rating')
            ->join('pesanan', 'pesanan.id_pesanan = rating_review.id_pesanan')
            ->where('pesanan.id_produk', $id)
            ->first();

        $ulasan = $ratingModel
            ->select('rating_review.*, users.nama_lengkap AS nama_pemberi')
            ->join('pesanan', 'pesanan.id_pesanan = rating_review.id_pesanan')
            ->join('users', 'users.id_user = rating_review.id_pemberi')
            ->where('pesanan.id_produk', $id)
            ->orderBy('rating_review.tanggal', 'DESC')
            ->limit(10)
            ->find();

        // Produk lain dari petani yang sama, untuk rekomendasi di bawah detail
        $produkLain = $produkModel
            ->where('id_petani', $produk['id_petani'])
            ->where('id_produk !=', $id)
            ->where('stok_kg >', 0)
            ->limit(4)
            ->find();

        $rekeningTervalidasi = $rekeningModel
            ->where('id_user', $produk['id_petani'])
            ->where('status_validasi', 'verified')
            ->findAll();

        return view('pedagang/katalog/show', [
            'title'               => $produk['nama_produk'],
            'produk'              => $produk,
            'rataRating'          => $rataRating,
            'ulasan'              => $ulasan,
            'produkLain'          => $produkLain,
            'rekeningTervalidasi' => $rekeningTervalidasi,
        ]);
    }
}