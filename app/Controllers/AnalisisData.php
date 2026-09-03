<?php

namespace App\Controllers;

use App\Models\TransaksiModel;
use App\Models\KategoriProdukModel;
use App\Models\AnalisisModel;
use App\Models\KuesionerModel;
use App\Models\InterpretasiLLMModel;
use App\Models\HistoryAnalisisModel;

class AnalisisData extends BaseController
{

    protected $transaksi;
    protected $kategori;
    protected $analisis;
    protected $kuesioner;
    


    public function __construct()
    {

        $this->transaksi = new TransaksiModel();

        $this->kategori = new KategoriProdukModel();

        $this->analisis = new AnalisisModel();

        $this->history = new HistoryAnalisisModel();

        $this->kuesioner = new KuesionerModel();

    
    }



    public function index()
    {


        $clusterData = $this->analisis
                            ->getClusterDetail();



        // ===============================
        // RINGKASAN CLUSTER
        // ===============================


        $karakteristik = [];


        foreach($clusterData as $row)
        {


            $c = $row['cluster'];



            if(!isset($karakteristik[$c]))
            {

                $karakteristik[$c] = [

                    'cluster'=>$c,

                    'jumlah'=>0,

                    'kopi'=>0,

                    'non_kopi'=>0,

                    'makanan'=>0,

                    'snack'=>0,

                    'total'=>0,

                    'jenis_hari'=>[]

                ];

            }
            


            $karakteristik[$c]['jumlah']++;


            $karakteristik[$c]['kopi']
            +=$row['menu_kopi'];


            $karakteristik[$c]['non_kopi']
            +=$row['menu_non_kopi'];


            $karakteristik[$c]['makanan']
            +=$row['menu_makanan'];


            $karakteristik[$c]['snack']
            +=$row['menu_snack'];



            $karakteristik[$c]['total']
            +=$row['total_asli'];



           if(!empty($row['jenis_hari']))
            {
                $karakteristik[$c]['jenis_hari'][] = $row['jenis_hari'];
            }

        }




       foreach($karakteristik as &$item)
{


    // ===============================
    // PRODUK DOMINAN DARI CENTROID
    // ===============================


    $centroid = $this->analisis
    ->getCentroidByCluster($item['cluster']);


if($centroid)
{

    $produk = [

        "Kopi" =>
        $centroid['menu_kopi'],

        "Non-Kopi" =>
        $centroid['menu_non_kopi'],

        "Makanan" =>
        $centroid['menu_makanan'],

        "Snack" =>
        $centroid['menu_snack']

    ];


    // urutkan nilai terbesar ke terkecil
    arsort($produk);


    // ambil 2 produk terbesar
    $produkDominan = array_slice(
        array_keys($produk),
        0,
        2
    );


    $item['produk_dominan'] =
        implode(" + ", $produkDominan);

}
else
{

    $item['produk_dominan'] = '-';

}




    // ===============================
    // HARI DOMINAN
    // ===============================


    if(!empty($item['jenis_hari']))
    {

        $jumlahHari = array_count_values(
            $item['jenis_hari']
        );


        arsort($jumlahHari);


        $item['hari_dominan'] =
        array_key_first($jumlahHari);

    }
    else
    {

        $item['hari_dominan'] = '-';

    }




    // ===============================
    // RATA-RATA TRANSAKSI
    // ===============================

$centroid = $this->analisis
                ->getCentroidByCluster($item['cluster']);


if($centroid)
{

    $item['rata_rata'] =
        (($centroid['total'] * (468000 - 1000)) + 1000);

}
else
{

    $item['rata_rata'] = 0;

}


}
usort($karakteristik, function($a, $b){

    return intval(substr($a['cluster'],1))
        <=>
    intval(substr($b['cluster'],1));

});


        $data=[


            'title'=>'Analisis Data',


            'cluster'=>$clusterData,


            'ringkasan'=>
            $this->analisis->getRingkasan(),


            'karakteristik'=>
            array_values($karakteristik),



           'evaluasi'=>
            $this->analisis
            ->getEvaluasiTerakhir(),

            'hasil_llm'=>$this->gabungInterpretasi($karakteristik),

        ];



        return view(
            'analisis_data/index',
            $data
        );


    }

