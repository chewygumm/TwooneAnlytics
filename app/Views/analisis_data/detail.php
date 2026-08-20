<?= $this->include('layouts/header') ?>


<div class="flex min-h-screen">


<?= $this->include('layouts/sidebar') ?>


<main class="ml-64 flex-1 px-6 py-6">


<?= $this->include('layouts/navbar') ?>


<section class="mt-6">


<div class="rounded-xl bg-white p-6 shadow">


<div class="mb-6">

<a href="<?= base_url('analisis-data') ?>"
class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-3 text-sm font-medium text-white">

Kembali

</a>

</div>



<h2 class="mb-6 text-xl font-bold text-gray-800">
Detail Tahapan Data Preparation
</h2>



<?php if(!empty($tahap)): ?>
<!-- ==========================
DATA AWAL
========================== -->

<div class="mb-5 rounded-xl border p-5">

<h3 class="font-semibold mb-3">
Data Awal
</h3>

<p>

<b>
<?= number_format($tahap['data_awal'],0,',','.') ?>
</b>
data transaksi
</p>

</div>


<!-- ==========================
1 PEMILIHAN ATRIBUT
========================== -->


<div class="mb-5 rounded-xl border p-5">


<h3 class="font-semibold mb-3">
1. Pemilihan Atribut
</h3>


<p>
Atribut yang digunakan:
<b>
<?= implode(', ', $tahap['atribut'] ?? []) ?>
</b>
</p>


</div>





<!-- ==========================
2 CLEANING DATA
========================== -->


<div class="mb-5 rounded-xl border p-5">


<h3 class="font-semibold mb-3">
2. Cleaning Data
</h3>


<p>

Jumlah data setelah cleaning:

<b>
<?= count($tahap['cleaning'] ?? []) ?>
</b>

data

</p>


<p class="text-sm text-gray-500 mt-2">

Data yang dihapus:
Total = 0 dan Produk = Take Away Service

</p>


</div>





<!-- ==========================
3 AGREGASI
========================== -->


<div class="mb-5 rounded-xl border p-5">


<h3 class="font-semibold mb-3">
3. Agregasi Data Berdasarkan No Struk
</h3>


<p class="mb-4">

Jumlah transaksi setelah agregasi:

<b>
<?= count($tahap['agregasi'] ?? []) ?>
</b>

transaksi

</p>



<table class="w-full text-sm">


<thead class="bg-gray-100">

<tr>

<th class="p-3">
No Struk
</th>


<th class="p-3">
Tanggal
</th>


<th class="p-3">
Produk
</th>


<th class="p-3">
Total
</th>


</tr>

</thead>



<tbody>


<?php foreach(array_slice($tahap['agregasi'] ?? [],0,5) as $row): ?>


<tr class="border-b">


<td class="p-3">
<?= $row['no_struk'] ?? '-' ?>
</td>


<td class="p-3">
<?= $row['tanggal'] ?? '-' ?>
</td>


<td class="p-3">

<?= isset($row['produk']) 
? implode(', ', $row['produk']) 
: '-' ?>

</td>


<td class="p-3">

Rp <?= number_format($row['total'] ?? 0,0,',','.') ?>

</td>


</tr>


<?php endforeach ?>


</tbody>


</table>


</div>





<!-- ==========================
4 TRANSFORMASI
========================== -->


<div class="mb-5 rounded-xl border p-5">


<h3 class="font-semibold mb-3">
4. Transformasi Data
</h3>


<p class="mb-4">

Transformasi yang dilakukan:
</p>

<ul class="list-disc ml-6 text-sm">

<li>
Tanggal diubah menjadi jenis hari:
<b>Weekday dan Weekend</b>
</li>

<li>
Produk ditransformasikan menjadi kategori menu yaitu:
<b>Kopi, Non-Kopi, Makanan, dan Snack</b>
</li>


</ul>



<div class="mt-6">

<table class="w-full text-sm">


<thead class="bg-gray-100">


<tr>


<th class="p-3">
Tanggal
</th>


<th class="p-3">
Hari
</th>


<th class="p-3">
Jenis Hari
</th>


<th class="p-3">
Produk
</th>


<th class="p-3">
Kategori
</th>


</tr>


</thead>



<tbody>


