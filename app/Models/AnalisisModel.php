<?php

namespace App\Models;

use CodeIgniter\Model;

class AnalisisModel extends Model
{

    protected $table = 'cluster';

    protected $primaryKey = 'id_cluster';

    protected $allowedFields = [
        'id_preprocessing',
        'cluster'
    ];



    // =================================================
    // DETAIL HASIL CLUSTERING
    // Sesuai tahap Encoding & Normalisasi
    // =================================================

    public function getClusterDetail()
    {
        return $this->db
            ->table('cluster')

            ->select('
                cluster.cluster,
                preprocessing.no_struk,
                preprocessing.jenis_hari,
                preprocessing.hari_encode,

                preprocessing.menu_kopi,
                preprocessing.menu_non_kopi,
                preprocessing.menu_makanan,
                preprocessing.menu_snack,

                preprocessing.total_asli,
                preprocessing.total_normalisasi
            ')

            ->join(
                'preprocessing',
                'preprocessing.id_preprocessing = cluster.id_preprocessing'
            )

            ->get()
            ->getResultArray();
    }


public function getCentroidByCluster($cluster)
{
    return $this->db
        ->table('centroid')
        ->where('cluster', $cluster)
        ->get()
        ->getRowArray();
}

    // =================================================
    // RINGKASAN KARAKTERISTIK CLUSTER
    // Produk Dominan + Hari Dominan + Rata-rata Total
    // =================================================


    public function getRingkasan()
{

    $data = $this->db
        ->table('cluster')
        ->select('
            cluster.cluster,
            preprocessing.jenis_hari,

            preprocessing.menu_kopi,
            preprocessing.menu_non_kopi,
            preprocessing.menu_makanan,
            preprocessing.menu_snack,

            preprocessing.total_asli
        ')
        ->join(
            'preprocessing',
            'preprocessing.id_preprocessing = cluster.id_preprocessing'
        )
        ->get()
        ->getResultArray();



    $hasil = [];



    foreach($data as $row)
    {

        $c = $row['cluster'];



        if(!isset($hasil[$c]))
        {

            $hasil[$c] = [

                'cluster'=>$c,

                'jumlah'=>0,

                'kopi'=>0,

                'non_kopi'=>0,

                'makanan'=>0,

                'snack'=>0,

                'hari'=>[],

                'total'=>0

            ];

        }



        // jumlah transaksi

        $hasil[$c]['jumlah']++;



        // jumlah kategori produk

        $hasil[$c]['kopi'] += $row['menu_kopi'];

        $hasil[$c]['non_kopi'] += $row['menu_non_kopi'];

        $hasil[$c]['makanan'] += $row['menu_makanan'];

        $hasil[$c]['snack'] += $row['menu_snack'];



        // menyimpan jenis hari transaksi

        $hasil[$c]['hari'][] = 
            $row['jenis_hari'];



        // total transaksi

        $hasil[$c]['total'] += 
            $row['total_asli'];

    }





    foreach($hasil as &$item)
    {

// =================================
// PRODUK DOMINAN
// =================================

$produk = [

    'Kopi'=>$item['kopi'],

    'Non-Kopi'=>$item['non_kopi'],

    'Makanan'=>$item['makanan'],

    'Snack'=>$item['snack']

];


$totalProduk = array_sum($produk);


$produkDominan = [];


foreach($produk as $nama=>$jumlah)
{

    if($totalProduk > 0)
    {

        $persentase = ($jumlah / $totalProduk) * 100;


        if($persentase >= 20)
        {
            $produkDominan[] = $nama;
        }

    }

}


$item['produk_dominan'] =
    implode(" + ", $produkDominan);
    

        // =================================
        // HARI DOMINAN
        // =================================


        $hari = array_count_values(
            $item['hari']
        );



        arsort($hari);



        $item['hari_dominan'] =
            array_key_first($hari);





        // =================================
        // RATA-RATA TRANSAKSI
        // =================================


        $item['rata_rata'] =
            $item['total'] / $item['jumlah'];

    }



    return array_values($hasil);

}

    // =================================================
    // JUMLAH DATA SETIAP CLUSTER
    // =================================================

    public function getJumlahCluster()
    {

        return $this->db

            ->table('cluster')

            ->select('
                cluster,
                COUNT(*) as jumlah
            ')

            ->groupBy('cluster')

            ->get()

            ->getResultArray();

    }



    public function getCentroidAkhir()
    {
        return $this->db
            ->table('centroid')
            ->orderBy('cluster','ASC')
            ->get()
            ->getResultArray();
    }

    // =================================================
    // EVALUASI SILHOUETTE SCORE
    // =================================================


    public function getEvaluasiTerakhir()
    {

        return $this->db

            ->table('evaluasi')

            ->orderBy(
                'id_evaluasi',
                'DESC'
            )

            ->get()

            ->getRowArray();

    }




    public function simpanEvaluasi($data)
    {

        return $this->db

            ->table('evaluasi')

            ->insert($data);

    }




    public function hapusEvaluasi()
    {

        return $this->db

            ->table('evaluasi')

            ->truncate();

    }





    // =================================================
    // HASIL INTERPRETASI GEMINI / LLM
    // =================================================


    public function getHasilInterpretasi()
    {

        return $this->db

            ->table('interpretasi_llm')

            ->select('

                nomor_cluster as cluster,

                nama_segmen,

                karakteristik as ringkasan,

                informasi_pendukung,

                strategi_promosi

            ')

            ->get()

            ->getResultArray();

    }





    public function simpanInterpretasi($data)
    {

        return $this->db

            ->table('interpretasi_llm')

            ->insert($data);

    }





    public function hapusInterpretasi()
    {

        return $this->db

            ->table('interpretasi_llm')

            ->truncate();

    }


}