   private function gabungInterpretasi($karakteristik)
{
    $hasilLLM = $this->analisis->getHasilInterpretasi();

    $data = [];


    foreach($hasilLLM as $llm){

        foreach($karakteristik as $cluster){


            // samakan nomor cluster database (0,1,2,3)
            // dengan hasil clustering (C1,C2,C3,C4)

            if(
                'C'.($llm['cluster'] + 1)
                ==
                $cluster['cluster']
            ){


                $data[] = [

                    'cluster' =>
                    'C'.($llm['cluster'] + 1),


                    'nama_segmen' =>
                    $llm['nama_segmen'],


                    'produk_dominan' =>
                    $cluster['produk_dominan'],


                    'jumlah_transaksi' =>
                    $cluster['jumlah'],


                    'pola_hari' =>
                    $cluster['hari_dominan'],


                    'rata_rata_transaksi' =>
                    'Rp '.number_format(
                        $cluster['rata_rata'],
                        0,
                        ',',
                        '.'
                    ),


                    'ringkasan' =>
                    $llm['ringkasan'],


                    'informasi_pendukung' =>
                    json_decode(
                        $llm['informasi_pendukung'],
                        true
                    ),


                    'strategi_promosi' =>
                    json_decode(
                        $llm['strategi_promosi'],
                        true
                    )

                ];


            }

        }

    }
    // urutkan cluster C1 - C4
    usort($data, function($a, $b){

        return intval(substr($a['cluster'],1)) 
            <=> 
            intval(substr($b['cluster'],1));

});


return $data;

    
}

public function proses()
{

    $db = \Config\Database::connect();


    /*
    =====================================
    RESET DATA LAMA
    =====================================
    */

    $db->query("SET FOREIGN_KEY_CHECKS=0");

    $db->table('cluster')->truncate();

    $db->table('evaluasi')->truncate();

    $db->table('preprocessing')->truncate();

    $db->query("SET FOREIGN_KEY_CHECKS=1");



    /*
    =====================================
    AMBIL DATA TRANSAKSI
    FILTER
    - Total > 0
    - Hapus Take Away Service
    =====================================
    */


    $transaksi = $this->transaksi
    ->select([
        'no_struk',
        'tanggal',
        'produk',
        'total'
    ])
    ->findAll();


    $totalTransaksi = count($transaksi);

    $dataCleaning=[];


foreach($transaksi as $row)
{

    if((float)$row['total'] == 0)
    {
        continue;
    }


    $produk = strtolower(trim($row['produk']));


    if($produk == 'take away service')
    {
        continue;
    }


    $dataCleaning[]=$row;

}


$transaksi=$dataCleaning;

$hapus = [];


foreach($this->transaksi->findAll() as $row)
{

    if(
        $row['total'] <= 0 ||
        strpos(
            strtolower(trim($row['produk'])),
            'take away'
        ) !== false
    ){

        $hapus[]=$row;

    }

}



    if(empty($transaksi))
    {

        return redirect()
        ->back()
        ->with(
            'error',
            'Data transaksi kosong'
        );

    }




    /*
    =====================================
    AMBIL DATA KATEGORI PRODUK
    =====================================
    */


    $dataKategori=[];


    $kategoriProduk = $this->kategori->findAll();


    foreach($kategoriProduk as $k)
    {

        $dataKategori[$k['produk']]
        =
        $k['kategori'];

    }
/*
=====================================
3. AGREGASI DATA
BERDASARKAN NO STRUK
=====================================
*/


$hasilAgregasi=[];


foreach($transaksi as $row)
{

    $noStruk=$row['no_struk'];


    if(!isset($hasilAgregasi[$noStruk]))
    {

        $hasilAgregasi[$noStruk]=[

            'no_struk'=>$noStruk,

            'tanggal'=>$row['tanggal'],

            'produk'=>[],

            'total'=>0

        ];

    }


    $hasilAgregasi[$noStruk]['produk'][]=
        $row['produk'];


    $hasilAgregasi[$noStruk]['total'] +=
        $row['total'];

}



$hasilAgregasi=array_values($hasilAgregasi);
   
/*
=====================================
4. TRANSFORMASI DATA

Tanggal → Hari
Produk → Kategori Produk
=====================================
*/


$transformasi=[];


foreach($hasilAgregasi as $row)
{


    $hariInggris=date(
        'l',
        strtotime($row['tanggal'])
    );



    $hariIndonesia=[

        'Monday'=>'Senin',
        'Tuesday'=>'Selasa',
        'Wednesday'=>'Rabu',
        'Thursday'=>'Kamis',
        'Friday'=>'Jumat',
        'Saturday'=>'Sabtu',
        'Sunday'=>'Minggu'

    ];



    $hari =
    $hariIndonesia[$hariInggris];



    if(
        in_array(
            $hari,
            ['Sabtu','Minggu']
        )
    )
    {

        $jenisHari='Weekend';

        $hariEncode=1;

    }
    else
    {

        $jenisHari='Weekday';

        $hariEncode=0;

    }



    $kategori=[];



    foreach($row['produk'] as $produk)
    {

        $produkCari = strtolower(trim($produk));


        foreach($dataKategori as $namaProduk=>$kat)
        {

            if(
                strtolower(trim($namaProduk))
                ==
                $produkCari
            )
            {

                $kategori[] = $kat;
                break;

            }

        }

    }


    $transformasi[]=[


        'no_struk'=>$row['no_struk'],


        'tanggal'=>$row['tanggal'],


        'hari'=>$hari,


        'jenis_hari'=>$jenisHari,


        'hari_encode'=>$hariEncode,


        'produk'=>implode(
            ", ",
            $row['produk']
        ),


        'kategori'=>$kategori,


        'total'=>$row['total']


    ];

    if(empty($kategori))
{
    echo "Kategori kosong : ";
    print_r($row['produk']);
    echo "<br>";
}

}


/*
=====================================
5. COUNT ENCODING KATEGORI PRODUK
=====================================
*/


$encoding=[];


foreach($transformasi as $row)
{


    $kategori = $row['kategori'];



    $encoding[]=[


        'no_struk'=>$row['no_struk'],


        'tanggal'=>$row['tanggal'],
        'jenis_hari'=>$row['jenis_hari'],

        // hasil encoding hari
        'hari'=>$row['hari_encode'],


        'hari_encode'=>$row['hari_encode'],


        'produk'=>$row['produk'],



        // COUNT ENCODING PRODUK

        'kopi'=>count(
            array_filter(
                $kategori,
                function($x){

                    return $x=="Kopi";

                }
            )
        ),



        'non_kopi'=>count(
            array_filter(
                $kategori,
                function($x){

                    return $x=="Non-Kopi";

                }
            )
        ),



        'makanan'=>count(
            array_filter(
                $kategori,
                function($x){

                    return $x=="Makanan";

                }
            )
        ),



        'snack'=>count(
            array_filter(
                $kategori,
                function($x){

                    return $x=="Snack";

                }
            )
        ),



        'total'=>$row['total']

    ];


}
//dd($encoding[0], $encoding[1], $encoding[2]);

/*
=====================================
NORMALISASI MIN MAX
ATRIBUT:
Kopi
Non-Kopi
Makanan
Snack
Total
=====================================
*/


$atributNormalisasi = [

    'kopi',
    'non_kopi',
    'makanan',
    'snack',
    'total'

];



// mencari nilai min dan max setiap atribut

$nilaiMinMax=[];


foreach($atributNormalisasi as $atribut)
{


    $nilai = array_column(
        $encoding,
        $atribut
    );


    $nilaiMinMax[$atribut] = [

        'min'=>min($nilai),

        'max'=>max($nilai)

    ];

}




$normalisasiData=[];

foreach($encoding as $row)
{

$normalisasiData[]=[


    'no_struk' => $row['no_struk'],

    'produk' => $row['produk'],

    'hari' => $row['hari'] ?? '-',

    'jenis_hari' => $row['jenis_hari'] ?? '-',

    'hari_encode' => $row['hari_encode'],


    // normalisasi kategori produk
    'menu_kopi' =>
        $this->minMax(
            $row['kopi'],
            0,
            5
        ),

    'menu_non_kopi' =>
        $this->minMax(
            $row['non_kopi'],
            0,
            5
        ),

    'menu_makanan' =>
        $this->minMax(
            $row['makanan'],
            0,
            5
        ),

    'menu_snack' =>
        $this->minMax(
            $row['snack'],
            0,
            4
        ),


    // nilai asli untuk laporan
    'total_asli' => $row['total'],

    // normalisasi total
    'total_normalisasi' =>
        $this->minMax(
            $row['total'],
            4000,
            666000
        )

];


}

//dd($encoding[0], $normalisasiData[0]);
    /*
    =====================================
    SIMPAN HASIL PREPROCESSING
    =====================================
    */

//dd($normalisasiData[0]);

    foreach($normalisasiData as $row)
    {

//dd($row);
        $db->table('preprocessing')
        ->insert([


            'no_struk'=>$row['no_struk'],

            'hari'=>$row['hari'],

            'jenis_hari'=>$row['jenis_hari'],

            'hari_encode'=>$row['hari_encode'],

            'menu_kopi'=>$row['menu_kopi'],

            'menu_non_kopi'=>$row['menu_non_kopi'],

            'menu_makanan'=>$row['menu_makanan'],

            'menu_snack'=>$row['menu_snack'],

            'total_asli'=>$row['total_asli'],

            'total_normalisasi'=>$row['total_normalisasi']


        ]);


    }




        /*
        =====================================
        AMBIL DATA HASIL PREPROCESSING
        UNTUK K-MEANS
        =====================================
        */


        $rows = $db
    ->table('preprocessing')
    ->orderBy('no_struk','ASC')
    ->get()
    ->getResultArray();



        if(count($rows)<4)
        {

            return redirect()
            ->back()
            ->with(
                'error',
                'Data preprocessing tidak cukup.'
            );

        }





        /*
        =====================================
        MEMBENTUK DATA FITUR
        =====================================
        */


        $dataset=[];



foreach($rows as $row)
{
   $dataset[]=[

    'id_preprocessing'=>$row['id_preprocessing'],

    'hari'=>(float)$row['hari_encode'],

    'menu_kopi'=>(float)$row['menu_kopi'],

    'menu_non_kopi'=>(float)$row['menu_non_kopi'],

    'menu_makanan'=>(float)$row['menu_makanan'],

    'menu_snack'=>(float)$row['menu_snack'],

    'total_normalisasi'=>(float)$row['total_normalisasi']

];
}
        


//dd($rows[0]);

        /*
        =====================================
        K-MEANS
        =====================================
        */


        $kOptimal = $this->request->getPost('jumlah_cluster');

        if(empty($kOptimal)){
            $kOptimal = 4;
        }

        $kOptimal = (int)$kOptimal;

        if($kOptimal < 2)
        {
            return redirect()
            ->back()
            ->with(
                'error',
                'Jumlah cluster minimal 2.'
            );
        }
//dd($dataset[0]);


if($kOptimal == 4)
{

    $centroid = [

        [
            'hari'=>0,
            'menu_kopi'=>0.2,
            'menu_non_kopi'=>0.2,
            'menu_makanan'=>0.2,
            'menu_snack'=>0.25,
            'total_normalisasi'=>0.094
        ],

        [
            'hari'=>1,
            'menu_kopi'=>0.2,
            'menu_non_kopi'=>0.2,
            'menu_makanan'=>0,
            'menu_snack'=>0.5,
            'total_normalisasi'=>0.118
        ],

        [
            'hari'=>1,
            'menu_kopi'=>0.2,
            'menu_non_kopi'=>0,
            'menu_makanan'=>0.2,
            'menu_snack'=>0,
            'total_normalisasi'=>0.060
        ],

        [
            'hari'=>0,
            'menu_kopi'=>0,
            'menu_non_kopi'=>0.2,
            'menu_makanan'=>0,
            'menu_snack'=>0,
            'total_normalisasi'=>0.032
        ]

    ];

}
else
{

    $centroid = [];

    $randomIndex = array_rand($dataset, $kOptimal);

    foreach($randomIndex as $index)
    {
        $centroid[] = [
            'hari'=>$dataset[$index]['hari'],
            'menu_kopi'=>$dataset[$index]['menu_kopi'],
            'menu_non_kopi'=>$dataset[$index]['menu_non_kopi'],
            'menu_makanan'=>$dataset[$index]['menu_makanan'],
            'menu_snack'=>$dataset[$index]['menu_snack'],
            'total_normalisasi'=>$dataset[$index]['total_normalisasi']
        ];
    }

}


        $berubah=true;



       while($berubah)
        {

            $cluster=[];


            // ===============================
            // ASSIGN CLUSTER
            // ===============================
//dd(count($dataset), $dataset[0], $dataset[1], $dataset[2]);

            foreach($dataset as $row)
            {

                $jarak=[];


                foreach($centroid as $index=>$c)
                {

                    $jarak[$index] = sqrt(

                        pow($row['hari'] - $centroid[$index]['hari'],2) +

                        pow($row['menu_kopi'] - $centroid[$index]['menu_kopi'],2) +

                        pow($row['menu_non_kopi'] - $centroid[$index]['menu_non_kopi'],2) +

                        pow($row['menu_makanan'] - $centroid[$index]['menu_makanan'],2) +

                        pow($row['menu_snack'] - $centroid[$index]['menu_snack'],2) +

                        pow($row['total_normalisasi'] - $centroid[$index]['total_normalisasi'],2)

                    );

                }


                // cek jarak 1 data dulu
                //dd($jarak);


                $clusterIndex = array_search(
                    min($jarak),
                    $jarak
                );


                $cluster[$clusterIndex][] = $row;

            }


            // cek jumlah anggota iterasi awal
           // dd([
              //  'Iterasi Awal',
                //'C1'=>count($cluster[0] ?? []),
                //'C2'=>count($cluster[1] ?? []),
                //'C3'=>count($cluster[2] ?? []),
                //'C4'=>count($cluster[3] ?? [])
            //]);

            /*
            ===============================
            UPDATE CENTROID
            ===============================
            */


            $centroidBaru=[];



            for($i=0;$i<$kOptimal;$i++)
            {


                if(!isset($cluster[$i]))
                {

                    $centroidBaru[$i]
                    =
                    $centroid[$i];

                    continue;

                }



                $anggota=$cluster[$i];


                $jumlah=count($anggota);



              $centroidBaru[$i]=[

            'hari' =>
            round(
                array_sum(array_column($anggota,'hari'))/$jumlah,
                3
            ),


            'menu_kopi' =>
            round(
                array_sum(array_column($anggota,'menu_kopi'))/$jumlah,
                3
            ),


            'menu_non_kopi' =>
            round(
                array_sum(array_column($anggota,'menu_non_kopi'))/$jumlah,
                3
            ),


            'menu_makanan' =>
            round(
                array_sum(array_column($anggota,'menu_makanan'))/$jumlah,
                3
            ),


            'menu_snack' =>
            round(
                array_sum(array_column($anggota,'menu_snack'))/$jumlah,
                3
            ),


            'total_normalisasi' =>
            round(
                array_sum(array_column($anggota,'total_normalisasi'))/$jumlah,
                3
            )

            ];


            }



            $berubah=false;



            for($i=0;$i<$kOptimal;$i++)
            {

                $selisih = 0;


                foreach($centroid[$i] as $key=>$nilai)
                {

                    $selisih += abs(
                        $nilai - $centroidBaru[$i][$key]
                    );

                }


                if($selisih > 0.001)
                {

                    $berubah = true;

                    break;

                }


            }


           $centroid = $centroidBaru;


// simpan cluster terakhir setelah centroid stabil
$clusterAkhir = $cluster;

}


// =====================================
// SIMPAN CENTROID AKHIR
// =====================================

$db->table('centroid')->truncate();


foreach($centroid as $index=>$c)
{

    $db->table('centroid')->insert([

        'cluster'=>'C'.($index+1),

        'menu_kopi'=>$c['menu_kopi'],

        'menu_non_kopi'=>$c['menu_non_kopi'],

        'menu_makanan'=>$c['menu_makanan'],

        'menu_snack'=>$c['menu_snack'],

        'hari_encode'=>$c['hari'],

        'total'=>$c['total_normalisasi']

    ]);

}



// =====================================
// SIMPAN HASIL CLUSTER TERAKHIR
// =====================================

$insertCluster=[];


foreach($clusterAkhir as $clusterIndex=>$anggota)
{


    foreach($anggota as $row)
    {

        $insertCluster[]=[

            'id_preprocessing'=>$row['id_preprocessing'],

            'cluster'=>'C'.($clusterIndex+1)

        ];

    }

}


$this->analisis
->insertBatch($insertCluster);



// =====================================
// SIMPAN DETAIL TAHAP PREPARATION
// =====================================

session()->set([

    'tahap_preparation'=>[

        'data_awal' => $totalTransaksi,

        'atribut'=>[
            'No. Struk',
            'Tanggal',
            'Produk',
            'Total'
        ],

        

        'cleaning'=>$transaksi,


        'agregasi'=>array_values($hasilAgregasi),


        'transformasi'=>$transformasi,


        'encoding'=>$encoding,


        'normalisasi'=>$normalisasiData

    ]

]);
       // dd($centroidBaru);
                /*
        =====================================
        SIMPAN DETAIL TAHAP PREPARATION
        UNTUK BUTTON DETAIL
        =====================================
        */


       session()->set([

    'tahap_preparation'=>[

        'data_awal' => $totalTransaksi,

        'atribut'=>[
            'No. Struk',
            'Tanggal',
            'Produk',
            'Total'
        ],


        'cleaning'=>$transaksi,


        'agregasi'=>array_values($hasilAgregasi),


        'transformasi'=>$transformasi,


        'encoding'=>$encoding,


        'normalisasi'=>$normalisasiData

    ]

]);





// =====================================
// HITUNG SILHOUETTE SCORE
// MENGGUNAKAN CLUSTER AKHIR
// =====================================

$silhouette = [];


// fungsi euclidean distance
$hitungJarak = function($a, $b)
{

    return sqrt(

        pow(
            $a['hari'] - $b['hari'],
            2
        )

        +

        pow(
            $a['menu_kopi'] - $b['menu_kopi'],
            2
        )

        +

        pow(
            $a['menu_non_kopi'] - $b['menu_non_kopi'],
            2
        )

        +

        pow(
            $a['menu_makanan'] - $b['menu_makanan'],
            2
        )

        +

        pow(
            $a['menu_snack'] - $b['menu_snack'],
            2
        )

        +

        pow(
            $a['total_normalisasi']
            -
            $b['total_normalisasi'],
            2
        )

    );

};



foreach($clusterAkhir as $clusterId=>$anggota)
{


    foreach($anggota as $index=>$dataA)
    {


        // =========================
        // a(i) rata-rata jarak dalam cluster
        // =========================

        $a = 0;


        if(count($anggota)>1)
        {


            foreach($anggota as $j=>$dataB)
            {


                if($index==$j)
                    continue;


                $a += $hitungJarak(
                    $dataA,
                    $dataB
                );


            }


            $a =
            $a / (count($anggota)-1);


        }




        // =========================
        // b(i) jarak minimum cluster lain
        // =========================

        $b = PHP_FLOAT_MAX;



        foreach($clusterAkhir as $clusterLain=>$anggotaLain)
        {


            if($clusterLain==$clusterId)
                continue;



            if(count($anggotaLain)==0)
                continue;



            $totalJarak = 0;



            foreach($anggotaLain as $dataB)
            {


                $totalJarak +=
                $hitungJarak(
                    $dataA,
                    $dataB
                );


            }



            $rataJarak =
            $totalJarak /
            count($anggotaLain);



            if($rataJarak < $b)
            {
                $b = $rataJarak;
            }


        }




        // =========================
        // nilai silhouette setiap data
        // =========================

        if(
            max($a,$b)>0 &&
            $b != PHP_FLOAT_MAX
        )
        {

            $silhouette[] =
            ($b-$a) /
            max($a,$b);

        }


    }


}



$nilaiSilhouette =
count($silhouette)>0
?
array_sum($silhouette) / count($silhouette)
:
0;







        /*
        =====================================
        SIMPAN EVALUASI
        =====================================
        */


       $this->analisis->simpanEvaluasi([

            'k'=>$kOptimal,

            'nilai_silhouette'=>round(
                $nilaiSilhouette,
                4
            ),

            'tanggal_proses'=>date(
                'Y-m-d H:i:s'
            )

        ]);

        // Setelah clustering selesai, langsung lanjut ke proses interpretasi LLM.
        // Dengan begitu pengguna tidak perlu menekan tombol "Buat Promosi" lagi.
        return redirect()->to('/analisis-data/generate');


    
    }


public function generate()
{

    // =====================================
    // DATA CLUSTERING
    // =====================================

   $dataCluster = $this->analisis->getRingkasan();

// perbaiki produk dominan berdasarkan centroid akhir
foreach($dataCluster as &$row)
{

    $centroid = $this->analisis
        ->getCentroidByCluster($row['cluster']);


    if($centroid)
    {

        $produk = [

            "Kopi" =>
            $centroid['menu_kopi'],

            "Non-Kopi" =>
            $centroid['menu_non_kopi'],

            "Makanan" =>
            $centroid['menu_makanan'],

            "Snack" =>
            $centroid['menu_snack']

        ];


        arsort($produk);


        $produkDominan = array_slice(
            array_keys($produk),
            0,
            2
        );


        $row['produk_dominan'] =
            implode(" + ", $produkDominan);

    }

}

unset($row);

    $clusterInfo = "";


    foreach($dataCluster as $row)
{
    
    $clusterInfo .= "

========================
CLUSTER ".$row['cluster']."
========================

Jumlah Transaksi :
".$row['jumlah']." data

Produk Dominan :
".($row['produk_dominan'] ?? '-')."

Target Promosi :
    ".$this->cariTargetPromo(
    $row['produk_dominan']
    )."

Pola Hari :
".($row['hari_dominan'] ?? '-')."

Rata-rata Transaksi :
Rp ".number_format($row['rata_rata'],0,',','.')."

";



    }


// =====================================
// DISTRIBUSI DATA KUESIONER
// =====================================

$distribusi = $this->getDistribusiKuesioner();


$infoKuesioner = "";


$urutanStatus = [
    'Pekerja',
    'Mahasiswa',
    'Pelajar'
];


foreach($urutanStatus as $status)
{

    if(!isset($distribusi[$status]))
        continue;


    $item = $distribusi[$status];


    $infoKuesioner .= "
    ========================
    STATUS : ".$status."
    ========================
    
    Jumlah Responden :
    ".$item['jumlah']." orang
    ";


foreach($item['usia'] as $usia=>$persen)
{
    $infoKuesioner .= "
- ".$usia." : ".$persen;
}



$infoKuesioner .= "


Distribusi Produk :
";


foreach($item['produk'] as $produk=>$persen)
{
    $infoKuesioner .= "
- ".$produk." : ".$persen;
}



$infoKuesioner .= "
Kunjungan : ";
    
foreach($item['jenis_hari'] as $hari=>$persen)
{
    $infoKuesioner .= 
    $hari." (".$persen."), ";
}


$infoKuesioner .= "
Waktu : ";

foreach($item['periode_waktu'] as $waktu=>$persen)
{
    $infoKuesioner .= 
    $waktu." (".$persen."), ";
}

$infoKuesioner .= "

";

}
//dd($clusterInfo);


