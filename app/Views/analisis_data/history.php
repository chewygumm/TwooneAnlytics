<?= $this->include('layouts/header') ?>

<div class="flex min-h-screen bg-gray-50">

<?= $this->include('layouts/sidebar') ?>


<div class="ml-64 flex-1">

<?= $this->include('layouts/navbar') ?>


<main class="p-6">
<div class="mb-6">

<a href="<?= base_url('analisis-data') ?>"
   class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-3 text-sm font-medium text-white hover:bg-secondary">

    Kembali

</a>

</div>

<div class="rounded-xl bg-white p-6 shadow">


<h2 class="mb-5 text-xl font-semibold">
History Analisis
</h2>


<table class="w-full text-sm">


<thead class="bg-primary text-white">

<tr>

<th class="p-3">
Periode
</th>

<th>
Tanggal
</th>

<th>
Jumlah Data
</th>

<th>
Cluster
</th>

<th>
Silhouette
</th>

<th>
Rekomendasi Promosi
</th>

</tr>

</thead>


<tbody>


<?php foreach($history as $row): ?>


<tr class="border-b">


<td class="p-3 text-center">
<?= esc($row['periode_data']) ?>
</td>


<td class="text-center">
<?= esc($row['tanggal_analisis']) ?>
</td>


<td class="text-center">
<?= esc($row['jumlah_data']) ?>
</td>


<td class="text-center">
<?= esc($row['jumlah_cluster']) ?>
</td>


<td class="text-center">
<?= esc($row['silhouette_score']) ?>
</td>


<td class="text-center">
<?= esc($row['rekomendasi_promosi']) ?>
</td>


</tr>


<?php endforeach ?>


</tbody>


</table>


</div>


</main>


</div>

</div>


<?= $this->include('layouts/footer') ?>