<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoryAnalisisModel extends Model
{

    protected $table = 'history_analisis';

    protected $primaryKey = 'id_history';


    protected $allowedFields = [
        'tanggal_analisis',
        'periode_data',
        'jumlah_data',
        'jumlah_cluster',
        'silhouette_score',
        'hasil_cluster',
        'rekomendasi_promosi'
    ];


}