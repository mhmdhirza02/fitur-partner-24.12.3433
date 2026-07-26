@extends('layouts.admin')
@section('title', 'Kelola Voucher - Admin')
@section('page_title', 'Kelola Voucher & Kupon Diskon')
@section('page_subtitle', 'Buat dan kelola kode kupon promosi untuk menarik penonton event Anda.')

@section('content')

@if(session('success'))
    <div class="bg-emerald-100 text-emerald-800 p-4 rounded-2xl mb-6 font-bold text-sm shadow-sm border border-emerald-200/50 animate-fade-in flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-rose-100 text-rose-700 p-4 rounded-2xl mb-6 font-bold text-sm shadow-sm border border-rose-200/50 animate-fade-in flex items-center gap-3">
        <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        {{ session('error') }}
    </div>
@endif

<div class="mb-6 text-right">
    <a href="{{ route('admin.vouchers.create') }}" class="inline-flex items-center gap-2 px-6 py-3.5 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 active:scale-95 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Voucher Baru
    </a>
</div>

<div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-100">
        <form action="{{ route('admin.vouchers.index') }}" method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode atau nama voucher..." 
                   class="w-full px-5 py-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium text-sm">
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 active:scale-95 transition text-sm">
                Cari
            </button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
                <tr>
                    <th class="px-8 py-4">Kode & Nama Voucher</th>
                    <th class="px-8 py-4">Event Tujuan</th>
                    <th class="px-8 py-4">Besaran Diskon</th>
                    <th class="px-8 py-4">Kuota Terpakai</th>
                    <th class="px-8 py-4">Masa Berlaku</th>
                    <th class="px-8 py-4">Status</th>
                    <th class="px-8 py-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y border-t border-slate-100">
                @forelse($vouchers as $voucher)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-8 py-6">
                        <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-700 font-black rounded-lg text-xs tracking-wider mb-1 border border-indigo-100">
                            {{ $voucher->code }}
                        </span>
                        <p class="font-bold text-slate-800 text-sm">{{ $voucher->name }}</p>
                        @if($voucher->partner)
                            <span class="text-[10px] text-slate-400 font-bold">Oleh: {{ $voucher->partner->name }}</span>
                        @else
                            <span class="text-[10px] text-indigo-500 font-bold">Promo Global / Sistem</span>
                        @endif
                    </td>
                    <td class="px-8 py-6">
                        @if($voucher->event)
                            <span class="font-bold text-slate-700 text-xs line-clamp-1">{{ $voucher->event->title }}</span>
                        @else
                            <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-bold">Semua Event</span>
                        @endif
                    </td>
                    <td class="px-8 py-6">
                        @if($voucher->discount_type === 'percent')
                            <span class="font-black text-emerald-600 text-base">{{ $voucher->discount_value }}%</span>
                            @if($voucher->max_discount)
                                <p class="text-[10px] text-slate-400 font-bold">Maks. Rp {{ number_format($voucher->max_discount, 0, ',', '.') }}</p>
                            @endif
                        @else
                            <span class="font-black text-emerald-600 text-base">Rp {{ number_format($voucher->discount_value, 0, ',', '.') }}</span>
                        @endif
                        @if($voucher->min_purchase > 0)
                            <p class="text-[10px] text-slate-400 font-bold">Min. Beli Rp {{ number_format($voucher->min_purchase, 0, ',', '.') }}</p>
                        @endif
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-700 text-sm">{{ $voucher->used_count }}</span>
                            <span class="text-slate-300">/</span>
                            <span class="text-slate-500 text-sm font-medium">{{ $voucher->quota }}</span>
                        </div>
                        <div class="w-20 bg-slate-100 h-1.5 rounded-full overflow-hidden mt-1">
                            <div class="bg-indigo-600 h-full" style="width: {{ min(100, ($voucher->used_count / max(1, $voucher->quota)) * 100) }}%"></div>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-xs font-medium text-slate-600">
                        @if($voucher->start_date || $voucher->end_date)
                            <p>{{ $voucher->start_date ? $voucher->start_date->format('d M Y') : 'Sekarang' }} -</p>
                            <p>{{ $voucher->end_date ? $voucher->end_date->format('d M Y') : 'Seterusnya' }}</p>
                        @else
                            <span class="text-slate-400">Selamanya</span>
                        @endif
                    </td>
                    <td class="px-8 py-6">
                        @if($voucher->is_active && ($voucher->quota > $voucher->used_count) && (!$voucher->end_date || $voucher->end_date->isFuture()))
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-xl text-xs font-bold inline-flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Aktif
                            </span>
                        @else
                            <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-xl text-xs font-bold">
                                Tidak Aktif / Habis
                            </span>
                        @endif
                    </td>
                    <td class="px-8 py-6">
                        @if(auth()->user()->role === 'superadmin' || $voucher->partner_id === auth()->user()->partner_id)
                        <div class="flex gap-2">
                            <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" 
                               class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition" title="Ubah Voucher">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 00-2 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus voucher ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2.5 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition" title="Hapus Voucher">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                        @else
                        <span class="text-xs text-slate-400 font-bold">Milik Lain</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-8 py-12 text-center text-slate-500 font-medium">Belum ada voucher yang dibuat. Klik tombol "Tambah Voucher Baru" untuk mulai membuat promosi!</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($vouchers->hasPages())
    <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100 items-center">
        {{ $vouchers->links() }}
    </div>
    @endif
</div>

@endsection
