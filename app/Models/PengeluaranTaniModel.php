<?php

namespace App\Models;

use CodeIgniter\Model;

class PengeluaranTaniModel extends Model
{
    protected $table            = 'pengeluaran_tani';
    protected $primaryKey       = 'id_pengeluaran';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_petani', 'kategori', 'keterangan', 'nominal',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'kategori' => 'required|max_length[50]',
        'nominal'  => 'required|numeric|greater_than[0]',
    ];

    public function getByPetani(int $idPetani)
    {
        return $this->where('id_petani', $idPetani)
                    ->orderBy('tanggal', 'DESC')
                    ->findAll();
    }

    public function totalModal(int $idPetani): int
    {
        $result = $this->selectSum('nominal', 'total')
                        ->where('id_petani', $idPetani)
                        ->first();
        return (int) ($result['total'] ?? 0);
    }
}