<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RekeningAdminModel;

class RekeningAdminController extends BaseController
{
    protected RekeningAdminModel $model;

    public function __construct()
    {
        $this->model = new RekeningAdminModel();
    }

    public function index()
    {
        return view('admin/rekening_admin/index', [
            'pageTitle'    => 'Rekening Platform',
            'pageSubtitle' => 'Rekening tujuan pembayaran escrow dari pedagang',
            'rekening'     => $this->model->findAll(),
        ]);
    }

    public function store()
    {
        $rules = [
            'tipe'           => 'required|in_list[bank,e_wallet]',
            'nomor_rekening' => 'required|max_length[50]',
            'atas_nama'      => 'required|max_length[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Data rekening tidak valid.');
        }

        $this->model->insert([
            'tipe'           => $this->request->getPost('tipe'),
            'nama_bank'      => $this->request->getPost('nama_bank'),
            'nomor_rekening' => $this->request->getPost('nomor_rekening'),
            'atas_nama'      => $this->request->getPost('atas_nama'),
        ]);

        return redirect()->to('/admin/rekening-admin')->with('success', 'Rekening platform berhasil ditambahkan.');
    }

    public function delete(int $id)
    {
        $this->model->delete($id);
        return redirect()->to('/admin/rekening-admin')->with('success', 'Rekening platform dihapus.');
    }
}