   $prompt = "

Anda adalah Marketing Data Analyst cafe.

Tugas:
Interpretasikan hasil clustering K-Means dan buat rekomendasi promosi berdasarkan data.

Gunakan:
- Clustering sebagai sumber utama.
- Kuesioner sebagai pendukung target promosi.


DATA CLUSTERING:

".$clusterInfo."


DATA KUESIONER:

".$infoKuesioner."


ATURAN:

1. Produk dominan, jumlah transaksi, pola hari, dan rata-rata transaksi harus sama dengan hasil clustering.

2. Produk dominan tidak boleh diubah.
Contoh:
Kopi + Snack tetap Kopi + Snack.

3. Nama segmen dibuat langsung dari produk_dominan maksimal 2 produk:
Kopi = Penikmat Kopi
Kopi + Snack = Penikmat Kopi dan Snack
Kopi + Makanan = Penikmat Kopi dan Makanan
Kopi + Non-Kopi = Penikmat Kopi dan Non-Kopi
Non-Kopi + Snack = Penikmat Non-Kopi dan Snack


4. Kuesioner hanya digunakan sebagai informasi pendukung untuk menggambarkan karakteristik pelanggan, bukan sebagai pembentuk cluster.

5. Informasi pendukung kuesioner harus menampilkan karakteristik pelanggan berdasarkan status:
- Mahasiswa
- Pelajar
- Pekerja

Jelaskan setiap status berdasarkan:
- hari dan waktu kunjungan dominan
- Produk dominan yang sering dibeli beserta persentasenya.
- Persentase setiap kategori harus berjumlah 100%:
  - Hari kunjungan
  - Waktu kunjungan
  - Produk

Format:
Mahasiswa:
• Kunjungan :
• Produk :

6. Strategi promosi harus berdasarkan:
- produk dominan
- hari transaksi
- waktu kunjungan


7. Strategi promosi:
- Hanya 1 rekomendasi setiap cluster.
- Field produk WAJIB sama persis dengan produk_dominan hasil clustering. Jangan diubah.
- Field nama_promo WAJIB berbeda dari field produk, dilarang keras sama dengan nama produk.
- Field nama_promo harus frasa kampanye/marketing yang catchy, maksimal 10 kata, relevan dengan waktu kunjungan dan target pelanggan.
- Field diskon hanya boleh diskon 10%, diskon 20%, dan paket promo  Paket Hemat: gabungkan harga 2 produk dominan jadi satu harga paket lebih murah menjadi 35000 (hanya boleh digunakan untuk produk yang ada dominan SNACK saja, variasikan tiap cluster. Boleh pakai format: persentase (contoh: diskon 10%, , diskon 20%).
- Sesuaikan gaya bahasa nama_promo dengan target: santai untuk pelajar/mahasiswa, praktis untuk pekerja.
- Gunakan gaya berbeda tiap cluster, jangan pakai pola nama_promo yang sama.

Contoh strategi_promosi yang benar, jangan disalin, buat variasi baru sesuai data:
nama_promo: Malam Santai Hemat, diskon: 10%, produk: Kopi + Non-Kopi, waktu: Weekend Malam, target: Mahasiswa
nama_promo: Nongkrong Sore Hemat, diskon: Paket Hemat Rp35.000, produk: Snack + Kopi, waktu: Weekday Sore, target: Pelajar

Jangan menjelaskan alasan atau tujuan promosi.
Target promosi hanya boleh menggunakan: Mahasiswa, Pelajar, Pekerja diambil sesuai informasi pendukung kuesioner

Jangan membuat:
- bundling
- kombinasi produk
- nama segmen berdasarkan usia/status/waktu
- nama_promo yang sama dengan field produk

8. Penentuan target pelanggan:
Target pelanggan ditentukan berdasarkan kesesuaian produk dominan cluster dengan distribusi produk pada ringkasan kuesioner.

Contoh:
Jika produk dominan cluster = Kopi + Makanan dan pada ringkasan kuesioner Pekerja memiliki persentase pembelian Kopi + Makanan tertinggi, maka target pelanggan = Pekerja.

Waktu kunjungan ditentukan berdasarkan distribusi kunjungan target pelanggan pada ringkasan kuesioner.

Contoh:
Jika Pekerja memiliki kunjungan dominan Weekday dan periode waktu siang, maka waktu promosi = Weekday siang.


FORMAT JSON:

[
{
\"cluster\":\"\",
\"nama_segmen\":\"\",
\"produk_dominan\":\"\",
\"jumlah_transaksi\":\"\",
\"pola_hari\":\"\",
\"rata_rata_transaksi\":\"\",


\"informasi_pendukung\":{

    \"mahasiswa\":{
        \"kunjungan\":\"\",
        \"produk\":\"\"
    },
    \"pelajar\":{
        \"kunjungan\":\"\",
        \"produk\":\"\"
    },
    \"pekerja\":{
        \"kunjungan\":\"\",
        \"produk\":\"\"
    }
},

