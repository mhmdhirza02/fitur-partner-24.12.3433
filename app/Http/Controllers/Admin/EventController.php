<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = \App\Models\Event::with('category');

        if (auth()->user()->role === 'partner') {
            $query->where('partner_id', auth()->user()->partner_id);
        }

        $events = $query->latest()->paginate(10);
        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = \App\Models\Category::all();
        $partners = auth()->user()->role === 'superadmin'
            ? \App\Models\Partner::all()
            : [];

        return view('admin.events.create', compact('categories', 'partners'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'partner_id' => 'nullable|exists:partners,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:1',
            'poster' => 'nullable|image|max:2048',
        ]);

        if (auth()->user()->role === 'partner') {
            $data['partner_id'] = auth()->user()->partner_id;
        }

     if ($request->hasFile('poster')) {
    $data['poster_path'] = $request->file('poster')->store('posters', 's3');
}


        $event = \App\Models\Event::create($data);

        if ($request->has('tiers') && is_array($request->tiers)) {
            foreach ($request->tiers as $tier) {
                if (!empty($tier['name']) && isset($tier['price']) && isset($tier['stock'])) {
                    $event->ticketTiers()->create([
                        'name' => $tier['name'],
                        'price' => $tier['price'],
                        'stock' => $tier['stock'],
                        'start_date' => !empty($tier['start_date']) ? $tier['start_date'] : null,
                        'end_date' => !empty($tier['end_date']) ? $tier['end_date'] : null,
                        'is_active' => true,
                    ]);
                }
            }
        }

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Data Event & Kategori Tiket berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        if (auth()->user()->role === 'partner' && $event->partner_id !== auth()->user()->partner_id) {
            abort(403);
        }

        $categories = \App\Models\Category::all();
        $partners = auth()->user()->role === 'superadmin'
            ? \App\Models\Partner::all()
            : [];

        $event->load('ticketTiers');

        return view('admin.events.edit', compact('event', 'categories', 'partners'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        if (auth()->user()->role === 'partner' && $event->partner_id !== auth()->user()->partner_id) {
            abort(403);
        }

        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'partner_id' => 'nullable|exists:partners,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:1',
            'poster' => 'nullable|image|max:2048',
        ]);

        if (auth()->user()->role === 'partner') {
            $data['partner_id'] = auth()->user()->partner_id;
        }

        // Upload poster baru ke Supabase Storage
        if ($request->hasFile('poster')) {

            // Hapus poster lama jika ada
            if ($event->poster_path) {
                Storage::disk('s3')->delete($event->poster_path);
            }

         $data['poster_path'] = $request->file('poster')->store('posters', 's3');
        }

        $event->update($data);

        if ($request->has('tiers') && is_array($request->tiers)) {

            $event->ticketTiers()->delete();

            foreach ($request->tiers as $tier) {
                if (!empty($tier['name']) && isset($tier['price']) && isset($tier['stock'])) {
                    $event->ticketTiers()->create([
                        'name' => $tier['name'],
                        'price' => $tier['price'],
                        'stock' => $tier['stock'],
                        'start_date' => !empty($tier['start_date']) ? $tier['start_date'] : null,
                        'end_date' => !empty($tier['end_date']) ? $tier['end_date'] : null,
                        'is_active' => true,
                    ]);
                }
            }
        }

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event & Kategori Tiket berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        if ($event->poster_path) {
            Storage::disk('s3')->delete($event->poster_path);
        }

        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Data event berhasil dihapus secara permanen.');
    }
}