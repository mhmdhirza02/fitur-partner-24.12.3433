@extends('layouts.admin')
@section('title', 'Kelola Kategori - Admin')
@section('page_title', 'Kelola Kategori')
@section('page_subtitle', 'Buat, ubah, dan hapus kategori event di sini.')

@section('content')

@if(session('error'))
    <div class="bg-rose-100 text-rose-700 p-4 rounded-2xl mb-6 font-bold text-sm shadow-sm border border-rose-200/50 animate-fade-in">
        {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Form Tambah Kategori -->
    <div class="lg:col-span-1">
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm sticky top-28">
            <h3 class="font-black text-lg text-slate-800 mb-4">Tambah Kategori</h3>
            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-widest">Nama Kategori</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Musik, Workshop..." 
                           class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
                    @error('name') 
                        <span class="text-rose-500 text-xs mt-1 block font-semibold">{{ $message }}</span> 
                    @enderror
                </div>
                <button type="submit" class="w-full px-6 py-4 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 active:scale-95 transition">
                    + Simpan Kategori
                </button>
            </form>
        </div>
    </div>

    <!-- Tabel Daftar Kategori -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <form action="{{ route('admin.categories.index') }}" method="GET" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kategori..." 
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
                            <th class="px-8 py-4 w-16">No</th>
                            <th class="px-8 py-4">ID</th>
                            <th class="px-8 py-4">Kategori</th>
                            <th class="px-8 py-4">Dibuat Pada</th>
                            <th class="px-8 py-4">Diubah Pada</th>
                            <th class="px-8 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y border-t">
                        @forelse($categories as $index => $category)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-8 py-6 font-bold text-slate-400">{{ $categories->firstItem() + $index }}</td>
                            <td class="px-8 py-6 text-slate-500 font-medium">#{{ $category->id }}</td>
                            <td class="px-8 py-6">
                                <p class="font-black text-slate-800">{{ $category->name }}</p>
                                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mt-0.5">
                                    {{ $category->slug }}
                                </p>
                            </td>
                            <td class="px-8 py-6 text-slate-600 font-medium text-sm">
                                {{ $category->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-8 py-6 text-slate-600 font-medium text-sm">
                                {{ $category->updated_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex gap-2">
                                    <!-- Edit Button (Triggers Modal via JS) -->
                                    <button onclick="openEditModal('{{ $category->id }}', '{{ addslashes($category->name) }}')"
                                            class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 00-2 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>

                                    <!-- Delete Button -->
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2.5 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-8 py-10 text-center text-slate-500 font-medium">Belum ada kategori yang ditambahkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($categories->hasPages())
            <div class="px-8 py-6 bg-slate-50/50 border-t">
                {{ $categories->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div id="editModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <!-- Backdrop with Fade -->
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300 opacity-0" id="modalBackdrop" onclick="closeEditModal()"></div>
    
    <!-- Modal Content -->
    <div class="bg-white w-full max-w-md rounded-[2.5rem] p-8 border border-slate-100 shadow-2xl relative z-10 transition-transform duration-300 transform scale-95 opacity-0" id="modalContainer">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-black text-xl text-slate-800">Ubah Nama Kategori</h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="editForm" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-widest">Nama Kategori</label>
                <input type="text" name="name" id="edit_name"
                       class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" required>
                @error('name') 
                    <span class="text-rose-500 text-xs mt-1 block font-semibold" id="edit_error">{{ $message }}</span> 
                @enderror
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeEditModal()" 
                        class="px-6 py-4 text-slate-500 font-bold hover:text-slate-800 transition">
                    Batal
                </button>
                <button type="submit" 
                        class="px-8 py-4 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, name) {
        const modal = document.getElementById('editModal');
        const backdrop = document.getElementById('modalBackdrop');
        const container = document.getElementById('modalContainer');
        const form = document.getElementById('editForm');
        const input = document.getElementById('edit_name');

        // Set form action dynamically
        form.action = `/admin/categories/${id}`;
        input.value = name;

        // Show modal and apply animations
        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            container.classList.remove('scale-95', 'opacity-0');
            container.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        const backdrop = document.getElementById('modalBackdrop');
        const container = document.getElementById('modalContainer');

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        container.classList.remove('scale-100', 'opacity-100');
        container.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>

@endsection
