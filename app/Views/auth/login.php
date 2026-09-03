<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | Two One Kopi</title>


    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">


    <style>

        body{
            font-family:'Poppins',sans-serif;
        }

    </style>


</head>



<body class="bg-gray-100">


<div class="flex min-h-screen">



    <!-- =========================
         BAGIAN FOTO KIRI
    ========================== -->

    <div class="relative hidden lg:block lg:w-1/2">

      <img 
        src="<?= base_url('kopi.png') ?>"
        class="absolute inset-0 h-full w-full object-cover"
        alt="Two One Kopi">


        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/50"></div>



        <div class="absolute inset-0 flex items-center justify-center">


            <div class="px-10 text-center text-white">


                <h1 class="text-5xl font-bold">
                    Two One Analytics
                </h1>


                <p class="mt-4 text-lg text-gray-200">
                    Sistem Promosi Two One Kopi
                </p>


            </div>


        </div>


    </div>






    <!-- =========================
         BAGIAN FORM LOGIN KANAN
    ========================== -->


    <div class="flex w-full items-center justify-center px-8 lg:w-1/2">



        <div class="w-full max-w-md rounded-2xl bg-white p-10 shadow-xl">



            <div class="mb-8 text-center">


                <h1 class="text-3xl font-bold text-[#1B4332]">

                    Login

                </h1>


                <p class="mt-2 text-gray-500">

                    Masuk ke sistem analisis promosi

                </p>


            </div>





            <form action="<?= base_url('login') ?>" method="post">



                <!-- Username -->

                <div class="mb-5">


                    <label class="mb-2 block text-sm font-medium text-gray-700">

                        Username

                    </label>



                    <input
                        type="text"
                        name="username"
                        required
                        class="w-full rounded-lg border border-gray-300 p-3 
                               focus:border-[#1B4332] 
                               focus:outline-none
                               focus:ring-1 
                               focus:ring-[#1B4332]">


                </div>






                <!-- Password -->

                <div class="mb-6">


                    <label class="mb-2 block text-sm font-medium text-gray-700">

                        Password

                    </label>



                    <input
                        type="password"
                        name="password"
                        required
                        class="w-full rounded-lg border border-gray-300 p-3 
                               focus:border-[#1B4332] 
                               focus:outline-none
                               focus:ring-1 
                               focus:ring-[#1B4332]">


                </div>






                <!-- Button -->


                <button
                    type="submit"
                    class="w-full rounded-lg bg-[#1B4332] py-3 
                           font-semibold text-white 
                           transition 
                           hover:bg-[#2D6A4F]">


                    LOGIN


                </button>




            </form>



        </div>



    </div>



</div>







<!-- =========================
     ALERT LOGIN ERROR
========================== -->


<?php if(session()->getFlashdata('error')): ?>


<script>


Swal.fire({

    icon:'error',

    title:'Login Gagal',

    text:'<?= session()->getFlashdata('error') ?>'

});


</script>


<?php endif; ?>





</body>


</html>