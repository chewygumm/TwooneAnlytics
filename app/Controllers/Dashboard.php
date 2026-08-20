<?php

namespace App\Controllers;

use App\Models\TransaksiModel;
use App\Models\KuesionerModel;
use App\Models\AnalisisModel;

class Dashboard extends BaseController
{

    protected $transaksi;
    protected $kuesioner;
    protected $analisis;


    public function __construct()
    {
        $this->transaksi = new TransaksiModel();
        $this->kuesioner = new KuesionerModel();
        $this->analisis = new AnalisisModel();
    }


    public function index()
    {


        /*
        ======================
        STATISTIK
        ======================
        */


        // Total transaksi
        $totalTransaksi = $this->transaksi
            ->countAllResults();



        // Total kuesioner (hanya data yang memiliki nama)
        $totalKuesioner = $this->kuesioner
            ->where('nama !=', '')
            ->where('nama IS NOT NULL')
            ->countAllResults();



        // Ambil hasil evaluasi terakhir
        $evaluasi = $this->analisis
        ->getEvaluasiTerakhir();


        $jumlahCluster = $evaluasi['k'] ?? 0;


        $silhouette = $evaluasi['nilai_silhouette'] ?? 0;


        /*
        ======================
        GRAFIK TRANSAKSI BULAN
        ======================
        */

        $grafikTransaksi = $this->transaksi
            ->select("
                DATE_FORMAT(tanggal,'%Y-%m') as periode,
                COUNT(id_transaksi) as jumlah_transaksi,
                SUM(total) as total_transaksi
            ")
            ->groupBy("DATE_FORMAT(tanggal,'%Y-%m')")
            ->orderBy('periode','ASC')
            ->findAll();



        /*
        ======================
        DISTRIBUSI CLUSTER
        ======================
        */


        $grafikCluster = $this->analisis
            ->select("
                cluster,
                COUNT(*) as jumlah
            ")
            ->groupBy('cluster')
            ->orderBy('cluster','ASC')
            ->findAll();





        /*
        ======================
        DATA TRANSAKSI TERBARU
        ======================
        */


        $transaksiTerbaru = $this->transaksi
            ->orderBy('id_transaksi','DESC')
            ->limit(5)
            ->find();





        $data = [


            'title'=>'Dashboard',


            'totalTransaksi'=>$totalTransaksi,


            'totalKuesioner'=>$totalKuesioner,


            'jumlahCluster'=>$jumlahCluster,


            'silhouette'=>$silhouette,


            'grafikTransaksi'=>$grafikTransaksi,


            'grafikCluster'=>$grafikCluster,


            'transaksiTerbaru'=>$transaksiTerbaru


        ];



        return view('dashboard/index',$data);


    }

}