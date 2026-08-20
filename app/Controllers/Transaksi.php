<?php

namespace App\Controllers;

use App\Models\TransaksiModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class Transaksi extends BaseController
{
    public function index()
    {
        $model = new TransaksiModel();

        $data = [
            'title'      => 'Riwayat Transaksi',
            'transaksi'  => $model->findAll()
        ];

        return view('transaksi/index', $data);
    }

    public function upload()
    {
        $file = $this->request->getFile('file_excel');

        if (!$file->isValid()) {
            return redirect()->back()
                ->with('error', 'File tidak valid');
        }

        $spreadsheet = IOFactory::load($file->getTempName());
        $sheet = $spreadsheet->getActiveSheet();

        // Ambil semua data
        $rows = $sheet->toArray();

        $model = new TransaksiModel();

        foreach ($rows as $key => $row) {

            // Skip header
            if ($key == 0) {
                continue;
            }

            /*
            ============================================
            KONVERSI TANGGAL EXCEL
            ============================================
            */

            $tanggalCell = $sheet->getCell('B' . ($key + 1))->getValue();

            if (is_numeric($tanggalCell)) {

                // Jika kolom bertipe Date di Excel
                $tanggal = Date::excelToDateTimeObject($tanggalCell)
                    ->format('Y-m-d');

            } else {

                // Jika berupa teks misalnya 30-04-2026
                $tanggal = date(
                    'Y-m-d',
                    strtotime(
                        str_replace('/', '-', trim($tanggalCell))
                    )
                );

            }

            /*
            ============================================
            SIMPAN DATA
            ============================================
            */

            $model->insert([

                'no_struk'              => $row[0],
                'tanggal'               => $tanggal,
                'jam'                   => $row[2],
                'nama_kasir'            => $row[3],
                'produk'                => $row[4],
                'jumlah_produk'         => $row[5],
                'jumlah_dibatalkan'     => $row[6],
                'harga_per_produk'      => $row[7],
                'subtotal'              => $row[8],
                'tipe_harga'            => $row[9],
                'diskon_produk'         => $row[10],
                'tipe_diskon_produk'    => $row[11],
                'total'                 => $row[12],
                'status'                => $row[13],
                'metode_pembayaran'     => $row[14]

            ]);
        }

        return redirect()->to('/transaksi')
            ->with('success', 'Data transaksi berhasil diupload.');
    }

    public function reset()
    {
        $db = \Config\Database::connect();

        // Matikan sementara Foreign Key
        $db->query("SET FOREIGN_KEY_CHECKS=0");

        // Hapus data
        $db->query("DELETE FROM preprocessing");
        $db->query("DELETE FROM transaksi");

        // Reset AUTO_INCREMENT
        $db->query("ALTER TABLE preprocessing AUTO_INCREMENT = 1");
        $db->query("ALTER TABLE transaksi AUTO_INCREMENT = 1");

        // Aktifkan lagi Foreign Key
        $db->query("SET FOREIGN_KEY_CHECKS=1");

        return redirect()->to('/transaksi')
            ->with('success', 'Data transaksi berhasil direset.');
    }
}