\"strategi_promosi\":{
    \"nama_promo\":\"\",
    \"diskon\":\"\",
    \"produk\":\"\",
    \"waktu\":\"\",
    \"target\":\"\"
}

}
]

Jangan menambahkan teks selain JSON.

";


    // =====================================
    // REQUEST GEMINI API
    // =====================================


    $apiKey = env('GEMINI_KEY');


    $url =
    "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=".$apiKey;



    $body = [

        "contents"=>[

            [

                "parts"=>[

                    [
                        "text"=>$prompt
                    ]

                ]

            ]

        ],

        "generationConfig"=>[
        "temperature"=>0
    ]

    ];



    $ch = curl_init($url);


    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);


    curl_setopt($ch,CURLOPT_HTTPHEADER,[

        "Content-Type: application/json"

    ]);


    curl_setopt($ch,CURLOPT_POST,true);


    curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($body));


    $response = curl_exec($ch);


    curl_close($ch);



    $result = json_decode($response,true);



    $text =
    $result['candidates'][0]['content']['parts'][0]['text']
    ?? "";



    if(empty($text))
    {
        dd($result);
    }



    $text = str_replace(
        ['```json','```'],
        '',
        $text
    );



   // =====================================
// UBAH JSON GEMINI MENJADI ARRAY
// =====================================
$hasil = json_decode($text, true);


