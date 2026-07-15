<?php

namespace App\Controllers\Pedagang;

use App\Controllers\BaseController;
use App\Models\NegoHargaModel;
use App\Models\ProdukModel;
use App\Libraries\NegoService;
use CodeIgniter\Exceptions\PageNotFoundException;

class NegoController extends BaseController
{
    /**
     * Daftar nego milik pedagang (hanya kepala rantai / nego_induk NULL) beserta status terkini
     * dan riwayat lengkap tiap rantai untuk ditampilkan sebagai timeline.
     */
    public function index()
    {
        $idPedagang = session()->get('id_user');
        $negoModel = new NegoHargaModel();

        $kepalaRantai = $negoModel
            ->select('nego_harga.*, produk.nama_produk, produk.gambar_produk, produk.harga_per_kg')
            ->join('produk', 'produk.id_produk = nego_harga.id_produk')
            ->where('nego_harga.id_pedagang', $idPedagang)
            ->where('nego_harga.nego_induk', null)
            ->orderBy('nego_harga.tanggal_nego', 'DESC')
            ->find();

        // Untuk tiap kepala rantai, ambil status & tawaran terakhir dalam rantainya
        foreach ($kepalaRantai as &$n) {
            $n['riwayat'] = $negoModel->getRiwayatChain($n['id_nego']);
            $terakhir = end($n['riwayat']);
            $n['status_terkini']       = $terakhir ? $terakhir['status_nego'] : $n['status_nego'];
            $n['harga_terkini']        = $terakhir ? $terakhir['harga_tawaran'] : $n['harga_tawaran'];
            $n['pihak_selanjutnya']    = $terakhir ? $terakhir['pihak_selanjutnya'] : $n['pihak_selanjutnya'];
            $n['id_nego_aktif']        = $terakhir ? $terakhir['id_nego'] : $n['id_nego'];
        }
        unset($n);

        return view('pedagang/nego/index', [
            'title' => 'Negosiasi Saya',
            'daftarNego' => $kepalaRantai,
        ]);
    }

    /**
     * Mengajukan tawaran nego baru dari halaman detail produk.
     */
    public function ajukan()
    {
        $idPedagang = session()->get('id_user');

        $rules = [
            'id_produk'           => 'required|is_natural_no_zero',
            'jumlah_kebutuhan_kg' => 'required|is_natural_no_zero',
            'harga_tawaran'       => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $idProduk = (int) $this->request->getPost('id_produk');
        $produk = (new ProdukModel())->find($idProduk);

        if (!$produk) {
            throw PageNotFoundException::forPageNotFound('Produk tidak ditemukan');
        }

        $jumlah = (int) $this->request->getPost('jumlah_kebutuhan_kg');
        if ($jumlah > $produk['stok_kg']) {
            return redirect()->back()->with('error', 'Jumlah melebihi stok yang tersedia.');
        }

        $negoModel = new NegoHargaModel();
        $negoModel->insert([
            'id_pedagang'         => $idPedagang,
            'id_produk'           => $idProduk,
            'jumlah_kebutuhan_kg' => $jumlah,
            'harga_tawaran'       => (int) $this->request->getPost('harga_tawaran'),
            'status_nego'         => 'Menunggu',
            'pihak_selanjutnya'   => 'petani',
        ]);

        model('NotifikasiModel')->kirim(
            $produk['id_petani'],
            'Tawaran Nego Baru',
            'Ada tawaran nego untuk produk "' . $produk['nama_produk'] . '" sejumlah ' . $jumlah . ' kg.'
        );

        return redirect()->to('pedagang/katalog/' . $idProduk)->with('success', 'Tawaran nego berhasil dikirim, menunggu respon petani.');
    }

    /**
     * Aksi pedagang saat giliran merespon (pihak_selanjutnya = pedagang, artinya petani baru saja nego balik).
     * aksi: terima | tolak | balas
     */
    public function respon(int $idNego)
    {
        $idPedagang = session()->get('id_user');
        $negoModel = new NegoHargaModel();

        $nego = $negoModel
            ->where('id_nego', $idNego)
            ->where('id_pedagang', $idPedagang)
            ->first();

        if (!$nego) {
            throw PageNotFoundException::forPageNotFound('Data nego tidak ditemukan');
        }

        if ($nego['pihak_selanjutnya'] !== 'pedagang' || !in_array($nego['status_nego'], ['Menunggu', 'Dibalas'])) {
            return redirect()->back()->with('error', 'Nego ini bukan giliran Anda untuk merespon.');
        }

        $produk = (new ProdukModel())->find($nego['id_produk']);
        $aksi = $this->request->getPost('aksi');
        $service = new NegoService();

        try {
            if ($aksi === 'terima') {
                $idPesanan = $service->terima($nego);
                return redirect()->to('pedagang/pembelian/bayar/' . $idPesanan)
                    ->with('success', 'Tawaran diterima! Silakan selesaikan pembayaran.');
            }

            if ($aksi === 'tolak') {
                $service->tolak($nego, (string) $produk['id_petani'], $produk['nama_produk']);
                return redirect()->to('pedagang/nego')->with('success', 'Tawaran dari petani telah Anda tolak.');
            }

            if ($aksi === 'balas') {
                $rules = ['harga_baru' => 'required|is_natural_no_zero'];
                if (!$this->validate($rules)) {
                    return redirect()->back()->with('errors', $this->validator->getErrors());
                }
                $jumlahBaru = (int) ($this->request->getPost('jumlah_baru') ?: $nego['jumlah_kebutuhan_kg']);
                $service->balas($nego, (int) $this->request->getPost('harga_baru'), $jumlahBaru, 'petani');

                model('NotifikasiModel')->kirim(
                    $produk['id_petani'],
                    'Balasan Nego dari Pedagang',
                    'Pedagang membalas tawaran untuk produk "' . $produk['nama_produk'] . '" dengan harga baru.'
                );

                return redirect()->to('pedagang/nego')->with('success', 'Balasan nego terkirim.');
            }
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('error', 'Aksi tidak dikenali.');
    }
}