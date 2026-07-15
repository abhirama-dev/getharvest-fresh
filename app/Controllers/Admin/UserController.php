<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UserController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $role   = $this->request->getGet('role') ?: '';
        $q      = $this->request->getGet('q') ?: '';

        $builder = $this->userModel->where('role !=', 'admin');

        if ($role) {
            $builder->where('role', $role);
        }
        if ($q) {
            $builder->groupStart()
                ->like('nama_lengkap', $q)
                ->orLike('email', $q)
                ->groupEnd();
        }

        $users = $builder->orderBy('created_at', 'DESC')->findAll();

        return view('admin/users/index', [
            'pageTitle'    => 'Kelola Pengguna',
            'pageSubtitle' => 'Daftar seluruh petani dan pedagang',
            'users'        => $users,
            'filterRole'   => $role,
            'filterQ'      => $q,
        ]);
    }

    public function detail(int $id)
    {
        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to('/admin/users')->with('error', 'Pengguna tidak ditemukan.');
        }

        $db = \Config\Database::connect();

        $statistik = [];
        if ($user['role'] === 'petani') {
            $statistik['jumlah_produk'] = $db->table('produk')->where('id_petani', $id)->countAllResults();
            $statistik['total_pendapatan'] = (new \App\Models\PesananModel())->totalPendapatanPetani($id);
            $statistik['rata_rating'] = (new \App\Models\RatingReviewModel())->rataRata($id);
        } else {
            $statistik['total_belanja'] = (new \App\Models\PesananModel())->totalBelanjaPedagang($id);
            $statistik['jumlah_transaksi'] = $db->table('pesanan')->where('id_pedagang', $id)->countAllResults();
        }

        return view('admin/users/detail', [
            'pageTitle'    => 'Detail Pengguna',
            'pageSubtitle' => $user['nama_lengkap'],
            'user'         => $user,
            'statistik'    => $statistik,
        ]);
    }
}