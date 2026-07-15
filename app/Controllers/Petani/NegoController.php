<?php

namespace App\Controllers\Petani;

use App\Controllers\BaseController;
use App\Models\NegoHargaModel;
use App\Models\ProdukModel;
use App\Libraries\NegoService;
use CodeIgniter\Exceptions\PageNotFoundException;

class NegoController extends BaseController
{
    public function index()
    {
        $idPetani = session()->get('id_user');
        $negoModel = new NegoHargaModel();

        $kepalaRantai = $negoModel
            ->select('nego_harga.*, produk.nama_produk, produk.gambar_produk, produk.harga_per_kg, users.nama_lengkap AS nama_pedagang')
            ->join('produk', 'produk.id_produk = nego_harga.id_produk')
            ->join('users', 'users.id_user = nego_harga.id_pedagang')
            ->where('produk.id_petani', $idPetani)
            ->where('nego_harga.nego_induk', null)
            ->orderBy('nego_harga.tanggal_nego', 'DESC')
            ->find();

        foreach ($kepalaRantai as &$n) {
            $n['riwayat'] = $negoModel->getRiwayatChain($n['id_nego']);
            $terakhir = end($n['riwayat']);
            $n['status_terkini']    = $terakhir ? $terakhir['status_nego'] : $n['status_nego'];
            $n['harga_terkini']     = $terakhir ? $terakhir['harga_tawaran'] : $n['harga_tawaran'];
            $n['pihak_selanjutnya'] = $terakhir ? $terakhir['pihak_selanjutnya'] : $n['pihak_selanjutnya'];
            $n['id_nego_aktif']     = $terakhir ? $terakhir['id_nego'] : $n['id_nego'];
        }
        unset($n);

        return view('petani/nego/index', [
            'title' => 'Negosiasi Masuk',
            'daftarNego' => $kepalaRantai,
        ]);
    }

    /**
     * Aksi petani saat giliran merespon (pihak_selanjutnya = petani): terima | tolak | balas
     */
    public function respon(int $idNego)
    {
        $idPetani = session()->get('id_user');
        $negoModel = new NegoHargaModel();

        $nego = $negoModel
            ->select('nego_harga.*, produk.id_petani, produk.nama_produk')
            ->join('produk', 'produk.id_produk = nego_harga.id_produk')
            ->where('nego_harga.id_nego', $idNego)
            ->where('produk.id_petani', $idPetani)
            ->first();

        if (!$nego) {
            throw PageNotFoundException::forPageNotFound('Data nego tidak ditemukan');
        }

        if ($nego['pihak_selanjutnya'] !== 'petani' || !in_array($nego['status_nego'], ['Menunggu', 'Dibalas'])) {
            return redirect()->back()->with('error', 'Nego ini bukan giliran Anda untuk merespon.');
        }

        $aksi = $this->request->getPost('aksi');
        $service = new NegoService();

        try {
            if ($aksi === 'terima') {
                $service->terima($nego);
                return redirect()->to('petani/nego')->with('success', 'Tawaran diterima. Menunggu pembayaran dari pedagang.');
            }

            if ($aksi === 'tolak') {
                $service->tolak($nego, (string) $nego['id_pedagang'], $nego['nama_produk']);
                return redirect()->to('petani/nego')->with('success', 'Tawaran telah Anda tolak.');
            }

            if ($aksi === 'balas') {
                $rules = ['harga_baru' => 'required|is_natural_no_zero'];
                if (!$this->validate($rules)) {
                    return redirect()->back()->with('errors', $this->validator->getErrors());
                }
                $jumlahBaru = (int) ($this->request->getPost('jumlah_baru') ?: $nego['jumlah_kebutuhan_kg']);
                $service->balas($nego, (int) $this->request->getPost('harga_baru'), $jumlahBaru, 'pedagang');

                model('NotifikasiModel')->kirim(
                    $nego['id_pedagang'],
                    'Balasan Nego dari Petani',
                    'Petani membalas tawaran untuk produk "' . $nego['nama_produk'] . '" dengan harga baru.'
                );

                return redirect()->to('petani/nego')->with('success', 'Balasan nego terkirim.');
            }
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('error', 'Aksi tidak dikenali.');
    }
}