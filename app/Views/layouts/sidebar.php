<aside class="fixed left-0 top-0 h-screen w-64 bg-white text-[#1f4735] border-r border-gray-200 overflow-y-auto">


    <!-- Logo -->
    <div class="px-6 py-6 border-b border-gray-200">

        <h1 class="text-xl font-bold text-[#1f4735] whitespace-nowrap">
            Two One Analytics
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Promotion Analytics
        </p>

    </div>




    <!-- Menu -->
    <nav class="mt-6 px-4">


        <ul class="space-y-2">


            <!-- Master Data -->
            <li class="pt-5">

                <p class="px-4 text-xs font-semibold uppercase tracking-wider text-gray-400">

                    Master Data

                </p>

            </li>




            <li>

                <details class="group">


                    <summary
                    class="flex cursor-pointer items-center justify-between
                    rounded-lg px-4 py-3 text-sm font-medium
                    text-[#1f4735]
                    hover:bg-[#e8f0eb] transition">


                        Master Data


                        <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 transition group-open:rotate-180"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">


                            <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 9l-7 7-7-7"/>


                        </svg>


                    </summary>





                    <ul class="mt-2 ml-3 space-y-1 border-l border-gray-200 pl-3">


                        <li>

                            <a href="<?= base_url('transaksi') ?>"
                            class="block rounded-lg px-3 py-2 text-sm 
                            text-[#365f4b]
                            hover:bg-gray-100 transition">

                                Riwayat Transaksi

                            </a>

                        </li>



                        <li>

                            <a href="<?= base_url('kuesioner') ?>"
                            class="block rounded-lg px-3 py-2 text-sm 
                            text-[#365f4b]
                            hover:bg-gray-100 transition">

                                Data Kuesioner

                            </a>

                        </li>



                        <li>

                            <a href="<?= base_url('kategori-produk') ?>"
                            class="block rounded-lg px-3 py-2 text-sm 
                            text-[#365f4b]
                            hover:bg-gray-100 transition">

                                Kategori Produk

                            </a>

                        </li>


                    </ul>


                </details>

            </li>






            <!-- Pengolahan Data -->
            <li class="pt-5">

                <p class="px-4 text-xs font-semibold uppercase tracking-wider text-gray-400">

                    Promosi

                </p>

            </li>





            <li>

                <a href="<?= base_url('analisis-data') ?>"
                class="block rounded-lg px-4 py-3 text-sm font-medium
                text-[#1f4735]
                hover:bg-[#e8f0eb] transition">

                    Promosi

                </a>

            </li>


        </ul>


    </nav>







    <!-- Footer -->
    <div class="absolute bottom-0 w-full px-6 py-5 border-t border-gray-200">


        <a href="<?= base_url('logout') ?>"
        onclick="return confirm('Logout?')"
        class="inline-flex rounded-lg bg-red-500 px-5 py-2 
        text-sm font-medium text-white
        hover:bg-red-600 transition">

            Logout

        </a>


    </div>



</aside>