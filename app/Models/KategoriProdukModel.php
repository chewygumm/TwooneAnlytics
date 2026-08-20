<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriProdukModel extends Model
{
    protected $table = 'kategori_produk';

    protected $primaryKey = 'id_produk';

    protected $allowedFields = [
        'produk',
        'kategori'
    ];
}