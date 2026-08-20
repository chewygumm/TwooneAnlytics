<?= $this->include('layouts/header') ?>


<div class="flex min-h-screen bg-gray-50">


    <?= $this->include('layouts/sidebar') ?>


    <div class="ml-64 flex-1 px-6 py-6">


        <?= $this->include('layouts/navbar') ?>


        <main class="px-3 py-5">


            <section class="mt-6">


                <!-- BUTTON -->
<div class="mb-6 flex items-center justify-between">

    <div class="flex gap-3">

        <a href="<?= base_url('analisis-data/proses') ?>"
           class="inline-flex items-center justify-center rounded-full border border-green-800 bg-green-800 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-green-900">

            Analisis Data

        </a>


        <a href="<?= base_url('analisis-data/reset') ?>"
           onclick="return confirm('Hapus hasil clustering?')"
           class="inline-flex items-center justify-center rounded-full border border-red-600 bg-red-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">

            Reset

        </a>

    </div>


    <div class="flex gap-4">

        <a href="<?= base_url('analisis-data/detail') ?>"
           class="text-green-700 underline">

            Lihat Detail

        </a>


    </div>

</div>




            <!-- RINGKASAN DETAIL CLUSTER -->

<div class="mb-6 rounded-xl bg-white p-6 shadow">


    <h3 class="mb-5 text-lg font-semibold text-gray-800">

        Analisis Karakteristik Cluster

    </h3>


    <div class="overflow-x-auto">


        <table class="w-full text-sm">


            <thead class="bg-primary text-white">

                <tr>

                    <th class="px-4 py-3">
                        Cluster
                    </th>

                    <th class="px-4 py-3">
                        Jumlah Transaksi
                    </th>

                    <th class="px-4 py-3">
                        Produk Dominan
                    </th>

                    <th class="px-4 py-3">
                        Hari Dominan
                    </th>

                    <th class="px-4 py-3">
                        Total Transaksi
                    </th>

                </tr>

            </thead>


            <tbody>


            <?php if(!empty($karakteristik)): ?>


                <?php foreach($karakteristik as $row): ?>


                <tr class="border-b hover:bg-gray-50">


                    <td class="px-4 py-3 text-center font-semibold">

                        <?= esc($row['cluster']) ?>

                    </td>



                    <td class="px-4 py-3 text-center">

                        <?= esc($row['jumlah']) ?> data

                    </td>



                    <td class="px-4 py-3 text-center">

                        <?= esc($row['produk_dominan']) ?>

                    </td>



                    <td class="px-4 py-3 text-center">
                        <?= esc($row['hari_dominan'] ?? '-') ?>
                    </td>



                    <td class="px-4 py-3 text-center">

                        Rp <?= number_format($row['rata_rata'],0,',','.') ?>

                    </td>


                </tr>


                <?php endforeach; ?>


            <?php else: ?>


                <tr>

                    <td colspan="5" class="py-8 text-center text-gray-500">

                        Belum ada ringkasan cluster

                    </td>

                </tr>


            <?php endif; ?>


            </tbody>


        </table>


    </div>


</div>
<!-- ==========================
HASIL INTERPRETASI LLM
========================== -->


<div class="rounded-xl bg-white shadow p-6 mb-6">


<div class="flex justify-between items-center mb-6">


<h2 class="text-xl font-semibold text-gray-800">
    Rekomendasi Promosi
</h2>



<a href="<?= base_url('analisis-data/generate') ?>"
class="bg-primary text-white px-4 py-2 rounded-lg">

Buat Promosi

</a>


</div>




<?php if(!empty($hasil_llm)): ?>


<div class="grid grid-cols-1 md:grid-cols-2 gap-6">


<?php foreach($hasil_llm as $item): ?>

<div class="group rounded-2xl border border-gray-200 bg-white shadow-sm 
            hover:shadow-lg transition duration-300 overflow-hidden">


    <!-- Header Card -->
    <div class="bg-green-50 px-5 py-4 border-b">

        <div class="flex justify-between items-start">

            <div>

                <h3 class="text-lg font-bold text-green-800">
                    <?= esc($item['nama_segmen']) ?>
                </h3>


                <p class="text-xs text-gray-500 mt-1">
                    Cluster <?= esc($item['cluster']) ?>
                </p>

            </div>


            <span class="rounded-full bg-green-700 px-3 py-1 
                         text-xs font-semibold text-white">

                Promo

            </span>

        </div>

    </div>



    <!-- Isi Promo -->
    <div class="p-5">


        <div class="rounded-xl bg-green-100 p-4 mb-5">


            <p class="text-xs font-semibold text-green-700 uppercase">
                Nama Promo
            </p>


            <h4 class="text-xl font-bold text-gray-800 mt-1">
               <?= esc(
                    $item['produk_dominan'] ?? '-'
                ) ?>
            </h4>


            <p class="mt-2 text-green-700 font-semibold">

                <?= esc(
                    $item['strategi_promosi']['diskon'] ?? '-'
                ) ?>

            </p>


        </div>




        <!-- Detail Promo -->

        <div class="space-y-3 text-sm">


            <div class="flex items-center justify-between">

                <span class="text-gray-500">
                    Produk
                </span>

                <span class="font-semibold text-gray-800">

                <?= esc(
                $item['produk_dominan'] ?? '-'
                ) ?>
                </span>

            </div>



            <div class="flex items-center justify-between">


                <span class="text-gray-500">
                    Waktu
                </span>


                <span class="font-semibold text-gray-800">

                <?= esc(
                    $item['strategi_promosi']['waktu'] ?? '-'
                ) ?>

                </span>


            </div>




            <div class="flex items-center justify-between">


                <span class="text-gray-500">
                    Target
                </span>


                <span class="font-semibold text-gray-800">

                <?= esc(
                    $item['strategi_promosi']['target'] ?? '-'
                ) ?>

                </span>


            </div>



        </div>



        <!-- Button Detail -->

        <button
        onclick="toggleDetail('detail<?= $item['cluster'] ?>')"
        class="mt-5 flex w-full items-center justify-center gap-2
               rounded-lg border border-green-700 
               py-2 text-sm font-semibold text-green-700
               hover:bg-green-700 hover:text-white transition">


            Lihat Detail Cluster ▼


        </button>




        <!-- Dropdown Detail -->

        <div id="detail<?= $item['cluster'] ?>"
             class="hidden mt-4 rounded-xl bg-gray-50 p-4 text-sm">


            <h4 class="font-bold text-gray-800 mb-3">
                Detail Hasil Clustering
            </h4>



            <div class="space-y-2">


                <p>
                    <b>Produk Dominan:</b>
                    <?= esc($item['produk_dominan']) ?>
                </p>


                <p>
                    <b>Jumlah Transaksi:</b>
                    <?= esc($item['jumlah_transaksi']) ?>
                </p>


                <p>
                    <b>Pola Hari:</b>
                    <?= esc($item['pola_hari']) ?>
                </p>


                <p>
                    <b>Rata-rata Transaksi:</b>
                    <?= esc($item['rata_rata_transaksi']) ?>
                </p>


                <p>
                    <b>Ringkasan:</b><br>
                    <?= esc($item['ringkasan']) ?>
                </p>


            </div>


        </div>


    </div>