// jika gagal decode
if(!is_array($hasil))
{
    $hasil = [];
}



// ================================
// UPDATE HISTORY HASIL LLM
// ================================


$rekomendasi = [];


foreach($hasil as $item)
{

    $rekomendasi[] = [

        'cluster' => $item['cluster'] ?? '',

        'nama_segmen' => $item['nama_segmen'] ?? '',

        'strategi_promosi' => $item['strategi_promosi'] ?? ''

    ];

}



$historyTerakhir = $this->history
    ->orderBy('id_history','DESC')
    ->first();


if($historyTerakhir){

    $this->history
        ->where('id_history', $historyTerakhir['id_history'])
        ->set([
            'rekomendasi_promosi' => json_encode($rekomendasi)
        ])
        ->update();

}




// =====================================
// SIMPAN HASIL INTERPRETASI KE DATABASE
// =====================================


// hapus hasil interpretasi lama
$this->analisis->hapusInterpretasi();



foreach($hasil as $row)
{

    $hariCluster = "-";


    foreach($dataCluster as $cluster)
    {

        if($cluster['cluster'] == $row['cluster'])
        {

            $row['strategi_promosi']['produk'] =
                $cluster['produk_dominan'];

            $hariCluster =
                $cluster['hari_dominan'];

            break;

        }

    }


    $row['strategi_promosi']['target'] =
    $this->cariTargetPromo(
        $row['produk_dominan']
    );


    $row['strategi_promosi']['waktu'] =
        $hariCluster
        ." ".
        $this->cariPeriodeWaktuPromo(
        $row['strategi_promosi']['target']
        );


    $this->analisis->simpanInterpretasi([

    // ubah C1,C2,C3,C4 menjadi 0,1,2,3
    'nomor_cluster' => 
        intval(str_replace('C','',$row['cluster'])) - 1,


    'nama_segmen' => 
        $row['nama_segmen'],



    'informasi_pendukung' => 
        json_encode(
            $row['informasi_pendukung']
        ),


    'strategi_promosi' => 
        json_encode(
            $row['strategi_promosi']
        ),


    'alasan' =>
        'Berdasarkan hasil clustering dan informasi pendukung pelanggan'

]);



}




