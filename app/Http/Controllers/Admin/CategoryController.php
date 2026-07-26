<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::query();
        if ($request->has('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }
        $categories = $query->latest()->paginate(10)->withQueryString();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'partner_id' => auth()->user()->role === 'partner' ? auth()->user()->partner_id : null,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource (redirected to index).
     */
    public function edit(Category $category)
    {
        return redirect()->route('admin.categories.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        if (auth()->user()->role !== 'superadmin' && $category->partner_id !== auth()->user()->partner_id) {
            return back()->with('error', 'Anda tidak berhak mengubah kategori milik sistem atau partner lain.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Nama kategori berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if (auth()->user()->role !== 'superadmin' && $category->partner_id !== auth()->user()->partner_id) {
            return back()->with('error', 'Anda tidak berhak menghapus kategori milik sistem atau partner lain.');
        }

        if ($category->events()->count() > 0) {
            return back()->with('error', 'Kategori ini tidak dapat dihapus karena masih digunakan oleh event yang ada.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus!');
    }
}
