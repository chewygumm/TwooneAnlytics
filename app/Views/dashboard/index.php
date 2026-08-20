<?= $this->include('layouts/header') ?>


<div class="flex min-h-screen">


    <?= $this->include('layouts/sidebar') ?>


    <main class="ml-64 flex-1 px-6 py-6">


        <?= $this->include('layouts/navbar') ?>


        <section class="mt-6">




            <!-- Statistik -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">

                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Total Transaksi</p>
                    <h2 class="mt-2 text-3xl font-bold text-primary">
                        <?= number_format($totalTransaksi) ?>
                    </h2>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Data Kuesioner</p>
                    <h2 class="mt-2 text-3xl font-bold text-primary">
                        <?= number_format($totalKuesioner) ?>
                    </h2>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Jumlah Cluster</p>
                <h2 class="mt-2 text-3xl font-bold text-primary">
                    <?= $jumlahCluster ?>
                </h2>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Silhouette Score</p>
                    <h2 class="mt-2 text-3xl font-bold text-primary">
                        <?= number_format($silhouette,3) ?>
                    </h2>
                </div>

            </div>


        </section>

        <?= $this->include('layouts/footer') ?>

    </main>

</div>
