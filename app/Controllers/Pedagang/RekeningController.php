<?php

namespace App\Controllers\Pedagang;

use App\Controllers\BaseController;
use App\Libraries\RekeningService;
use App\Models\RekeningModel;

class RekeningController extends BaseController
{
    protected RekeningModel $rekeningModel;
    protected RekeningService $service;

    public function __construct()
    {
        $this->rekeningModel = new RekeningModel();
        $this->service       = new RekeningService();
    }

    public function index()
    {
        $idUser   = session()->get('id_user');
        $rekening = $this->rekeningModel->getByUser($idUser);

        $microNominal = [];
        foreach ($rekening as $r) {
            if ($r['status_validasi'] === 'pending') {
                $existing = $this->service->getMicroTransfer($r['id_rekening']);
                $microNominal[$r['id_rekening']] = $existing ?? $this->service->generateMicroTransfer($r['id_rekening']);
            }
        }

        return view('pedagang/rekening/index', [
            'pageTitle'    => 'Kelola Rekening',
            'pageSubtitle' => 'Kelola rekening bank/e-wallet Anda',
            'rekening'     => $rekening,
            'microNominal' => $microNominal,
            'backUrl'      => '/pedagang/rekening',
        ]);
    }

    public function store()
    {
        $rules = [
            'tipe'           => 'required|in_list[bank,e_wallet]',
            'nama_bank'      => 'required|max_length[100]',
            'nomor_rekening' => 'required|min_length[5]|max_length[50]',
            'atas_nama'      => 'required|min_length[3]|max_length[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $this->service->tambah(session()->get('id_user'), $this->request->getPost());

        return redirect()->to('/pedagang/rekening')->with('success', 'Rekening berhasil ditambahkan, silakan lakukan verifikasi.');
    }

    public function verifikasi(int $id)
    {
        $nominal = (int) $this->request->getPost('nominal');
        $ok = $this->service->konfirmasiMicroTransfer($id, session()->get('id_user'), $nominal);

        if (! $ok) {
            return redirect()->back()->with('error', 'Nominal konfirmasi tidak sesuai. Silakan coba lagi.');
        }

        return redirect()->to('/pedagang/rekening')->with('success', 'Rekening berhasil diverifikasi dan siap digunakan.');
    }

    public function hapus(int $id)
    {
        $ok = $this->service->hapus($id, session()->get('id_user'));

        if (! $ok) {
            return redirect()->to('/pedagang/rekening')->with('error', 'Rekening tidak ditemukan.');
        }

        return redirect()->to('/pedagang/rekening')->with('success', 'Rekening berhasil dihapus.');
    }
}