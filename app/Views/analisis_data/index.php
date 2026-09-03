<?= $this->include('layouts/header') ?>


<div class="flex min-h-screen bg-gray-50">


    <?= $this->include('layouts/sidebar') ?>


    <div class="ml-64 flex-1 px-6 py-6">


        <?= $this->include('layouts/navbar') ?>


        <main class="px-3 py-5">


            <section class="mt-6">


 <div class="mb-6 flex items-end justify-between">

    <div class="flex items-end gap-3">

        <form action="<?= base_url('analisis-data/proses') ?>" method="post" class="flex items-end gap-3">

            <?= csrf_field() ?>

            <div>
                <label for="jumlah_cluster" class="mb-2 block text-sm font-medium text-gray-700">
                    Jumlah Promosi
                </label>

                <input
                    type="number"
                    id="jumlah_cluster"
                    name="jumlah_cluster"
                    min="2"
                    value="4"
                    required
                    class="w-20 rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-green-700 focus:outline-none focus:ring-1 focus:ring-green-700"
                >
            </div>

            <button type="submit"
                    class="inline-flex items-center justify-center rounded-full border border-green-800 bg-green-800 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-green-900">
              Buat Promosi
            </button>

        </form>

        <a href="<?= base_url('analisis-data/reset') ?>"
           onclick="return confirm('Hapus hasil clustering?')"
           class="inline-flex items-center justify-center rounded-full border border-red-600 bg-red-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">
            Reset
        </a>

    </div>

    <div>
        <a href="<?= base_url('analisis-data/detail') ?>"
           class="text-green-700 underline">
            Lihat Detail
        </a>
    

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
                    $item['strategi_promosi']['nama_promo'] ?? '-'
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



    </div>


</div>


<?php endforeach; ?>


</div>



<?php else: ?>


<div class="text-center text-gray-500 py-5">

Belum ada hasil interpretasi.

</div>


<?php endif; ?>


</div>



</section>




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

            const loading = document.getElementById('loading');
            loading.classList.remove('hidden');
            loading.classList.add('flex');
        }



        // Analisis Data
        document.querySelector('a[href*="analisis-data/proses"]')
        ?.addEventListener('click',function(){

            showLoading(
                'Sedang melakukan analisis data dan membuat rekomendasi promosi...'
            );

        });
`



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