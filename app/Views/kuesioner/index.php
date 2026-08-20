<?= $this->include('layouts/header') ?>


<div class="flex min-h-screen">


    <?= $this->include('layouts/sidebar') ?>


    <main class="ml-64 flex-1 px-6 py-6">


        <?= $this->include('layouts/navbar') ?>


        <section class="mt-6">


            <div class="mb-6 flex items-center justify-between">


                <div class="flex gap-3">


<button
onclick="document.getElementById('upload').classList.remove('hidden')"
class="rounded-lg bg-primary px-5 py-3 text-white">

Upload Excel

</button>



<a href="<?=base_url('kuesioner/reset')?>"
onclick="return confirm('Hapus semua data?')"
class="rounded-lg bg-red-600 px-5 py-3 text-white">

Reset Data

</a>


</div>


</div>



<div id="upload"
class="hidden mb-6 rounded-xl bg-white p-6 shadow">


<form action="<?=base_url('kuesioner/upload')?>"
method="post"
enctype="multipart/form-data">


<input type="file"
name="file_excel"
required
class="border p-3 w-full mb-4">


<button
class="bg-primary text-white px-5 py-2 rounded">

Upload

</button>


</form>


</div>



<div class="overflow-x-auto rounded-xl bg-white shadow">


<table class="w-full text-sm">


<thead class="bg-primary text-white">


<tr>

<th class="px-4 py-3">No</th>

<th>Nama</th>

<th>Usia</th>

<th>Status</th>

<th>Waktu</th>

<th>Hari</th>

<th>Menu Minuman</th>

<th>Menu Makanan</th>

<th>No HP</th>


</tr>


</thead>



<tbody>


<?php foreach($kuesioner as $key=>$row): ?>
<?php if(empty($row['nama'])) continue; ?>

<tr class="border-b">


<td class="px-4 py-3">
<?=$key+1?>
</td>


<td><?=$row['nama']?></td>

<td><?=$row['usia']?></td>

<td><?=$row['status']?></td>

<td><?=$row['waktu_kunjungan']?></td>

<td><?=$row['hari_kunjungan']?></td>

<td><?=$row['menu_minuman']?></td>

<td><?=$row['menu_makanan']?></td>

<td><?=$row['no_hp']?></td>


</tr>


<?php endforeach; ?>


</tbody>


</table>


</div>



</main>


</div>


</div>


<?= $this->include('layouts/footer') ?>