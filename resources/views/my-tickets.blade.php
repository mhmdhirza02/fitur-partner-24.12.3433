@extends('layouts.app')

@section('content')
<main class="max-w-7xl mx-auto px-6 py-12">
    <!-- Hero Banner & Search Section -->
    <div class="bg-slate-900 rounded-3xl p-8 sm:p-12 mb-12 shadow-xl relative overflow-hidden">
        <!-- Subtle Pattern/Texture (Optional) -->
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 24px 24px;"></div>

        <div class="relative z-10 max-w-3xl">
            <div class="mb-6">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight mb-3">
                    Riwayat Pesanan & E-Ticket
                </h1>
                <p class="text-slate-400 text-sm sm:text-base leading-relaxed max-w-2xl">
                    Kelola pesanan, pantau status pembayaran, dan akses seluruh e-ticket dari event yang akan atau telah Anda ikuti.
                </p>
            </div>

            <!-- Search Form -->
            <form action="{{ route('my-tickets') }}" method="GET" class="mt-8 max-w-2xl">
                <div class="relative flex items-center">
                    <div class="absolute left-4 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Cari Email atau Order ID (cth: TRX-...)"
                        class="w-full pl-12 pr-32 py-3.5 bg-white border-0 rounded-xl text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500 transition-shadow text-sm sm:text-base shadow-sm">
                    <button type="submit" 
                        class="absolute right-1.5 px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors text-sm sm:text-base flex items-center gap-2">
                        Cari
                    </button>
                </div>
                @if(request('search'))
                    <div class="mt-3 flex items-center gap-2 text-slate-300 text-sm">
                        <span>Menampilkan hasil pencarian untuk: <strong class="text-white">"{{ request('search') }}"</strong></span>
                        <span class="text-slate-500">•</span>
                        <a href="{{ route('my-tickets') }}" class="text-indigo-400 hover:text-indigo-300 transition-colors">Reset Filter</a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    @if($transactions->isEmpty())
        <!-- Empty State -->
        <div class="bg-white rounded-[2.5rem] border border-slate-200/80 p-12 text-center max-w-2xl mx-auto shadow-sm my-16">
            <div class="w-20 h-20 bg-indigo-50 text-indigo-600 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-inner">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-2">Belum Ada Tiket Ditemukan</h3>
            <p class="text-slate-500 text-sm sm:text-base leading-relaxed mb-8">
                @if(request('search'))
                    Kami tidak dapat menemukan pesanan tiket dengan kata kunci <span class="font-bold text-slate-700">"{{ request('search') }}"</span>. Pastikan Alamat Email atau Order ID yang Anda masukkan sudah benar.
                @elseif(auth()->check())
                    Anda belum memiliki riwayat pemesanan tiket dengan email <span class="font-bold text-slate-700">{{ auth()->user()->email }}</span>. Yuk, jelajahi berbagai event menarik dan pesan tiket pertamamu sekarang!
                @else
                    Silakan masukkan Alamat Email yang Anda gunakan saat pemesanan atau Nomor Order ID pada kolom pencarian di atas untuk melihat e-ticket Anda.
                @endif
            </p>
            <a href="{{ url('/#events') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-xl shadow-indigo-500/20 hover:scale-105 active:scale-95 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <span>Jelajahi Event Sekarang</span>
            </a>
        </div>
    @else
        <!-- Tickets Grid -->
        <div class="mb-6 flex items-center justify-between border-b border-slate-200 pb-4">
            <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                <span>Daftar Pesanan Anda</span>
                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-black">{{ $transactions->count() }}</span>
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($transactions as $trx)
                <div class="bg-white rounded-3xl border border-slate-200/80 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group hover:-translate-y-1">
                    <!-- Thumbnail & Status Banner -->
                    <div class="relative h-48 bg-slate-100 overflow-hidden">
                        <img src="{{ $trx->event->poster_url }}"
                             alt="{{ $trx->event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        
                        <div class="absolute top-4 right-4 z-10">
                            @if(in_array(strtolower($trx->status), ['success', 'settlement', 'capture']))
                                <span class="px-3.5 py-1.5 bg-green-500/90 backdrop-blur-md text-white rounded-full text-xs font-bold shadow-lg flex items-center gap-1.5 border border-white/20">
                                    <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                                    Berhasil / Lunas
                                </span>
                            @elseif(in_array(strtolower($trx->status), ['pending', 'challenge']))
                                <span class="px-3.5 py-1.5 bg-amber-500/90 backdrop-blur-md text-white rounded-full text-xs font-bold shadow-lg flex items-center gap-1.5 border border-white/20">
                                    <span class="w-1.5 h-1.5 bg-white rounded-full animate-ping"></span>
                                    Menunggu Bayar
                                </span>
                            @else
                                <span class="px-3.5 py-1.5 bg-red-500/90 backdrop-blur-md text-white rounded-full text-xs font-bold shadow-lg flex items-center gap-1.5 border border-white/20">
                                    Gagal / Expired
                                </span>
                            @endif
                        </div>

                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                        
                        <div class="absolute bottom-3 left-4 right-4 text-white flex items-center justify-between">
                            <span class="text-[10px] font-mono font-bold tracking-wider bg-white/20 backdrop-blur-md px-2.5 py-1 rounded-lg border border-white/20">
                                {{ $trx->order_id }}
                            </span>
                            <span class="text-[11px] font-medium text-slate-300">
                                {{ $trx->created_at->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>

                    <!-- Event Info -->
                    <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <h3 class="font-black text-lg text-slate-900 group-hover:text-indigo-600 transition-colors line-clamp-2 mb-3 leading-snug">
                                <a href="{{ route('events.show', $trx->event->id) }}">{{ $trx->event->title }}</a>
                            </h3>
                            <div class="space-y-2 text-slate-500 text-xs sm:text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span>{{ \Carbon\Carbon::parse($trx->event->date)->format('l, d M Y • H:i') }} WIB</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="truncate">{{ $trx->event->location }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-sm">
                            <span class="text-slate-400 font-medium">Total Bayar:</span>
                            @if($trx->total_price <= 0)
                                <span class="font-black text-green-600">Gratis (Rp 0)</span>
                            @else
                                <span class="font-black text-indigo-600">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="p-6 pt-0 flex gap-2.5">
                        @if(in_array(strtolower($trx->status), ['success', 'settlement', 'capture']))
                            <a href="{{ route('ticket', $trx->order_id) }}" class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-center text-sm rounded-2xl shadow-md shadow-indigo-200 transition-all flex items-center justify-center gap-2 active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                                <span>Lihat E-Ticket</span>
                            </a>
                            @if(!$trx->review)
                                <a href="{{ route('reviews.create', $trx->order_id) }}" title="Tulis Ulasan" class="px-4 py-3 bg-amber-50 hover:bg-amber-100 text-amber-600 font-bold rounded-2xl transition flex items-center justify-center text-sm border border-amber-200/50 hover:scale-105">
                                    ⭐ Ulas
                                </a>
                            @else
                                <span title="Sudah diulas" class="px-4 py-3 bg-green-50 text-green-600 font-bold rounded-2xl flex items-center justify-center text-sm cursor-default border border-green-200/50">
                                    ✓ Diulas
                                </span>
                            @endif
                        @elseif(in_array(strtolower($trx->status), ['pending', 'challenge']))
                            <a href="{{ route('checkout.payment', $trx->order_id) }}" class="flex-1 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold text-center text-sm rounded-2xl shadow-md shadow-amber-200 transition-all flex items-center justify-center gap-2 active:scale-95 animate-pulse">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                <span>Bayar Sekarang</span>
                            </a>
                        @else
                            <a href="{{ route('events.show', $trx->event_id) }}" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-center text-sm rounded-2xl transition-all flex items-center justify-center gap-2 active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <span>Pesan Ulang</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</main>
@endsection
