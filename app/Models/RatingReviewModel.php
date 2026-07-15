<?php

namespace App\Models;

use CodeIgniter\Model;

class RatingReviewModel extends Model
{
    protected $table            = 'rating_review';
    protected $primaryKey       = 'id_rating';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_pesanan', 'id_pemberi', 'id_penerima', 'rating', 'ulasan',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'rating' => 'required|numeric|greater_than[0]|less_than_equal_to[5]',
        'ulasan' => 'permit_empty|max_length[1000]',
    ];

    public function getByPenerima(int $idPenerima)
    {
        return $this->select('rating_review.*, users.nama_lengkap AS nama_pemberi')
                    ->join('users', 'users.id_user = rating_review.id_pemberi')
                    ->where('id_penerima', $idPenerima)
                    ->orderBy('tanggal', 'DESC')
                    ->findAll();
    }

    public function rataRata(int $idPenerima): float
    {
        $result = $this->selectAvg('rating', 'avg_rating')
                        ->where('id_penerima', $idPenerima)
                        ->first();
        return round((float) ($result['avg_rating'] ?? 0), 1);
    }

    public function sudahDirating(int $idPesanan): bool
    {
        return $this->where('id_pesanan', $idPesanan)->first() !== null;
    }
}