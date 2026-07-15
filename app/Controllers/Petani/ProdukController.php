<?php

namespace App\Controllers\Petani;

use App\Controllers\BaseController;
use App\Models\ProdukModel;

class ProdukController extends BaseController
{
    protected ProdukModel $produkModel;

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
    }

    public function index()
    {
        $idPetani = session()->get('id_user');
        $produk   = $this->produkModel->getByPetani($idPetani);

        return view('petani/produk/index', [
            'pageTitle'    => 'Etalase Produk',
            'pageSubtitle' => 'Kelola produk yang Anda jual',
            'produk'       => $produk,
        ]);
    }

    public function create()
    {
        return view('petani/produk/create', [
            'pageTitle'    => 'Tambah Produk',
            'pageSubtitle' => 'Tambahkan hasil panen baru ke etalase Anda',
        ]);
    }

    public function store()
    {
        $rules = [
            'nama_produk'   => 'required|min_length[3]|max_length[100]',
            'kategori'      => 'required|max_length[50]',
            'harga_per_kg'  => 'required|numeric|greater_than[0]',
            'stok_kg'       => 'required|numeric|greater_than[0]',
            'status_panen'  => 'required|in_list[Siap Jual,Pre-Order]',
            'grade'         => 'required|in_list[A,B,C,Organik,Biasa]',
            'gambar_produk' => 'uploaded[gambar_produk]|is_image[gambar_produk]|max_size[gambar_produk,2048]',
        ];

        $messages = [
            'gambar_produk' => [
                'uploaded' => 'Gambar produk wajib diunggah.',
                'is_image' => 'File harus berupa gambar (jpg/png/webp).',
                'max_size' => 'Ukuran gambar maksimal 2MB.',
            ],
        ];

        if ($this->request->getPost('status_panen') === 'Pre-Order') {
            $rules['tanggal_estimasi_panen'] = 'required|valid_date';
        }

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $fileGambar = $this->request->getFile('gambar_produk');
            $namaGambar = $fileGambar->getRandomName();
            $fileGambar->move(FCPATH . 'assets/uploads/produk', $namaGambar);

            $namaSertifikat = null;
            $fileSertifikat = $this->request->getFile('sertifikat');
            if ($fileSertifikat && $fileSertifikat->isValid()) {
                $namaSertifikat = $fileSertifikat->getRandomName();
                $fileSertifikat->move(FCPATH . 'assets/uploads/sertifikat', $namaSertifikat);
            }

            $data = [
                'id_petani'              => session()->get('id_user'),
                'nama_produk'            => $this->request->getPost('nama_produk'),
                'kategori'               => $this->request->getPost('kategori'),
                'harga_per_kg'           => $this->request->getPost('harga_per_kg'),
                'stok_kg'                => $this->request->getPost('stok_kg'),
                'status_panen'           => $this->request->getPost('status_panen'),
                'tanggal_estimasi_panen' => $this->request->getPost('status_panen') === 'Pre-Order'
                    ? $this->request->getPost('tanggal_estimasi_panen')
                    : null,
                'gambar_produk'          => $namaGambar,
                'grade'                  => $this->request->getPost('grade'),
                'sertifikat'             => $namaSertifikat,
            ];

            $this->produkModel->insert($data);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi database gagal.');
            }
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan produk: ' . $e->getMessage());
        }

        return redirect()->to('/petani/produk')->with('success', 'Produk "' . $data['nama_produk'] . '" berhasil ditambahkan ke etalase.');
    }

    public function delete($id)
    {
        $idPetani = session()->get('id_user');
        $produk   = $this->produkModel->find($id);

        if (! $produk || $produk['id_petani'] != $idPetani) {
            return redirect()->to('/petani/produk')->with('error', 'Produk tidak ditemukan atau bukan milik Anda.');
        }

        // Hapus file gambar & sertifikat fisik jika ada
        $pathGambar = FCPATH . 'assets/uploads/produk/' . $produk['gambar_produk'];
        if ($produk['gambar_produk'] && is_file($pathGambar)) {
            unlink($pathGambar);
        }
        if (! empty($produk['sertifikat'])) {
            $pathSertifikat = FCPATH . 'assets/uploads/sertifikat/' . $produk['sertifikat'];
            if (is_file($pathSertifikat)) {
                unlink($pathSertifikat);
            }
        }

        $this->produkModel->delete($id);

        return redirect()->to('/petani/produk')->with('success', 'Produk "' . $produk['nama_produk'] . '" berhasil dihapus.');
    }
}
