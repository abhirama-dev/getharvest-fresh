<?php

namespace App\Controllers\Petani;

use App\Controllers\BaseController;
use App\Models\PengeluaranTaniModel;

class KalkulatorController extends BaseController
{
    protected PengeluaranTaniModel $pengeluaranModel;

    public function __construct()
    {
        $this->pengeluaranModel = new PengeluaranTaniModel();
    }

    public function index()
    {
        $idPetani = session()->get('id_user');

        $totalModal = $this->pengeluaranModel->totalModal($idPetani);
        $pengeluaran = $this->pengeluaranModel->getByPetani($idPetani);

        return view('petani/kalkulator/index', [
            'pageTitle'    => 'Kalkulator Laba Premium',
            'pageSubtitle' => 'Hitung harga pokok dan rekomendasi harga jual',
            'totalModal'   => $totalModal,
            'pengeluaran'  => $pengeluaran,
        ]);
    }

    public function simpanPengeluaran()
    {
        $rules = [
            'kategori'    => 'required|max_length[50]',
            'keterangan'  => 'permit_empty|max_length[255]',
            'nominal'     => 'required|numeric|greater_than[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $this->pengeluaranModel->insert([
            'id_petani'  => session()->get('id_user'),
            'kategori'   => $this->request->getPost('kategori'),
            'keterangan' => $this->request->getPost('keterangan'),
            'nominal'    => $this->request->getPost('nominal'),
        ]);

        return redirect()->to('/petani/kalkulator')->with('success', 'Pengeluaran berhasil dicatat, total modal diperbarui.');
    }

    public function hapusPengeluaran(int $id)
    {
        $idPetani   = session()->get('id_user');
        $pengeluaran = $this->pengeluaranModel->find($id);

        if (! $pengeluaran || $pengeluaran['id_petani'] != $idPetani) {
            return redirect()->to('/petani/kalkulator')->with('error', 'Data pengeluaran tidak ditemukan.');
        }

        $this->pengeluaranModel->delete($id);

        return redirect()->to('/petani/kalkulator')->with('success', 'Data pengeluaran dihapus.');
    }
}