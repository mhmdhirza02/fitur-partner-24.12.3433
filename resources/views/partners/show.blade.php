@extends('layouts.app')

@section('title', $partner->name . ' - Partner Profile')

@section('content')
<main class="max-w-7xl mx-auto px-6 py-12">
    <!-- Header Partner -->
    <div class="bg-white rounded-[2.5rem] p-10 shadow-xl border border-slate-100 flex flex-col md:flex-row items-center gap-8 mb-16 relative overflow-hidden">
        <!-- Decor -->
        <div class="absolute -top-20 -right-20 w-64 h-64 bg-indigo-50 rounded-full blur-3xl opacity-60 pointer-events-none"></div>

        <div class="w-40 h-40 shrink-0 bg-white border-4 border-indigo-50 rounded-full shadow-lg p-2 overflow-hidden flex items-center justify-center relative z-10">
            @if($partner->logo_url && !str_contains($partner->logo_url, 'placehold.co'))
                <img src="{{ str_starts_with($partner->logo_url, 'http') ? $partner->logo_url : asset(ltrim($partner->logo_url, '/')) }}" alt="{{ $partner->name }}" class="w-full h-full object-contain">
            @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode($partner->name) }}&background=4f46e5&color=fff&size=256&bold=true" alt="{{ $partner->name }}" class="w-full h-full object-cover rounded-full">
            @endif
        </div>
        
        <div class="flex-1 text-center md:text-left relative z-10">
            <h1 class="text-4xl font-black text-slate-900 mb-2">{{ $partner->name }}</h1>
            <p class="text-slate-500 font-medium mb-6">Penyelenggara Event Terverifikasi</p>
            
            <div class="flex flex-col md:flex-row gap-6 items-center">
                <div class="flex items-center gap-3 bg-indigo-50 px-6 py-3 rounded-2xl">
                    <svg class="w-8 h-8 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    <div>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ number_format($averageRating, 1) }} <span class="text-sm font-medium text-slate-500">/ 5.0</span></p>
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-1">Dari {{ $reviews->count() }} Ulasan</p>
                    </div>
                </div>
                <div class="text-center md:text-left">
                    <p class="text-2xl font-black text-slate-800">{{ $partner->events->count() }}</p>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Event Diselenggarakan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Layout Bawah (2 Kolom) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Kolom Kiri: Review -->
        <div class="lg:col-span-2">
            <h2 class="text-2xl font-black mb-8 flex items-center gap-3">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                Ulasan Pembeli
            </h2>

            @if($reviews->count() > 0)
                <div class="space-y-6">
                    @foreach($reviews as $review)
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="font-bold text-slate-800">{{ $review->transaction->customer_name }}</h4>
                                <p class="text-sm text-slate-500">Event: {{ $review->transaction->event->title }}</p>
                            </div>
                            <div class="flex items-center text-amber-400">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    @else
                                        <svg class="w-5 h-5 text-slate-200" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    @endif
                                @endfor
                            </div>
                        </div>
                        @if($review->comment)
                        <p class="text-slate-600 italic">"{{ $review->comment }}"</p>
                        @endif
                        <p class="text-xs text-slate-400 mt-4">{{ $review->created_at->diffForHumans() }}</p>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-slate-50 rounded-2xl border border-slate-100 border-dashed">
                    <p class="text-slate-500 font-medium">Belum ada ulasan untuk penyelenggara ini.</p>
                </div>
            @endif
        </div>

        <!-- Kolom Kanan: Event Lainnya -->
        <div class="lg:col-span-1 space-y-6">
            <h3 class="text-xl font-bold mb-6">Event oleh {{ $partner->name }}</h3>
            @foreach($partner->events->take(3) as $event)
            <a href="{{ route('events.show', $event->id) }}" class="block group">
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow border border-slate-100 flex h-28">
                    <img src="{{ $event->poster_url }}" class="w-24 h-full object-cover group-hover:scale-105 transition-transform">
                    <div class="p-4 flex flex-col justify-center">
                        <h4 class="font-bold text-slate-800 text-sm line-clamp-2 group-hover:text-indigo-600 transition-colors">{{ $event->title }}</h4>
                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}
                        </p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</main>
@endsection
