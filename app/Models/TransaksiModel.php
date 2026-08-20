<?php

namespace App\Models;

use CodeIgniter\Model;


class TransaksiModel extends Model
{

    protected $table = 'transaksi';

    protected $primaryKey = 'id_transaksi';


    protected $allowedFields = [

        'no_struk',
        'tanggal',
        'jam',
        'nama_kasir',
        'produk',
        'jumlah_produk',
        'jumlah_dibatalkan',
        'harga_per_produk',
        'subtotal',
        'tipe_harga',
        'diskon_produk',
        'tipe_diskon_produk',
        'total',
        'status',
        'metode_pembayaran'

    ];
    public function reset()
    {
        return $this->builder()->delete();
    }

}