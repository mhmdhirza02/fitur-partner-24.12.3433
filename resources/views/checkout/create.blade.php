@extends('layouts.app')
@section('title', 'Checkout - ' . $event->title)
@section('content')
@php
    $basePrice = $selectedTier ? $selectedTier->price : $event->price;
    $tierName = $selectedTier ? $selectedTier->name : 'Regular / Normal';
    $adminFee = $basePrice <= 0 ? 0 : 5000;
    $totalPrice = $basePrice + $adminFee;
@endphp

<main class="max-w-3xl mx-auto px-6 py-20">
    <div class="mb-12">
        <a href="{{ route('events.show', $event->id) }}" class="text-indigo-600 font-bold flex items-center gap-2 mb-6 hover:underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali ke Detail Event
        </a>
        <h1 class="text-4xl font-extrabold tracking-tight">Checkout Pemesanan</h1>
        <p class="text-slate-500 mt-2">Lengkapi data diri dan klaim kupon diskon Anda di bawah ini.</p>
    </div>

    @if(session('error'))
    <div class="mb-6 p-4 bg-rose-100 text-rose-700 rounded-2xl font-bold border border-rose-200 flex items-center gap-3">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        {{ session('error') }}
    </div>
    @endif

    <div class="grid grid-cols-1 gap-8">
        <!-- Summary Card -->
        <div class="bg-white rounded-[2.5rem] border border-slate-200/80 p-8 shadow-sm">
            <h3 class="text-xl font-black mb-6 border-b border-slate-100 pb-4 flex items-center justify-between">
                <span>Ringkasan Pesanan</span>
                @if($selectedTier)
                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-xl text-xs font-black uppercase tracking-wider border border-indigo-100">
                    {{ $tierName }}
                </span>
                @endif
            </h3>
            <div class="flex gap-6 items-start">
                <img src="{{ ($event->poster_path && Storage::disk('public')->exists($event->poster_path))
                 ? asset('storage/' . $event->poster_path)
                 : 'https://placehold.co/200x200' }}"
                    alt="Event" class="w-24 h-24 rounded-2xl object-cover shadow-md">
                <div>
                    <h4 class="font-extrabold text-lg text-slate-900">{{ $event->title }}</h4>
                    <p class="text-slate-500 text-sm mt-1 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        {{ $event->date->format('d M Y, H:i') }}
                    </p>
                    <p class="text-slate-500 text-sm mt-0.5 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        {{ $event->location }}
                    </p>
                    <p class="text-indigo-600 font-black mt-2 text-sm">Tiket Pilihan: <span class="underline">{{ $tierName }}</span> (1 x Rp {{ number_format($basePrice, 0, ',', '.') }})</p>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 space-y-3 font-medium text-sm">
                <div class="flex justify-between text-slate-600">
                    <span>Harga Tiket ({{ $tierName }})</span>
                    <span class="font-bold text-slate-900">Rp {{ number_format($basePrice, 0, ',', '.') }}</span>
                </div>
                <div id="summary-discount-row" class="flex justify-between text-emerald-600 hidden font-bold">
                    <span>Potongan Voucher Diskon</span>
                    <span id="summary-discount">- Rp 0</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Biaya Layanan & Sistem</span>
                    @if($basePrice <= 0)
                        <span class="font-bold text-emerald-600">Gratis (Rp 0)</span>
                    @else
                        <span class="font-bold text-slate-900">Rp 5.000</span>
                    @endif
                </div>
                <div class="flex justify-between text-2xl font-black mt-4 pt-4 border-t border-slate-200 text-slate-900">
                    <span>Total Bayar</span>
                    @if($basePrice <= 0)
                        <span id="summary-total" class="text-emerald-600">Rp 0 (Gratis)</span>
                    @else
                        <span id="summary-total" class="text-indigo-600">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-[2.5rem] border border-slate-200/80 p-8 shadow-sm">
            @if(!auth()->check())
            <div class="mb-6">
                <a href="{{ route('auth.google', ['event_id' => $event->id, 'tier_id' => $selectedTier ? $selectedTier->id : null]) }}" class="w-full flex items-center justify-center gap-3 py-4 bg-white border-2 border-slate-200 rounded-2xl font-bold text-slate-700 hover:bg-slate-50 transition-all shadow-sm">
                    <svg class="w-6 h-6" viewBox="0 0 24 24">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    Continue with Google
                </a>
                
                <div class="mt-6 flex items-center justify-between">
                    <span class="w-1/5 border-b lg:w-1/4"></span>
                    <span class="text-xs text-center text-slate-400 uppercase font-black tracking-wider">Atau isi manual</span>
                    <span class="w-1/5 border-b lg:w-1/4"></span>
                </div>
            </div>
            @else
            <div class="mb-6 p-4 bg-indigo-50 border border-indigo-100 rounded-2xl flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Masuk sebagai</p>
                        <p class="font-bold text-slate-900">{{ auth()->user()->name }} <span class="text-xs text-slate-500">({{ auth()->user()->email }})</span></p>
                    </div>
                </div>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-checkout').submit();" class="text-xs text-rose-600 font-bold hover:underline">Ganti Akun</a>
                <form id="logout-form-checkout" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            </div>
            @endif

            <h3 class="text-xl font-bold mb-6 text-slate-900">
                Informasi Pembeli
            </h3>
            
            <form action="{{ route('checkout.store', $event->id) }}" method="POST" class="space-y-6">
                @csrf
                @if($selectedTier)
                    <input type="hidden" name="tier_id" value="{{ $selectedTier->id }}">
                @endif
                <input type="hidden" name="voucher_code" id="hidden_voucher_code" value="">

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Nama Lengkap</label>
                    <input type="text" name="customer_name" placeholder="Masukkan nama sesuai identitas"
                        class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium"
                        required value="{{ old('customer_name', auth()->check() ? auth()->user()->name : '') }}">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Email Aktif</label>
                        <input type="email" name="customer_email" placeholder="contoh@gmail.com"
                            class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium"
                            required value="{{ old('customer_email', auth()->check() ? auth()->user()->email : '') }}">
                        <p class="text-[11px] text-indigo-600 mt-1.5 font-bold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            *E-Ticket akan dikirim langsung ke email ini
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">No. WhatsApp</label>
                        <input type="tel" name="customer_phone" placeholder="08xxxxxxx"
                            class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium"
                            required value="{{ old('customer_phone') }}">
                    </div>
                </div>

                <!-- Voucher Code Input Box -->
                <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-3">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">
                        Kode Voucher / Kupon Diskon (Opsional)
                    </label>
                    <div class="flex gap-2.5">
                        <input type="text" id="voucher_input" placeholder="Masukkan kode promo" 
                               class="w-full px-4 py-3 bg-white border border-slate-300 rounded-xl font-bold uppercase text-sm outline-none focus:border-slate-800 transition">
                        <button type="button" onclick="applyVoucher()" id="btn_apply_voucher"
                                class="px-6 py-3 bg-slate-900 text-white font-semibold text-sm rounded-xl hover:bg-slate-800 transition flex-shrink-0">
                            Terapkan
                        </button>
                    </div>
                    <p id="voucher_msg" class="text-xs font-medium hidden"></p>
                </div>

                <button type="submit"
                    class="w-full py-5 bg-indigo-600 text-white rounded-2xl font-black text-xl shadow-xl shadow-indigo-200 hover:bg-indigo-700 active:scale-95 transition-all">
                    Lanjut ke Pembayaran
                </button>
                <p class="text-center text-xs text-slate-400">Dengan menekan tombol di atas, Anda menyetujui Syarat & Ketentuan serta Kebijakan Privasi kami.</p>
            </form>
        </div>
    </div>
