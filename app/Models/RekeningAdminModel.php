<?php

namespace App\Models;

use CodeIgniter\Model;

class RekeningAdminModel extends Model
{
    protected $table            = 'rekening_admin';
    protected $primaryKey       = 'id_rekening';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'tipe', 'nama_bank', 'nomor_rekening', 'atas_nama',
    ];

    protected $useTimestamps = false;
}