<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\NotifikasiModel;
use App\Models\UserModel;

class AuthController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        return view('auth/login', [
            'pageTitle' => 'Masuk',
        ]);
    }

    public function attemptLogin()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Email dan password wajib diisi dengan benar.');
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->findByEmail($email);

        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau password yang Anda masukkan salah.');
        }

        if ($user['role'] === 'pedagang') {
            if ($user['status_verifikasi'] === 'pending') {
                return redirect()->back()->with('error', 'Akun Anda masih menunggu verifikasi dari Admin. Mohon tunggu konfirmasi.');
            }

            if ($user['status_verifikasi'] === 'ditolak') {
                $alasan = $user['alasan_tolak'] ?: 'Tidak ada keterangan dari Admin.';
                return redirect()->back()->with('error', 'Pendaftaran akun Anda ditolak. Alasan: ' . $alasan);
            }
        }

        session()->set([
            'isLoggedIn'   => true,
            'id_user'      => $user['id_user'],
            'nama_lengkap' => $user['nama_lengkap'],
            'email'        => $user['email'],
            'role'         => $user['role'],
            'is_premium'   => (bool) $user['is_premium'],
        ]);

        return $this->redirectByRole($user['role'])
            ->with('success', 'Selamat datang kembali, ' . $user['nama_lengkap'] . '!');
    }

    public function register()
    {
        return view('auth/register', [
            'pageTitle' => 'Daftar Akun',
        ]);
    }

    public function attemptRegister()
    {
        $role = $this->request->getPost('role');

        $rules = [
            'nama_lengkap'     => 'required|min_length[3]|max_length[100]',
            'alamat'           => 'required',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
            'role'             => 'required|in_list[petani,pedagang]',
            'no_hp'            => 'required|min_length[9]|max_length[15]',
        ];

        $messages = [
            'email'            => ['is_unique' => 'Email sudah terdaftar, gunakan email lain.'],
            'confirm_password' => ['matches' => 'Konfirmasi password tidak sesuai.'],
        ];

        if ($role === 'pedagang') {
            $rules['alamat_toko'] = 'required|max_length[255]';
            $rules['koordinat']   = 'required';
            $rules['foto_toko']   = 'uploaded[foto_toko]|is_image[foto_toko]|max_size[foto_toko,2048]';
            $messages['foto_toko'] = [
                'uploaded' => 'Foto toko wajib diunggah untuk akun pedagang.',
                'is_image' => 'File yang diunggah harus berupa gambar (jpg/png).',
                'max_size' => 'Ukuran foto toko maksimal 2MB.',
            ];
        }

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'alamat'       => $this->request->getPost('alamat'),
            'email'        => $this->request->getPost('email'),
            'password'     => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'         => $role,
            'no_hp'        => $this->request->getPost('no_hp'),
            'is_premium'   => 0,
        ];

        if ($role === 'pedagang') {
            $file    = $this->request->getFile('foto_toko');
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'assets/uploads/toko', $newName);

            $data['alamat_toko']       = $this->request->getPost('alamat_toko');
            $data['koordinat']         = $this->request->getPost('koordinat');
            $data['foto_toko']         = $newName;
            $data['status_verifikasi'] = 'pending';
        }

        $this->userModel->insert($data);

        if ($role === 'pedagang') {
            $notifModel = new NotifikasiModel();
            $admins     = $this->userModel->where('role', 'admin')->findAll();

            foreach ($admins as $admin) {
                $notifModel->kirim(
                    $admin['id_user'],
                    'Pendaftaran Pedagang Baru',
                    $data['nama_lengkap'] . ' mendaftar sebagai pedagang dan menunggu verifikasi Anda.'
                );
            }

            return redirect()->to('/login')
                ->with('success', 'Pendaftaran berhasil! Akun Anda menunggu verifikasi Admin sebelum dapat digunakan.');
        }

        return redirect()->to('/login')
            ->with('success', 'Pendaftaran berhasil! Silakan masuk dengan akun Anda.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil keluar.');
    }

    private function redirectByRole(string $role)
    {
        return match ($role) {
            'petani'   => redirect()->to('/petani/dashboard'),
            'pedagang' => redirect()->to('/pedagang/dashboard'),
            'admin'    => redirect()->to('/admin/dashboard'),
            default    => redirect()->to('/login'),
        };
    }
}