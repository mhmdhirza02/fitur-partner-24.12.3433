<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Ditangguhkan - AmikomEventHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-6">
    <div class="w-full max-w-lg text-center">
        <div class="bg-white p-10 rounded-[2.5rem] shadow-xl border border-slate-100">
            <div class="w-20 h-20 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            
            <h1 class="text-2xl font-black text-slate-900 mb-2">Akun Menunggu Persetujuan</h1>
            <p class="text-slate-500 mb-8 leading-relaxed">
                Halo, <strong>{{ auth()->user()->name }}</strong>!<br>
                Pendaftaran HIMA/Kepanitiaan Anda telah kami terima. Saat ini akun Anda sedang ditinjau oleh Superadmin untuk memastikan kelayakannya. Harap tunggu beberapa saat.
            </p>

            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition">
                    Keluar / Kembali
                </button>
            </form>
        </div>
    </div>
</body>
</html>