<?php foreach(array_slice($tahap['transformasi'] ?? [],0,5) as $row): ?>


<tr class="border-b">


<td class="p-3">

<?= $row['tanggal'] ?? '-' ?>

</td>


<td class="p-3">

<?= $row['hari'] ?? '-' ?>

</td>


<td class="p-3">

<?= $row['jenis_hari'] ?? '-' ?>

</td>


<td class="p-3">

<?= $row['produk'] ?? '-' ?>

</td>


<td class="p-3">

<?= isset($row['kategori']) 
? implode(', ', $row['kategori']) 
: '-' ?>

</td>


</tr>


<?php endforeach ?>


</tbody>


</table>

</div>
</div>





<!-- ==========================
5 ENCODING
========================== -->


<div class="mb-5 rounded-xl border p-5">


<h3 class="font-semibold mb-3">
5. Encoding
</h3>


<table class="w-full text-sm">


<thead class="bg-gray-100">


<tr>

<th class="p-3">
Hari
</th>

<th class="p-3">
Kopi
</th>

<th class="p-3">
Non-Kopi
</th>

<th class="p-3">
Makanan
</th>

<th class="p-3">
Snack
</th>


</tr>


</thead>



<tbody>


<?php foreach(array_slice($tahap['encoding'] ?? [],0,5) as $row): ?>


<tr class="border-b">

<td class="p-3">

<?= $row['hari_encode'] ?? 0 ?>

</td>


<td class="p-3">

<?= $row['kopi'] ?? 0 ?>

</td>


<td class="p-3">

<?= $row['non_kopi'] ?? 0 ?>

</td>


<td class="p-3">

<?= $row['makanan'] ?? 0 ?>

</td>


<td class="p-3">

<?= $row['snack'] ?? 0 ?>

</td>


</tr>


<?php endforeach ?>


</tbody>


</table>


</div>





<!-- ==========================
6 NORMALISASI
========================== -->


<div class="mb-5 rounded-xl border p-5">


<h3 class="font-semibold mb-3">
6. Normalisasi Min-Max
</h3>

    <div class="overflow-x-auto">

        <table class="w-full text-sm">

            <thead class="bg-primary text-white">

                <tr>

                    <th class="px-4 py-3 text-center">
                        Hari
                    </th>

                    <th class="px-4 py-3 text-center">
                        Kopi
                    </th>

                    <th class="px-4 py-3 text-center">
                        Non-Kopi
                    </th>

                    <th class="px-4 py-3 text-center">
                        Makanan
                    </th>

                    <th class="px-4 py-3 text-center">
                        Snack
                    </th>

                    <th class="px-4 py-3 text-center">
                        Total Asli
                    </th>

                    <th class="px-4 py-3 text-center">
                        Total Normalisasi
                    </th>

                </tr>

            </thead>

            <tbody>

                <?php
                $dataNormalisasi = array_slice(
                    $tahap['normalisasi'] ?? [],
                    0,
                    5
                );
                ?>

                <?php foreach($dataNormalisasi as $row): ?>

                <tr class="border-b hover:bg-gray-50">

                    <td class="px-4 py-3 text-center">
                        <?= esc($row['hari']) ?>
                    </td>

                    <td class="px-4 py-3 text-center">
                        <?= number_format(
                            $row['menu_kopi'],
                            2,
                            '.',
                            ''
                        ) ?>
                    </td>

                    <td class="px-4 py-3 text-center">
                        <?= number_format(
                            $row['menu_non_kopi'],
                            2,
                            '.',
                            ''
                        ) ?>
                    </td>

                    <td class="px-4 py-3 text-center">
                        <?= number_format(
                            $row['menu_makanan'],
                            2,
                            '.',
                            ''
                        ) ?>
                    </td>

                    <td class="px-4 py-3 text-center">
                        <?= number_format(
                            $row['menu_snack'],
                            2,
                            '.',
                            ''
                        ) ?>
                    </td>

                    <td class="px-4 py-3 text-center">
                        Rp <?= number_format(
                            $row['total_asli'],
                            0,
                            ',',
                            '.'
                        ) ?>
                    </td>

                    <td class="px-4 py-3 text-center">
                        <?= number_format(
                            $row['total_normalisasi'],
                            4,
                            '.',
                            ''
                        ) ?>
                    </td>

                </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>


