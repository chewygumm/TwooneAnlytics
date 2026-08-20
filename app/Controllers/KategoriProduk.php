<?php

namespace App\Controllers;

use App\Models\KategoriProdukModel;

class KategoriProduk extends BaseController
{

    public function index()
    {
        $model = new KategoriProdukModel();

        $data = [
            'title' => 'Kategori Produk',
            'produk' => $model->findAll()
        ];

        return view('kategori_produk/index', $data);
    }


    public function simpan()
    {

        $model = new KategoriProdukModel();


        $model->insert([

            'produk' => $this->request->getPost('produk'),

            'kategori' => $this->request->getPost('kategori')

        ]);


        return redirect()->to('/kategori-produk')
            ->with('success','Kategori produk berhasil ditambahkan');

    }



    public function hapus($id)
    {

        $model = new KategoriProdukModel();

        $model->delete($id);


        return redirect()->to('/kategori-produk')
            ->with('success','Data berhasil dihapus');

    }

}