<?php

namespace App\Controllers\Petani;

use App\Controllers\BaseController;
use App\Models\UserModel;

class ProfilController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $user = $this->userModel->find(session()->get('id_user'));

        return view('petani/profil/index', [
            'pageTitle'    => 'Profil Saya',
            'pageSubtitle' => 'Kelola informasi akun Anda',
            'user'         => $user,
        ]);
    }

    public function update()
    {
        $idUser = session()->get('id_user');

        $rules = [
            'nama_lengkap' => 'required|min_length[3]|max_length[100]',
            'alamat'       => 'required',
            'no_hp'        => 'required|min_length[9]|max_length[15]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $this->userModel->updateProfil($idUser, [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'alamat'       => $this->request->getPost('alamat'),
            'no_hp'        => $this->request->getPost('no_hp'),
        ]);

        session()->set('nama_lengkap', $this->request->getPost('nama_lengkap'));

        return redirect()->to('/petani/profil')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword()
    {
        $idUser = session()->get('id_user');
        $user   = $this->userModel->find($idUser);

        $rules = [
            'password_lama'  => 'required',
            'password_baru'  => 'required|min_length[6]',
            'konfirmasi'     => 'required|matches[password_baru]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()));
        }

        if (! password_verify($this->request->getPost('password_lama'), $user['password'])) {
            return redirect()->back()->with('error', 'Password lama tidak sesuai.');
        }

        $this->userModel->update($idUser, [
            'password' => password_hash($this->request->getPost('password_baru'), PASSWORD_DEFAULT),
        ]);

        return redirect()->to('/petani/profil')->with('success', 'Password berhasil diubah.');
    }
}