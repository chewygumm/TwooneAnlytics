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
                        class="rounded-lg bg-primary px-5 py-3 text-sm font-medium text-white hover:bg-secondary">

                        Upload Excel

                    </button>

                    <a href="<?= base_url('transaksi/reset') ?>"
                        onclick="return confirm('Apakah Anda yakin ingin menghapus seluruh data transaksi?')"
                        class="rounded-lg bg-red-600 px-5 py-3 text-sm font-medium text-white hover:bg-red-700">

                        Reset Data

                    </a>

                </div>

            </div>

            <!-- Upload -->
            <div id="upload"
                class="mb-6 hidden rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

                <form
                    action="<?= base_url('transaksi/upload') ?>"
                    method="post"
                    enctype="multipart/form-data">

                    <input
                        type="file"
                        name="file_excel"
                        accept=".xlsx,.xls"
                        required
                        class="mb-4 block w-full rounded-lg border border-gray-300 p-3">

                    <button
                        class="rounded-lg bg-primary px-5 py-2 text-white hover:bg-secondary">

                        Upload

                    </button>

                </form>

            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">

                <table class="min-w-max w-full text-sm">

                    <thead class="bg-primary text-white whitespace-nowrap">

                        <tr>

                            <th class="px-4 py-3 text-left">No</th>
                            <th class="px-4 py-3 text-left">No. Struk</th>
                            <th class="px-4 py-3 text-left">Tanggal</th>
                            <th class="px-4 py-3 text-left">Jam</th>
                            <th class="px-4 py-3 text-left">Nama Kasir</th>
                            <th class="px-4 py-3 text-left">Produk</th>
                            <th class="px-4 py-3 text-center">Jumlah Produk</th>
                            <th class="px-4 py-3 text-center">Jumlah Dibatalkan</th>
                            <th class="px-4 py-3 text-right">Harga / Produk</th>
                            <th class="px-4 py-3 text-right">Subtotal</th>
                            <th class="px-4 py-3 text-left">Tipe Harga</th>
                            <th class="px-4 py-3 text-right">Diskon Produk</th>
                            <th class="px-4 py-3 text-left">Tipe Diskon</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-left">Metode Pembayaran</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if (!empty($transaksi)) : ?>

                            <?php foreach ($transaksi as $key => $row) : ?>

                                <tr class="border-b hover:bg-gray-50">

                                    <td class="px-4 py-3"><?= $key + 1 ?></td>

                                    <td class="px-4 py-3"><?= esc($row['no_struk']) ?></td>

                                    <td class="px-4 py-3">

                                        <?php
                                        if (!empty($row['tanggal']) && $row['tanggal'] != '0000-00-00') {
                                            echo date('d-m-Y', strtotime($row['tanggal']));
                                        } else {
                                            echo '-';
                                        }
                                        ?>

                                    </td>

                                    <td class="px-4 py-3"><?= esc($row['jam']) ?></td>

                                    <td class="px-4 py-3"><?= esc($row['nama_kasir']) ?></td>

                                    <td class="px-4 py-3"><?= esc($row['produk']) ?></td>

                                    <td class="px-4 py-3 text-center"><?= esc($row['jumlah_produk']) ?></td>

                                    <td class="px-4 py-3 text-center"><?= esc($row['jumlah_dibatalkan']) ?></td>

                                    <td class="px-4 py-3 text-right">
                                        Rp <?= number_format($row['harga_per_produk'], 0, ',', '.') ?>
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        Rp <?= number_format($row['subtotal'], 0, ',', '.') ?>
                                    </td>

                                    <td class="px-4 py-3"><?= esc($row['tipe_harga']) ?></td>

                                    <td class="px-4 py-3 text-right">
                                        Rp <?= number_format($row['diskon_produk'], 0, ',', '.') ?>
                                    </td>

                                    <td class="px-4 py-3"><?= esc($row['tipe_diskon_produk']) ?></td>

                                    <td class="px-4 py-3 text-right font-semibold text-primary">
                                        Rp <?= number_format($row['total'], 0, ',', '.') ?>
                                    </td>

                                    <td class="px-4 py-3 text-center">

                                        <?php if ($row['status'] == 'Berhasil') : ?>

                                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                                                Berhasil
                                            </span>

                                        <?php else : ?>

                                            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700">
                                                <?= esc($row['status']) ?>
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td class="px-4 py-3"><?= esc($row['metode_pembayaran']) ?></td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else : ?>

                            <tr>

                                <td colspan="16" class="py-10 text-center text-gray-500">

                                    Belum ada data transaksi.

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>

        <?php if (session()->getFlashdata('success')) : ?>

            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '<?= session()->getFlashdata('success') ?>',
                    confirmButtonColor: '#1B4332'
                });
            </script>

        <?php endif; ?>

        <?= $this->include('layouts/footer') ?>

    </main>

</div>