<?php

namespace App\Controllers;

use App\Models\KuesionerModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Kuesioner extends BaseController
{
    public function index()
    {
        $model = new KuesionerModel();

        $data = [
            'title' => 'Data Kuesioner',
            'kuesioner' => $model->findAll()
        ];

        return view('kuesioner/index', $data);
    }

    public function upload()
    {
        $file = $this->request->getFile('file_excel');

        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid.');
        }

        $spreadsheet = IOFactory::load($file->getTempName());
        $rows = $spreadsheet->getActiveSheet()->toArray();

        $model = new KuesionerModel();

        foreach ($rows as $key => $row) {

            if ($key == 0) continue;

            $model->insert([
                'nama'             => $row[0],
                'usia'             => $row[1],
                'status'           => $row[2],
                'hari_kunjungan'   => $row[3],
                'waktu_kunjungan'  => $row[4],
                'menu_minuman'     => $row[5],
                'menu_makanan'     => $row[6],
                'no_hp'            => $row[7]
            ]);
        }

        return redirect()->to('/kuesioner')
            ->with('success', 'Data kuesioner berhasil diupload.');
    }

    public function reset()
    {
        $db = db_connect();

        $db->query("DELETE FROM kuesioner");
        $db->query("ALTER TABLE kuesioner AUTO_INCREMENT=1");

        return redirect()->to('/kuesioner')
            ->with('success', 'Data berhasil dihapus.');
    }
}