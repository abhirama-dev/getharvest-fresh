<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NotifikasiModel;
use App\Models\RekeningModel;

class RekeningController extends BaseController
{
    protected RekeningModel $rekeningModel;

    public function __construct()
    {
        $this->rekeningModel = new RekeningModel();
    }

    public function index()
    {
        $pending = $this->rekeningModel->getPendingValidasi();

        return view('admin/rekening/index', [
            'pageTitle'    => 'Validasi Rekening',
            'pageSubtitle' => 'Verifikasi manual rekening pengguna',
            'rekening'     => $pending,
        ]);
    }

    public function verifikasi(int $id)
    {
        $rekening = $this->rekeningModel->find($id);
        if (! $rekening) {
            return redirect()->to('/admin/rekening')->with('error', 'Rekening tidak ditemukan.');
        }

        $this->rekeningModel->update($id, ['status_validasi' => 'verified']);

        (new NotifikasiModel())->kirim(
            $rekening['id_user'],
            'Rekening Terverifikasi',
            'Rekening ' . $rekening['nama_bank'] . ' a.n. ' . $rekening['atas_nama'] . ' telah diverifikasi Admin dan siap digunakan.'
        );

        return redirect()->to('/admin/rekening')->with('success', 'Rekening berhasil diverifikasi.');
    }

    public function tolak(int $id)
    {
        $rekening = $this->rekeningModel->find($id);
        if (! $rekening) {
            return redirect()->to('/admin/rekening')->with('error', 'Rekening tidak ditemukan.');
        }

        $this->rekeningModel->update($id, ['status_validasi' => 'rejected']);

        (new NotifikasiModel())->kirim(
            $rekening['id_user'],
            'Rekening Ditolak',
            'Rekening ' . $rekening['nama_bank'] . ' a.n. ' . $rekening['atas_nama'] . ' ditolak Admin. Silakan periksa kembali data Anda.'
        );

        return redirect()->to('/admin/rekening')->with('success', 'Rekening ditolak.');
    }
}