<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Partner & Penyelenggara - AmikomEventHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans">

    <div class="sm:mx-auto sm:w-full sm:max-w-lg">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="flex items-center justify-center gap-2.5">
            <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-base shadow-sm">
                AH
            </div>
            <span class="font-bold text-xl text-slate-900 tracking-tight">AmikomEventHub</span>
        </a>
        
        <!-- Header -->
        <h2 class="mt-6 text-center text-2xl font-bold tracking-tight text-slate-900">
            Daftar sebagai Penyelenggara
        </h2>
        <p class="mt-2 text-center text-sm text-slate-500">
            Buat akun partner untuk mengelola event HIMA atau kepanitiaan Anda
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-lg px-4 sm:px-0">
        <div class="bg-white py-8 px-6 shadow-sm border border-slate-200/80 rounded-2xl sm:px-10">
            
            @if(session('error'))
                <div class="mb-5 p-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-xs font-medium text-center">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                
                <div>
                    <label for="partner_name" class="block text-sm font-medium text-slate-700">
                        Nama Organisasi / HIMA <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1.5">
                        <input id="partner_name" name="partner_name" type="text" required placeholder="Contoh: HIMA Informatika / KOMA Amikom" value="{{ old('partner_name') }}"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 text-sm focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 transition">
                    </div>
                    @error('partner_name') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700">
                        Nama PIC (Person in Charge) <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1.5">
                        <input id="name" name="name" type="text" required placeholder="Nama lengkap penanggung jawab" value="{{ old('name') }}"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 text-sm focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 transition">
                    </div>
                    @error('name') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">
                        Email Aktif <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1.5">
                        <input id="email" name="email" type="email" required placeholder="hima@amikom.ac.id" value="{{ old('email') }}"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 text-sm focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 transition">
                    </div>
                    @error('email') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1.5">
                            <input id="password" name="password" type="password" required placeholder="Minimal 8 karakter"
                                class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 text-sm focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 transition">
                        </div>
                        @error('password') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700">
                            Ulangi Password <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1.5">
                            <input id="password_confirmation" name="password_confirmation" type="password" required placeholder="Ulangi password di atas"
                                class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 text-sm focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 transition">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="logo" class="block text-sm font-medium text-slate-700">
                        Logo Organisasi / HIMA <span class="text-slate-400 text-xs font-normal">(Opsional)</span>
                    </label>
                    <div class="mt-1.5">
                        <input id="logo" name="logo" type="file" accept="image/*" onchange="if(this.files[0] && this.files[0].size > 3 * 1024 * 1024) { alert('⚠️ PERINGATAN:\nUkuran file logo terlalu besar (' + (this.files[0].size / 1024 / 1024).toFixed(1) + ' MB).\n\nBatas maksimal di Vercel adalah 3 MB agar tidak ditolak sistem.\nSilakan kompres foto Anda terlebih dahulu di iloveimg.com atau pilih foto lain yang ukurannya di bawah 2 MB!'); this.value = ''; }"
                            class="block w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition border border-slate-300 rounded-xl bg-white p-1.5 cursor-pointer focus:outline-none focus:border-indigo-600">
                    </div>
                    <p class="mt-1 text-[11px] text-slate-400">Format dukung: PNG, JPG, JPEG (Maks. 2MB)</p>
                    @error('logo') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="flex w-full justify-center rounded-xl bg-indigo-600 py-3 px-4 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition">
                        Daftar Sebagai Penyelenggara Sekarang
                    </button>
                </div>
            </form>

            <!-- Tautan Navigasi Bawah -->
            <div class="mt-8 pt-6 border-t border-slate-100 text-center space-y-2">
                <p class="text-xs text-slate-500">
                    Sudah punya akun partner? 
                    <a href="{{ route('admin.login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 hover:underline transition">
                        Masuk ke Dashboard Partner
                    </a>
                </p>
                <p class="text-xs text-slate-500">
                    Hanya ingin membeli tiket? 
                    <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 hover:underline transition">
                        &larr; Masuk sebagai Pembeli
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
