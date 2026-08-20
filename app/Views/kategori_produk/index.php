<?= $this->include('layouts/header') ?>


<div class="flex min-h-screen">


    <?= $this->include('layouts/sidebar') ?>


    <main class="ml-64 flex-1 px-6 py-6">


        <?= $this->include('layouts/navbar') ?>


        <section class="mt-6">

            <div class="rounded-xl bg-white p-6 shadow">

                <form action="<?= base_url('kategori-produk/simpan') ?>"
                      method="post"
                      class="mb-6 flex gap-3">

                    <input
                        type="text"
                        name="produk"
                        placeholder="Nama Produk"
                        required
                        class="w-1/2 rounded-lg border border-gray-300 p-3">

                    <select
                        name="kategori"
                        class="rounded-lg border border-gray-300 p-3">

                        <option value="Kopi">Kopi</option>
                        <option value="Non-Kopi">Non-Kopi</option>
                        <option value="Makanan">Makanan</option>
                        <option value="Snack">Snack</option>
                        <option value="Lainnya">Lainnya</option>

                    </select>

                    <button
                        class="rounded-lg bg-primary px-5 text-white hover:bg-secondary">

                        Tambah

                    </button>

                </form>

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="bg-primary text-white">

                            <tr>

                                <th class="p-3">No</th>
                                <th class="p-3 text-left">Produk</th>
                                <th class="p-3">Kategori</th>
                                <th class="p-3">Aksi</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php if (!empty($produk)) : ?>

                                <?php foreach ($produk as $i => $row) : ?>

                                    <tr class="border-b hover:bg-gray-50">

                                        <td class="p-3 text-center">
                                            <?= $i + 1 ?>
                                        </td>

                                        <td class="p-3">
                                            <?= esc($row['produk']) ?>
                                        </td>

                                        <td class="p-3 text-center">

                                            <span class="rounded-full bg-green-100 px-3 py-1 text-green-700">

                                                <?= esc($row['kategori']) ?>

                                            </span>

                                        </td>

                                        <td class="p-3 text-center">

                                            <a href="<?= base_url('kategori-produk/hapus/' . $row['id_produk']) ?>"
                                               onclick="return confirm('Hapus data?')"
                                               class="rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700">

                                                Hapus

                                            </a>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else : ?>

                                <tr>

                                    <td colspan="4" class="p-6 text-center text-gray-500">

                                        Belum ada data kategori produk.

                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </section>

    </main>

</div>

<?= $this->include('layouts/footer') ?>