</div>



<!-- ==========================
DETAIL CLUSTERING
========================== -->
<div class="mb-5 mt-8 rounded-xl bg-white p-5 shadow">


<h3 class="font-semibold mb-5 text-gray-800">
Detail Clustering
</h3>

<!-- JUMLAH DATA CLUSTER -->

<table class="w-full text-sm mb-8">


<thead class="bg-primary text-white">

<tr>

<th class="p-3">
Cluster
</th>

<th class="p-3">
Jumlah Data
</th>

</tr>

</thead>


<tbody>


<?php

$jumlahCluster=[];


foreach($cluster as $row){

    $jumlahCluster[$row['cluster']] =
    ($jumlahCluster[$row['cluster']] ?? 0)+1;

}


?>


<?php foreach($jumlahCluster as $c=>$jumlah): ?>


<tr class="border-b">

<td class="p-3 text-center font-semibold">
<?= $c ?>
</td>


<td class="p-3 text-center">
<?= $jumlah ?> data
</td>


</tr>


<?php endforeach ?>


</tbody>


</table>




<!-- CENTROID AKHIR -->
<div class="mb-5 rounded-xl bg-white p-5 shadow">

<h3 class="font-semibold mb-3">
Centroid Akhir
</h3>


<table class="w-full text-sm">


<thead class="bg-primary text-white">


<tr>

<th class="p-3">
Cluster
</th>

<th class="p-3">
Hari
</th>

<th class="p-3">
Kopi
</th>

<th class="p-3">
Non-Kopi
</th>

<th class="p-3">
Makanan
</th>

<th class="p-3">
Snack
</th>

<th class="p-3">
Total
</th>


</tr>


</thead>


<tbody>


<?php foreach($centroid as $row): ?>


<tr class="border-b">


<td class="p-3 text-center font-semibold">
<?= $row['cluster'] ?>
</td>


<td class="p-3 text-center">
<?= number_format($row['hari_encode'],3,'.','') ?>
</td>


<td class="p-3 text-center">
<?= number_format($row['menu_kopi'],3,'.','') ?>
</td>


<td class="p-3 text-center">
<?= number_format($row['menu_non_kopi'],3,'.','') ?>
</td>


<td class="p-3 text-center">
<?= number_format($row['menu_makanan'],3,'.','') ?>
</td>


<td class="p-3 text-center">
<?= number_format($row['menu_snack'],3,'.','') ?>
</td>


<td class="p-3 text-center">
<?= number_format($row['total'],3,'.','') ?>
</td>


</tr>


<?php endforeach ?>


</tbody>


</table>

</div>
<table>

<!-- SILHOUETTE SCORE -->

<div class="mb-8 rounded-xl bg-green-50 border border-green-200 p-5">

    <h3 class="font-semibold text-gray-800 mb-3">
        Evaluasi Hasil Clustering
    </h3>


    <?php 
        $nilaiSilhouette = $evaluasi['nilai_silhouette'] ?? 0;
    ?>


    <div class="flex items-center justify-between">

        <div>

            <p class="text-sm text-gray-600">
                Silhouette Score
            </p>

            <p class="text-3xl font-bold text-green-700">
                <?= number_format(
                    $nilaiSilhouette,
                    4,
                    '.',
                    ''
                ) ?>
            </p>

        </div>


        <div class="text-right">

            <?php if($nilaiSilhouette >= 0.71): ?>

                <span class="px-4 py-2 rounded-full bg-green-600 text-white text-sm">
                    Struktur Sangat Baik
                </span>

            <?php elseif($nilaiSilhouette >= 0.51): ?>

                <span class="px-4 py-2 rounded-full bg-blue-600 text-white text-sm">
                    Struktur Baik
                </span>


            <?php elseif($nilaiSilhouette >= 0.26): ?>

                <span class="px-4 py-2 rounded-full bg-yellow-500 text-white text-sm">
                    Struktur Lemah
                </span>


            <?php else: ?>

                <span class="px-4 py-2 rounded-full bg-red-600 text-white text-sm">
                    Struktur Buruk
                </span>

            <?php endif; ?>


        </div>


    </div>


</div>




</div>




<?php endif ?>


</div>


</section>


</main>


</div>


<?= $this->include('layouts/footer') ?>