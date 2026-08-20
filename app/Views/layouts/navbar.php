<header class="flex items-center justify-between border-b border-gray-200 bg-white px-8 py-5 shadow-sm">

    <div>
        <h1 class="text-2xl font-semibold text-gray-800">
            <?= $title ?? 'Dashboard' ?>
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Sistem Analisis Pola Transaksi Cafe Two One Kopi
        </p>
    </div>

    <div class="flex items-center gap-4">

        <div class="text-right">

            <p class="text-xs text-gray-500">
                <?= date('d F Y') ?>
            </p>

            <p class="font-semibold text-gray-800">
                <?= session('nama') ?? 'Administrator' ?>
            </p>


        </div>

        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-primary text-lg font-semibold text-white">
            <?= strtoupper(substr(session('nama') ?? 'A', 0, 1)) ?>
        </div>

    </div>

</header>