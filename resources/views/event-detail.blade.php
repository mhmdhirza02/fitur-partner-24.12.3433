@extends('layouts.app')

@section('content')
<main class="max-w-7xl mx-auto px-6 py-12">
    @if(session('error'))
    <div class="mb-8 p-4 bg-red-100 text-red-700 rounded-2xl font-bold flex items-center gap-3 shadow-sm">
        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Left: Poster -->
        <div class="lg:col-span-1">
            <div class="sticky top-32">
                <img src="{{ $event->poster_url }}" alt="{{ $event->title }}"
                    class="w-full rounded-[2.5rem] shadow-2xl border-8 border-white object-cover aspect-[3/4]">
                <div class="mt-8 p-6 bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow group cursor-pointer" onclick="window.location.href='{{ $event->partner ? route('partner.profile', $event->partner->id) : '#' }}'">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-bold">Penyelenggara</h4>
                        @if($event->partner)
                        <svg class="w-5 h-5 text-indigo-300 group-hover:text-indigo-600 transition-colors transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        @endif
                    </div>
                    <div class="flex items-center gap-4">
                        @if($event->partner && $event->partner->logo_url && !str_contains($event->partner->logo_url, 'placehold.co'))
                            <img src="{{ $event->partner->logo_url }}" class="w-12 h-12 rounded-full border-2 border-indigo-50 object-contain">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($event->partner ? $event->partner->name : 'AEH') }}&background=4f46e5&color=fff&size=128&bold=true" class="w-12 h-12 rounded-full border-2 border-indigo-50 object-cover">
                        @endif
                        <div>
                            <p class="font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $event->partner ? $event->partner->name : 'Penyelenggara Tidak Diketahui' }}</p>
                            <p class="text-xs text-slate-500">Verified Organizer</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Details -->
        <div class="lg:col-span-2 space-y-12">
            <div class="space-y-4">
                <span
                    class="px-4 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-sm font-bold uppercase tracking-wider">{{ $event->category->name }}</span>
                <h1 class="text-4xl md:text-5xl font-black leading-tight">{{ $event->title }}</h1>
                <div class="flex flex-wrap gap-6 text-slate-500 font-medium">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span>{{ \Carbon\Carbon::parse($event->date)->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>{{ $event->location }}</span>
                    </div>
                </div>
            </div>

            <div class="prose prose-slate max-w-none">
                <h3 class="text-2xl font-bold mb-4">Deskripsi Event</h3>
                <p class="text-lg text-slate-600 leading-relaxed">
                    {{ $event->description }}
                </p>
            </div>

            @if($event->ticketTiers->count() > 0)
                <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-sm border border-slate-200/80 space-y-8">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-6">
                        <div>
                            <span class="px-3.5 py-1.5 bg-indigo-50 text-indigo-700 rounded-xl text-xs font-black uppercase tracking-wider border border-indigo-100 mb-3 inline-block">Kategori Tiket</span>
                            <h2 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">Pilih Paket Acara</h2>
                        </div>
                        <p class="text-slate-500 text-xs md:text-sm max-w-xs leading-relaxed font-medium">Harga dan ketersediaan tiket dapat berubah sesuai ketentuan waktu dan kuota dari penyelenggara.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($event->ticketTiers as $tier)
                            @php
                                $status = $tier->status;
                            @endphp
                            <div class="rounded-3xl p-6 border transition-all relative flex flex-col justify-between {{ $status === 'active' ? 'bg-slate-50/70 hover:bg-white border-slate-200/80 hover:border-indigo-600 shadow-sm hover:shadow-xl hover:shadow-indigo-500/5 group' : 'bg-slate-50/40 border-slate-200/50 opacity-60' }}">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-4">
                                        <span class="font-extrabold text-lg text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $tier->name }}</span>
                                        @if($status === 'active')
                                            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl text-[11px] font-bold">Tersedia</span>
                                        @elseif($status === 'coming_soon')
                                            <span class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-xl text-[11px] font-bold">Segera Dibuka</span>
                                        @elseif($status === 'ended')
                                            <span class="px-3 py-1 bg-slate-100 text-slate-600 border border-slate-200 rounded-xl text-[11px] font-bold">Berakhir</span>
                                        @else
                                            <span class="px-3 py-1 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl text-[11px] font-bold">Habis</span>
                                        @endif
                                    </div>
                                    <h3 class="text-3xl font-black text-slate-900 mb-4 tracking-tight">
                                        Rp {{ number_format($tier->price, 0, ',', '.') }}
                                    </h3>
                                    @if($tier->start_date || $tier->end_date)
                                        <div class="text-xs text-slate-600 mb-6 bg-white p-3.5 rounded-2xl border border-slate-200/70 space-y-1.5 shadow-2xs">
                                            @if($tier->start_date)
                                            <p class="flex items-center justify-between gap-1.5">
                                                <span class="text-slate-400 font-medium">Buka:</span>
                                                <span class="font-bold text-slate-700">{{ $tier->start_date->format('d M Y, H:i') }}</span>
                                            </p>
                                            @endif
                                            @if($tier->end_date)
                                            <p class="flex items-center justify-between gap-1.5">
                                                <span class="text-slate-400 font-medium">Tutup:</span>
                                                <span class="font-bold text-slate-700">{{ $tier->end_date->format('d M Y, H:i') }}</span>
                                            </p>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="pt-4 border-t border-slate-200/70 mt-2">
                                    <div class="flex justify-between text-xs text-slate-500 mb-4 font-medium">
                                        <span>Ketersediaan:</span>
                                        <span class="font-bold {{ $tier->stock > 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $tier->stock > 0 ? 'Sisa ' . $tier->stock . ' tiket' : 'Habis' }}</span>
                                    </div>
                                    @if($status === 'active')
                                        <a href="{{ url('checkout/'.$event->id.'?tier_id='.$tier->id) }}" class="w-full block text-center py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold rounded-2xl text-sm transition-all shadow-lg shadow-indigo-100 active:scale-95">
                                            Pilih Tiket Ini
                                        </a>
                                    @else
                                        <button disabled class="w-full py-4 bg-slate-100 text-slate-400 font-bold rounded-2xl text-sm cursor-not-allowed border border-slate-200/80">
                                            {{ $status === 'coming_soon' ? 'Belum Dibuka' : ($status === 'ended' ? 'Masa Berakhir' : 'Tiket Habis') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
            <div
                class="bg-indigo-600 rounded-[2.5rem] p-8 md:p-12 text-white shadow-2xl shadow-indigo-200 relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
                    <div>
                        <p class="text-indigo-200 font-bold uppercase tracking-widest text-sm mb-2">Harga Tiket</p>
                        <h2 class="text-5xl font-black">Rp {{ number_format($event->price, 0, ',', '.') }} <span class="text-lg font-medium text-indigo-200">/
                                orang</span></h2>
                        <p class="mt-4 text-indigo-100 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            @if($event->stock > 0)
                                Sisa stok: <span class="font-bold underline">{{ $event->stock }} Tiket lagi!</span>
                            @else
                                <span class="font-bold text-rose-300">Tiket Habis!</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        @if($event->stock > 0)
                            <a href="{{url('checkout/'.$event->id)}}"
                                class="inline-block px-10 py-5 bg-white text-indigo-600 rounded-2xl font-black text-xl hover:scale-105 transition-transform shadow-xl">
                                Pesan Sekarang
                            </a>
                        @else
                            <button disabled
                                class="inline-block px-10 py-5 bg-slate-400 text-slate-200 rounded-2xl font-black text-xl cursor-not-allowed shadow-none">
                                Tiket Habis
                            </button>
                        @endif
                    </div>
                </div>
                <!-- Decoration -->
                <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-white opacity-10 rounded-full"></div>
                <div class="absolute -left-10 -top-10 w-32 h-32 bg-indigo-400 opacity-20 rounded-full"></div>
            </div>
            @endif

            <div class="space-y-4">
                <h3 class="text-xl font-bold">Kebijakan Tiket</h3>
                <ul class="space-y-3 text-slate-500">
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-green-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        E-Ticket akan dikirimkan otomatis setelah pembayaran berhasil.
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-green-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Tiket dapat discan di pintu masuk (Check-in).
                    </li>
                    <li class="flex items-start gap-2 text-rose-500">
                        <svg class="w-5 h-5 text-rose-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Tiket yang sudah dibeli tidak dapat direfund.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</main>
@endsection