</div>


<?php endforeach; ?>


</div>



<!-- ==========================
INFORMASI PENDUKUNG KUESIONER
========================== -->


<div class="mt-6 rounded-xl border bg-white shadow p-5">


<h3 class="font-bold text-lg mb-4">
Informasi Pendukung Kuesioner
</h3>

<?php 
$info = [];

foreach($hasil_llm as $item){
    if(isset($item['informasi_pendukung'])){
        $info = $item['informasi_pendukung'];
        break;
    }
}
?>

<div class="text-sm space-y-2">

<?php if(isset($info['pekerja'])): ?>

<p class="font-semibold">
Pekerja
</p>

<p>
• Kunjungan :
<?= $info['pekerja']['kunjungan'] ?? '-' ?>
</p>

<p>
• Produk :
<?= $info['pekerja']['produk'] ?? '-' ?>
</p>
<br>
<?php endif; ?>

<?php if(isset($info['mahasiswa'])): ?>

<p class="font-semibold">
Mahasiswa
</p>

<p>
• Kunjungan :
<?= $info['mahasiswa']['kunjungan'] ?? '-' ?>
</p>

<p>
• Produk :
<?= $info['mahasiswa']['produk'] ?? '-' ?>
</p>

<br>

<?php endif; ?>



<?php if(isset($info['pelajar'])): ?>

<p class="font-semibold">
Pelajar
</p>

<p>
• Kunjungan :
<?= $info['pelajar']['kunjungan'] ?? '-' ?>
</p>

<p>
• Produk :
<?= $info['pelajar']['produk'] ?? '-' ?>
</p>

<br>

<?php endif; ?>




</div>


</div>



<?php else: ?>


<div class="text-center text-gray-500 py-5">

Belum ada hasil interpretasi.

</div>


<?php endif; ?>


</div>



</section>

<script>

function toggleDetail(id)
{

    let detail = document.getElementById(id);


    if(detail.classList.contains('hidden'))
    {
        detail.classList.remove('hidden');
    }
    else
    {
        detail.classList.add('hidden');
    }

}

</script>


      <!-- Loading Overlay -->
<div id="loading" 
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">

    <div class="inline-flex items-center gap-3 rounded-lg bg-white px-6 py-4 shadow">

        <svg
            class="size-6 animate-spin text-green-700"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            aria-hidden="true">

            <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4">
            </circle>


            <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>

        </svg>


        <p id="loadingText" class="font-medium text-gray-700">
            Loading...
        </p>

    </div>

</div>

        <script>

        function showLoading(text)
        {

            document.getElementById('loadingText').innerHTML = text;


            let loading = document.getElementById('loading');
            let bar = document.getElementById('progressBar');


            loading.classList.remove('hidden');
            loading.classList.add('flex');


            let progress = 0;

            progressInterval = setInterval(function(){

                if(progress < 90)
                {
                    progress += Math.random() * 10;

                    bar.style.width = progress + "%";
                }


            },300);

        }



        // Analisis Data
        document.querySelector('a[href*="analisis-data/proses"]')
        ?.addEventListener('click',function(){

            showLoading(
                'Sedang melakukan proses analisis data...'
            );

        });



        // Generate LLM
        document.querySelector('a[href*="analisis-data/generate"]')
        ?.addEventListener('click',function(){

            showLoading(
                'Sedang membuat interpretasi LLM...'
            );

        });


</script>

        <?php if(session()->getFlashdata('success')): ?>


        <script>

            Swal.fire({

                icon:'success',

                title:'Berhasil',

                text:'<?= session()->getFlashdata('success') ?>',

                confirmButtonColor:'#1B4332'

            });


        </script>


        <?php endif; ?>





        <?= $this->include('layouts/footer') ?>



    </main>



</div>