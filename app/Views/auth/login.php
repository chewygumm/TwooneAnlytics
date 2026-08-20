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

<body class="bg-slate-100">

<div class="flex min-h-screen items-center justify-center">

    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-xl">

        <div class="mb-8 text-center">

            <h1 class="text-3xl font-bold text-[#1B4332]">
                Two One Kopi
            </h1>

            <p class="mt-2 text-gray-500">

                Sistem Analisis Pola Transaksi

            </p>

        </div>

        <form action="<?= base_url('login') ?>" method="post">

            <div class="mb-5">

                <label class="mb-2 block text-sm font-medium">

                    Username

                </label>

                <input
                    type="text"
                    name="username"
                    required
                    class="w-full rounded-lg border p-3 focus:border-[#1B4332] focus:outline-none">

            </div>

            <div class="mb-6">

                <label class="mb-2 block text-sm font-medium">

                    Password

                </label>

                <input
                    type="password"
                    name="password"
                    required
                    class="w-full rounded-lg border p-3 focus:border-[#1B4332] focus:outline-none">

            </div>

            <button
                class="w-full rounded-lg bg-[#1B4332] py-3 font-semibold text-white hover:bg-[#2D6A4F]">

                LOGIN

            </button>

        </form>

    </div>

</div>

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