@extends('layouts.admin')
@section('title', 'Edit Event - Admin')
@section('page_title', 'Edit Event')
@section('page_subtitle', 'Ubah detail acara.')

@section('content')
<div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm max-w-3xl">
    <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Judul Event</label>
            <input type="text" name="title" value="{{ old('title', $event->title) }}" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
            @error('title') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-bold text-slate-700 uppercase tracking-wide">Kategori</label>
                    <a href="{{ route('admin.categories.create') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 hover:underline transition" target="_blank">+ Tambah Kategori</a>
                </div>
                <select name="category_id" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            @if(auth()->user()->role === 'superadmin')
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Penyelenggara (Partner)</label>
                <select name="partner_id" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium">
                    <option value="">Tanpa Penyelenggara (Sistem)</option>
                    @foreach($partners as $partner)
                        <option value="{{ $partner->id }}" {{ old('partner_id', $event->partner_id) == $partner->id ? 'selected' : '' }}>{{ $partner->name }}</option>
                    @endforeach
                </select>
                @error('partner_id') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>
            @endif
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Deskripsi</label>
            <textarea name="description" rows="4" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium">{{ old('description', $event->description) }}</textarea>
            @error('description') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Tanggal & Waktu</label>
                <input type="datetime-local" name="date" value="{{ old('date', $event->date->format('Y-m-d\TH:i')) }}" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
                @error('date') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Lokasi</label>
                <input type="text" name="location" value="{{ old('location', $event->location) }}" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
                @error('location') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Harga (Rp)</label>
                <input type="text" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" name="price" value="{{ old('price', $event->price) }}" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
                @error('price') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Kapasitas (Stok)</label>
                <input type="text" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" name="stock" value="{{ old('stock', $event->stock) }}" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
                @error('stock') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Variasi & Harga Tiket (Opsional) -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-800 text-base">Variasi & Harga Tiket (Opsional)</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Tambahkan beberapa kategori tiket dengan harga dan kuota berbeda (misal: VIP, Early Bird, Presale).</p>
                </div>
                <button type="button" onclick="addTierRow()" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition shadow-sm flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Tambah Tiket</span>
                </button>
            </div>

            <div id="tiers-container" class="space-y-3">
                <!-- Dynamic rows inserted here -->
            </div>
            <p class="text-xs text-slate-400 bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                <span class="font-semibold text-slate-600">Catatan:</span> Jika Anda menambahkan variasi tiket di bawah ini, harga dan kuota dasar pada form di atas akan mengikuti sistem variasi tiket ini.
            </p>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Poster Event (Opsional)</label>
            <input type="file" name="poster" accept="image/*" onchange="if(this.files[0] && this.files[0].size > 3 * 1024 * 1024) { alert('⚠️ PERINGATAN:\nUkuran file gambar terlalu besar (' + (this.files[0].size / 1024 / 1024).toFixed(1) + ' MB).\n\nBatas maksimal di Vercel adalah 3 MB agar tidak ditolak sistem (Payload Too Large).\nSilakan kompres foto Anda terlebih dahulu di iloveimg.com atau pilih foto lain yang ukurannya di bawah 2 MB!'); this.value = ''; }" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium">
            @if($event->poster_path)
                <p class="text-sm text-slate-500 mt-2">Poster saat ini: <a href="{{ $event->poster_url }}" target="_blank" class="text-indigo-600 hover:underline">Lihat</a></p>
            @endif
            @error('poster') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="pt-4 flex justify-end gap-4 border-t border-slate-100">
            <a href="{{ route('admin.events.index') }}" class="px-6 py-4 text-slate-500 font-bold hover:text-slate-800 transition">Batal</a>
            <button type="submit" class="px-8 py-4 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition">Simpan Perubahan</button>
        </div>
    </form>
</div>

<script>
let tierIndex = 0;
function addTierRow(name = '', price = '', stock = '', start = '', end = '') {
    const container = document.getElementById('tiers-container');
    const id = `tier_row_${tierIndex}`;
    const html = `
        <div id="${id}" class="p-4 bg-slate-50/50 hover:bg-slate-50 rounded-xl border border-slate-200/80 transition grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            <div class="md:col-span-3">
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama Tiket</label>
                <input type="text" name="tiers[${tierIndex}][name]" value="${name}" placeholder="mis. Early Bird" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:border-slate-800 focus:ring-1 focus:ring-slate-800 transition">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Harga (Rp)</label>
                <input type="text" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" name="tiers[${tierIndex}][price]" value="${price}" placeholder="0" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:border-slate-800 focus:ring-1 focus:ring-slate-800 transition">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Kuota / Stok</label>
                <input type="text" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" name="tiers[${tierIndex}][stock]" value="${stock}" placeholder="100" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:border-slate-800 focus:ring-1 focus:ring-slate-800 transition">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Mulai Penjualan</label>
                <input type="datetime-local" name="tiers[${tierIndex}][start_date]" value="${start}" class="w-full px-2.5 py-2 bg-white border border-slate-200 rounded-lg text-xs text-slate-600 focus:outline-none focus:border-slate-800 focus:ring-1 focus:ring-slate-800 transition">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Selesai Penjualan</label>
                <input type="datetime-local" name="tiers[${tierIndex}][end_date]" value="${end}" class="w-full px-2.5 py-2 bg-white border border-slate-200 rounded-lg text-xs text-slate-600 focus:outline-none focus:border-slate-800 focus:ring-1 focus:ring-slate-800 transition">
            </div>
            <div class="md:col-span-1 flex justify-end">
                <button type="button" onclick="document.getElementById('${id}').remove()" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus baris">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    tierIndex++;
}

document.addEventListener('DOMContentLoaded', () => {
    @foreach($event->ticketTiers as $tier)
        addTierRow(
            "{{ addslashes($tier->name) }}", 
            "{{ $tier->price }}", 
            "{{ $tier->stock }}", 
            "{{ $tier->start_date ? $tier->start_date->format('Y-m-d\TH:i') : '' }}", 
            "{{ $tier->end_date ? $tier->end_date->format('Y-m-d\TH:i') : '' }}"
        );
    @endforeach
});
</script>
@endsection
