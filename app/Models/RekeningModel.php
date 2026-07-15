<?php

namespace App\Models;

use CodeIgniter\Model;

class RekeningModel extends Model
{
    protected $table            = 'rekening';
    protected $primaryKey       = 'id_rekening';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_user', 'tipe', 'nama_bank', 'nomor_rekening', 'atas_nama', 'status_validasi',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'id_user'        => 'required|numeric',
        'tipe'           => 'required|in_list[bank,e_wallet]',
        'nomor_rekening' => 'required|min_length[5]|max_length[50]',
        'atas_nama'      => 'required|min_length[3]|max_length[100]',
    ];

    public function getByUser(int $idUser)
    {
        return $this->where('id_user', $idUser)->findAll();
    }

    public function getVerified(int $idUser)
    {
        return $this->where('id_user', $idUser)
                    ->where('status_validasi', 'verified')
                    ->findAll();
    }

    public function getPendingValidasi()
    {
        return $this->select('rekening.*, users.nama_lengkap, users.role')
                    ->join('users', 'users.id_user = rekening.id_user')
                    ->where('status_validasi', 'pending')
                    ->findAll();
    }
}