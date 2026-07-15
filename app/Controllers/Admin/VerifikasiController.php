<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NotifikasiModel;
use App\Models\UserModel;

class VerifikasiController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $pending = $this->userModel->getPedagangPending();

        return view('admin/verifikasi/index', [
            'pageTitle'    => 'Verifikasi Pedagang',
            'pageSubtitle' => 'Tinjau pendaftaran akun pedagang baru',
            'pedagang'     => $pending,
        ]);
    }

    public function detail(int $id)
    {
        $pedagang = $this->userModel->find($id);
        if (! $pedagang || $pedagang['role'] !== 'pedagang') {
            return redirect()->to('/admin/verifikasi')->with('error', 'Data pedagang tidak ditemukan.');
        }

        return view('admin/verifikasi/detail', [
            'pageTitle'    => 'Detail Pedagang',
            'pageSubtitle' => $pedagang['nama_lengkap'],
            'pedagang'     => $pedagang,
        ]);
    }

    public function setujui(int $id)
    {
        $pedagang = $this->cekAkses($id);
        if ($pedagang instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $pedagang;
        }

        $this->userModel->update($id, [
            'status_verifikasi' => 'disetujui',
            'alasan_tolak'      => null,
        ]);

        (new NotifikasiModel())->kirim(
            $id,
            'Akun Disetujui',
            'Selamat! Akun pedagang Anda telah diverifikasi. Anda sekarang dapat login dan mulai berbelanja.'
        );

        return redirect()->to('/admin/verifikasi')->with('success', 'Pedagang "' . $pedagang['nama_lengkap'] . '" berhasil disetujui.');
    }

    public function tolak(int $id)
    {
        $pedagang = $this->cekAkses($id);
        if ($pedagang instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $pedagang;
        }

        $alasan = $this->request->getPost('alasan_tolak');
        if (empty($alasan)) {
            return redirect()->back()->with('error', 'Alasan penolakan wajib diisi.');
        }

        $this->userModel->update($id, [
            'status_verifikasi' => 'ditolak',
            'alasan_tolak'      => $alasan,
        ]);

        (new NotifikasiModel())->kirim(
            $id,
            'Pendaftaran Ditolak',
            'Pendaftaran akun pedagang Anda ditolak. Alasan: ' . $alasan
        );

        return redirect()->to('/admin/verifikasi')->with('success', 'Pendaftaran pedagang "' . $pedagang['nama_lengkap'] . '" ditolak.');
    }

    private function cekAkses(int $id)
    {
        $pedagang = $this->userModel->find($id);
        if (! $pedagang || $pedagang['role'] !== 'pedagang' || $pedagang['status_verifikasi'] !== 'pending') {
            return redirect()->to('/admin/verifikasi')->with('error', 'Data tidak valid atau sudah diproses.');
        }
        return $pedagang;
    }
}