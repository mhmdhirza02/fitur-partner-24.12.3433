@extends('layouts.admin')
@section('title', 'Edit Partner - Admin')
@section('page_title', 'Edit Partner')
@section('page_subtitle', 'Perbarui detail partner kemitraan Anda.')

@section('content')
<div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm max-w-3xl">
    <form action="{{ route('admin.partners.update', $partner->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Nama Partner</label>
            <input type="text" name="name" value="{{ old('name', $partner->name) }}" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required placeholder="Contoh: Universitas Amikom Yogyakarta">
            @error('name') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Logo URL</label>
            <select name="logo_url" class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium cursor-pointer" required>
                <option value="https://placehold.co/200x200" {{ old('logo_url', $partner->logo_url) == 'https://placehold.co/200x200' ? 'selected' : '' }}>
                    Logo 1 (Default Gray)
                </option>
                <option value="https://placehold.co/200x200/orange/white" {{ old('logo_url', $partner->logo_url) == 'https://placehold.co/200x200/orange/white' ? 'selected' : '' }}>
                    Logo 2 (Orange Accent)
                </option>
                <option value="https://placehold.co/200x200/black/white" {{ old('logo_url', $partner->logo_url) == 'https://placehold.co/200x200/black/white' ? 'selected' : '' }}>
                    Logo 3 (Dark Monochrome)
                </option>
            </select>
            @error('logo_url') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="pt-4 flex justify-end gap-4 border-t border-slate-100">
            <a href="{{ route('admin.partners.index') }}" class="px-6 py-4 text-slate-500 font-bold hover:text-slate-800 transition">Batal</a>
            <button type="submit" class="px-8 py-4 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition">Perbarui Partner</button>
        </div>
    </form>
</div>
@endsection