// simpan session untuk tampilan
session()->set(
    "hasil_llm",
    $hasil
);


return redirect()
    ->to('/analisis-data')
    ->with(
        'success',
        'Interpretasi LLM berhasil dibuat.'
    );
}

    public function detail()
{
    $data['tahap'] = session()->get('tahap_preparation');

    $data['cluster'] = $this->analisis->getClusterDetail();
    

    // centroid akhir
    $data['centroid'] = $this->analisis->getCentroidAkhir();

    $data['evaluasi'] = $this->analisis->getEvaluasiTerakhir();
    return view('analisis_data/detail', $data);
}


    /*
    =====================================
    RESET
    =====================================
    */

    public function reset()
    {

        $db=\Config\Database::connect();


        $db->query(
            "SET FOREIGN_KEY_CHECKS=0"
        );


        $db->table('cluster')
        ->truncate();


        $db->table('evaluasi')
        ->truncate();


        $db->table('preprocessing')
        ->truncate();

         $db->table('interpretasi_llm')
        ->truncate();

        $db->query(
            "SET FOREIGN_KEY_CHECKS=1"
        );


        // Hapus detail preparation
        session()->remove('tahap_preparation');
        session()->remove('hasil_llm');

        return redirect()
            ->to('/analisis-data')
            ->with(
                'success',
                'Data clustering berhasil direset.'
            );

}
private function kategoriWaktu($jam)
{
    $jam = (int) $jam;

    if ($jam >= 11 && $jam <= 14) {
        return "Siang";
    }

    if ($jam >= 15 && $jam <= 18) {
        return "Sore";
    }

    if ($jam >= 19 && $jam <= 23) {
        return "Malam";
    }

    return "-";
}



