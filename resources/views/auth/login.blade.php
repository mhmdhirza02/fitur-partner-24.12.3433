<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AmikomEventHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans">

    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="flex items-center justify-center gap-2.5">
            <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-base shadow-sm">
                AH
            </div>
            <span class="font-bold text-xl text-slate-900 tracking-tight">AmikomEventHub</span>
        </a>
        
        <!-- Header -->
        <h2 class="mt-6 text-center text-2xl font-bold tracking-tight text-slate-900">
            Masuk ke akun Anda
        </h2>
        <p class="mt-2 text-center text-sm text-slate-500">
            Kelola pesanan tiket dan ulasan event dengan mudah
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-6 shadow-sm border border-slate-200/80 rounded-2xl sm:px-10">
            
            @if(session('error'))
                <div class="mb-5 p-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-xs font-medium text-center">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-5 p-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-xs font-medium text-center">
                    {{ $errors->first() }}
                </div>
            @endif

            <form class="space-y-5" action="{{ route('login.post') }}" method="POST">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">
                        Alamat Email
                    </label>
                    <div class="mt-1.5">
                        <input id="email" name="email" type="email" autocomplete="email" required placeholder="nama@email.com" value="{{ old('email') }}"
                            class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 text-sm focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 transition">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">
                        Password
                    </label>
                    <div class="mt-1.5">
                        <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="••••••••"
                            class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder-slate-400 text-sm focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 transition">
                    </div>
                </div>

                <div class="pt-1">
                    <button type="submit" class="flex w-full justify-center rounded-xl bg-indigo-600 py-2.5 px-4 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition">
                        Masuk Sekarang
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs uppercase">
                        <span class="bg-white px-3 text-slate-400 font-medium">Atau</span>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('auth.google') }}" class="flex w-full items-center justify-center gap-2.5 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition">
                        <svg class="h-4 w-4" viewBox="0 0 24 24">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        <span>Lanjutkan dengan Google</span>
                    </a>
                </div>
            </div>

            <!-- Tautan Navigasi Bawah -->
            <div class="mt-8 pt-6 border-t border-slate-100 text-center space-y-2">
                <p class="text-xs text-slate-500">
                    Belum punya akun? 
                    <a href="{{ route('user.register') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 hover:underline transition">
                        Daftar sebagai Pembeli
                    </a>
                </p>
                <p class="text-xs text-slate-500">
                    Seorang penyelenggara event? 
                    <a href="{{ route('admin.login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 hover:underline transition">
                        Masuk sebagai Partner &rarr;
                    </a>
                </p>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="text-xs font-medium text-slate-400 hover:text-slate-600 transition">
                &larr; Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>