</main>

<script>
let currentPrice = {{ $basePrice }};

function applyVoucher() {
    const code = document.getElementById('voucher_input').value.trim();
    const msgEl = document.getElementById('voucher_msg');
    const btn = document.getElementById('btn_apply_voucher');
    if (!code) return;

    btn.innerText = 'Cek...';
    btn.disabled = true;

    fetch("{{ route('checkout.check-voucher') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            code: code,
            event_id: {{ $event->id }},
            price: currentPrice
        })
    })
    .then(async res => {
        const data = await res.json().catch(() => null);
        if (!data) {
            throw new Error("Gagal membaca respon dari sistem.");
        }
        return data;
    })
    .then(data => {
        btn.innerText = 'Terapkan';
        btn.disabled = false;
        msgEl.classList.remove('hidden');

        if (data.success) {
            msgEl.className = "text-xs font-semibold text-emerald-700 block bg-emerald-50 p-3 rounded-xl border border-emerald-200";
            msgEl.innerText = data.message + " (Diskon " + data.discount_formatted + ")";
            document.getElementById('hidden_voucher_code').value = data.voucher_code;
            document.getElementById('summary-discount-row').classList.remove('hidden');
            document.getElementById('summary-discount').innerText = data.discount_formatted;
            document.getElementById('summary-total').innerText = data.final_price_formatted;
            document.getElementById('voucher_input').disabled = true;
            btn.innerText = 'Terpasang';
            btn.className = "px-6 py-3 bg-emerald-600 text-white font-semibold text-sm rounded-xl cursor-default flex-shrink-0";
            btn.onclick = null;
        } else {
            msgEl.className = "text-xs font-semibold text-rose-600 block bg-rose-50 p-3 rounded-xl border border-rose-200";
            msgEl.innerText = data.message || "Kode voucher tidak ditemukan atau tidak valid.";
            document.getElementById('hidden_voucher_code').value = "";
        }
    })
    .catch(err => {
        btn.innerText = 'Terapkan';
        btn.disabled = false;
        msgEl.classList.remove('hidden');
        msgEl.className = "text-xs font-semibold text-rose-600 block bg-rose-50 p-3 rounded-xl border border-rose-200";
        msgEl.innerText = "Kode voucher tidak dapat ditemukan atau tidak berlaku.";
    });
}
</script>
@endsection