private function kategoriHari($hari)
{

    $weekday = [

        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat'

    ];


    if(in_array($hari,$weekday))
    {
        return "Weekday";
    }


    return "Weekend";

}



private function cekKategori($produk)
{
    if(empty($produk))
    {
        return "-";
    }


    $hasil = [];


    $list = explode(',', strtolower($produk));


    foreach($list as $item)
    {

        $item = trim($item);


        $data = $this->kategori
            ->like('produk',$item)
            ->first();



        if($data)
        {

            if(!in_array($data['kategori'],$hasil))
            {
                $hasil[] = $data['kategori'];
            }

        }

    }



    if(empty($hasil))
    {
        return "Tidak diketahui";
    }


    return implode(' + ', $hasil);

}

private function minMax($nilai,$min,$max)
{


    if(($max-$min)==0)
    {

        return 0;

    }


    return round(

        ($nilai-$min)
        /
        ($max-$min),

        4

    );

}
private function cariTargetPromo($produkCluster)
{

    $distribusi = $this->getDistribusiKuesioner();


    $produkCluster = strtolower($produkCluster);


    $produkCluster = str_replace(
        ' ',
        '',
        $produkCluster
    );


    $produkCluster = explode(
        '+',
        $produkCluster
    );


    sort($produkCluster);



    $target="-";
    $persenTertinggi=0;


    $statusPrioritas = [
        'Pekerja',
        'Mahasiswa',
        'Pelajar'
    ];


    foreach($statusPrioritas as $status)
    {

        if(!isset($distribusi[$status]))
            continue;


        $data = $distribusi[$status];


        foreach($data['produk'] as $produk=>$persen)
        {


            $produkKuesioner = strtolower($produk);


            $produkKuesioner = str_replace(
                ' ',
                '',
                $produkKuesioner
            );


            $produkKuesioner = explode(
                '+',
                $produkKuesioner
            );


            sort($produkKuesioner);



            if($produkCluster == $produkKuesioner)
            {

                $nilai = floatval(
                    str_replace(
                        '%',
                        '',
                        $persen
                    )
                );


                if($nilai > $persenTertinggi)
                {

                    $persenTertinggi=$nilai;

                    $target=$status;

                }

            }

        }

    }


    return $target;

}

