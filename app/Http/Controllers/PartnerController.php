<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Partner;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PartnerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            function ($request, $next) {
                if (auth()->check() && auth()->user()->role === 'partner') {
                    abort(403, 'Unauthorized action.');
                }
                return $next($request);
            }
        ];
    }

    public function index(Request $request)
    {
        $query = Partner::query();
        if ($request->has('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }
        $partners = $query->latest()->paginate(10)->withQueryString();

        return view('admin.partners.index', compact('partners'));
    }

    public function create()
    {
        return view('admin.partners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $logoPath = $request->file('logo')->store('partners', 'public');

        Partner::create([
            'name' => $request->name,
            'logo_url' => Storage::url($logoPath),
        ]);

        return redirect()->route('admin.partners.index');
    }

    public function edit(Partner $partner)
    {
        return view('admin.partners.edit', compact('partner'));
    }

    public function update(Request $request, Partner $partner)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = ['name' => $request->name];

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('partners', 'public');
            $data['logo_url'] = Storage::url($logoPath);
        }

        $partner->update($data);

        return redirect()->route('admin.partners.index');
    }

    public function destroy(Partner $partner)
    {
        $partner->delete();

        return redirect()->route('admin.partners.index');
    }

    public function toggleApprove(Partner $partner)
    {
        $partner->update([
            'is_approved' => !$partner->is_approved
        ]);
        
        $status = $partner->is_approved ? 'disetujui' : 'ditangguhkan';
        return redirect()->route('admin.partners.index')->with('success', "Partner berhasil $status.");
    }
}