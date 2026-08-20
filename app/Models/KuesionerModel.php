<?php

namespace App\Models;

use CodeIgniter\Model;

class KuesionerModel extends Model
{
    protected $table = 'kuesioner';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nama',
        'usia',
        'status',
        'hari_kunjungan',
        'waktu_kunjungan',
        'menu_minuman',
        'menu_makanan',
        'no_hp'
    ];
}