// =====================================
// DISTRIBUSI KUESIONER
// =====================================

private function getDistribusiKuesioner()
{

    $data = $this->kuesioner->findAll();

    $hasil = [];


    foreach($data as $row)
    {

        $status = $row['status'];


        if(!isset($hasil[$status]))
        {

            $hasil[$status] = [

                'jumlah'=>0,
                'usia'=>[],
                'produk'=>[],
                'jenis_hari'=>[],
                'periode_waktu'=>[]

            ];

        }


        $hasil[$status]['jumlah']++;


        // usia
        $hasil[$status]['usia'][] =
            $row['usia'];



        // kategori produk responden

        $kategoriMinuman =
            $this->cekKategori($row['menu_minuman']);

        $kategoriMakanan =
            $this->cekKategori($row['menu_makanan']);


        // gabungkan kategori sebagai 1 kombinasi
        $produk = "";


        if(
            $kategoriMinuman != "" &&
            $kategoriMinuman != "-"
        )
        {
            $produk .= $kategoriMinuman;
        }


        if(
            $kategoriMakanan != "" &&
            $kategoriMakanan != "-"
        )
        {

            if($produk != "")
            {
                $produk .= " + ";
            }

            $produk .= $kategoriMakanan;

        }


        if($produk != "")
        {
            $hasil[$status]['produk'][] = $produk;
        }

        // weekday / weekend
        $hasil[$status]['jenis_hari'][] =
            $this->kategoriHari(
                $row['hari_kunjungan']
            );



        // siang / sore / malam
        $hasil[$status]['periode_waktu'][] =
            $this->kategoriWaktu(
                $row['waktu_kunjungan']
            );

    }



    foreach($hasil as &$item)
    {

        $jumlah = $item['jumlah'];


        foreach(
            [
                'usia',
                'produk',
                'jenis_hari',
                'periode_waktu'
            ]
            as $kolom
        )
        {

            // pastikan hanya data string
            $item[$kolom] = array_filter(
                $item[$kolom],
                function($x){
                    return is_string($x) || is_int($x);
                }
            );


            $data = array_count_values(
                $item[$kolom]
            );


            arsort($data);


            $temp = [];

            foreach($data as $key=>$nilai)
            {

                if($kolom == 'produk')
                {
                    $totalHitung = array_sum($data);
                }
                else
                {
                    $totalHitung = $jumlah;
                }


                $temp[$key] =
                    round(
                        ($nilai/$totalHitung)*100,
                        2
                    )."%";

            }
            $item[$kolom] = $temp;

        }

    }


    return $hasil;

}
private function cariPeriodeWaktuPromo($target)
{
    $distribusi = $this->getDistribusiKuesioner();


    if(!isset($distribusi[$target]))
    {
        return "-";
    }


    $data = $distribusi[$target];


    $waktu = $data['periode_waktu'];


    arsort($waktu);


    return array_key_first($waktu);
}

}