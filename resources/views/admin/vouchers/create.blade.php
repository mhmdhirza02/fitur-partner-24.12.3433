@extends('layouts.admin')
@section('title', 'Tambah Voucher Baru - Admin')
@section('page_title', 'Buat Voucher Promosi Baru')
@section('page_subtitle', 'Atur kode diskon, kuota, dan masa berlaku untuk menarik pembeli.')

@section('content')
<div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm max-w-3xl">
    <form action="{{ route('admin.vouchers.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Kode Voucher (Unik)</label>
                <input type="text" name="code" value="{{ old('code') }}" 
                       class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-black uppercase tracking-wider" 
                       required placeholder="Contoh: MAHASISWA50">
                @error('code') <span class="text-rose-500 text-sm mt-1 block font-semibold">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Nama Promo / Keterangan</label>
                <input type="text" name="name" value="{{ old('name') }}" 
                       class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" 
                       required placeholder="Contoh: Diskon Mahasiswa 50%">
                @error('name') <span class="text-rose-500 text-sm mt-1 block font-semibold">{{ $message }}</span> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Pilih Event Tujuan</label>
            <select name="event_id" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium">
                <option value="">-- Berlaku untuk Semua Event Saya --</option>
                @foreach($events as $ev)
                    <option value="{{ $ev->id }}" {{ old('event_id') == $ev->id ? 'selected' : '' }}>{{ $ev->title }} (Rp {{ number_format($ev->price, 0, ',', '.') }})</option>
                @endforeach
            </select>
            <p class="text-xs text-slate-400 mt-1">Kosongkan jika voucher ini bisa digunakan untuk event apa pun milik Anda.</p>
            @error('event_id') <span class="text-rose-500 text-sm mt-1 block font-semibold">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-slate-50/70 rounded-2xl border border-slate-100">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Tipe Diskon</label>
                <select name="discount_type" id="discount_type" onchange="toggleDiscountFields()"
                        class="w-full px-5 py-4 bg-white border-2 border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-bold text-indigo-600">
                    <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : '' }}>Persentase (%)</option>
                    <option value="nominal" {{ old('discount_type') == 'nominal' ? 'selected' : '' }}>Nominal Rupiah (Rp)</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide" id="val_label">Besaran Diskon (%)</label>
                <input type="number" name="discount_value" id="discount_value" value="{{ old('discount_value', 50) }}" min="1"
                       class="w-full px-5 py-4 bg-white border-2 border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-black text-lg" required>
                @error('discount_value') <span class="text-rose-500 text-sm mt-1 block font-semibold">{{ $message }}</span> @enderror
            </div>

            <div id="max_disc_wrapper" class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Maksimal Diskon (Rp) - Opsional</label>
                <input type="number" name="max_discount" value="{{ old('max_discount') }}" placeholder="Contoh: 25000 (Maksimal potongan Rp 25.000)"
                       class="w-full px-5 py-4 bg-white border-2 border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium">
                <p class="text-xs text-slate-400 mt-1">Berguna agar diskon persentase tidak terlalu besar pada tiket mahal.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Minimal Pembelian (Rp)</label>
                <input type="number" name="min_purchase" value="{{ old('min_purchase', 0) }}" min="0"
                       class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" placeholder="0 (Tanpa minimal)">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Batas Kuota Penggunaan</label>
                <input type="number" name="quota" value="{{ old('quota', 100) }}" min="1"
                       class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Tanggal Mulai Berlaku (Opsional)</label>
                <input type="datetime-local" name="start_date" value="{{ old('start_date') }}"
                       class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Tanggal Berakhir / Kedaluwarsa</label>
                <input type="datetime-local" name="end_date" value="{{ old('end_date') }}"
                       class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium">
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                   class="w-6 h-6 text-indigo-600 bg-slate-100 border-slate-300 rounded focus:ring-indigo-500">
            <label for="is_active" class="text-sm font-bold text-slate-800 cursor-pointer">Aktifkan Voucher Ini Sekarang</label>
        </div>

        <div class="pt-4 flex justify-end gap-4 border-t border-slate-100">
            <a href="{{ route('admin.vouchers.index') }}" class="px-6 py-4 text-slate-500 font-bold hover:text-slate-800 transition">Batal</a>
            <button type="submit" class="px-8 py-4 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 active:scale-95 transition">Simpan Voucher</button>
        </div>
    </form>
</div>

<script>
function toggleDiscountFields() {
    const type = document.getElementById('discount_type').value;
    const label = document.getElementById('val_label');
    const maxWrapper = document.getElementById('max_disc_wrapper');
    if (type === 'percent') {
        label.innerText = 'Besaran Diskon (%)';
        maxWrapper.classList.remove('hidden');
    } else {
        label.innerText = 'Potongan Nominal (Rp)';
        maxWrapper.classList.add('hidden');
    }
}
document.addEventListener('DOMContentLoaded', toggleDiscountFields);
</script>
@endsection
