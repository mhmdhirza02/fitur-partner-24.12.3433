<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::with(['event', 'partner'])->latest();

        if (auth()->user()->role === 'partner') {
            $query->where('partner_id', auth()->user()->partner_id);
        }

        if ($request->has('search')) {
            $query->where('code', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('name', 'LIKE', '%' . $request->search . '%');
        }

        $vouchers = $query->paginate(10)->withQueryString();
        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        if (auth()->user()->role === 'partner') {
            $events = Event::where('partner_id', auth()->user()->partner_id)->get();
        } else {
            $events = Event::all();
        }

        return view('admin.vouchers.create', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code',
            'name' => 'required|string|max:255',
            'discount_type' => 'required|in:percent,nominal',
            'discount_value' => 'required|integer|min:1',
            'max_discount' => 'nullable|integer|min:0',
            'min_purchase' => 'nullable|integer|min:0',
            'quota' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        Voucher::create([
            'event_id' => $request->event_id ?: null,
            'partner_id' => auth()->user()->role === 'partner' ? auth()->user()->partner_id : null,
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'max_discount' => $request->max_discount ?: null,
            'min_purchase' => $request->min_purchase ?: 0,
            'quota' => $request->quota,
            'start_date' => $request->start_date ?: null,
            'end_date' => $request->end_date ?: null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.vouchers.index')->with('success', 'Kode voucher berhasil dibuat!');
    }

    public function edit(Voucher $voucher)
    {
        if (auth()->user()->role !== 'superadmin' && $voucher->partner_id !== auth()->user()->partner_id) {
            return redirect()->route('admin.vouchers.index')->with('error', 'Anda tidak berhak mengubah voucher ini.');
        }

        if (auth()->user()->role === 'partner') {
            $events = Event::where('partner_id', auth()->user()->partner_id)->get();
        } else {
            $events = Event::all();
        }

        return view('admin.vouchers.edit', compact('voucher', 'events'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        if (auth()->user()->role !== 'superadmin' && $voucher->partner_id !== auth()->user()->partner_id) {
            return redirect()->route('admin.vouchers.index')->with('error', 'Anda tidak berhak mengubah voucher ini.');
        }

        $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code,' . $voucher->id,
            'name' => 'required|string|max:255',
            'discount_type' => 'required|in:percent,nominal',
            'discount_value' => 'required|integer|min:1',
            'max_discount' => 'nullable|integer|min:0',
            'min_purchase' => 'nullable|integer|min:0',
            'quota' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $voucher->update([
            'event_id' => $request->event_id ?: null,
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'max_discount' => $request->max_discount ?: null,
            'min_purchase' => $request->min_purchase ?: 0,
            'quota' => $request->quota,
            'start_date' => $request->start_date ?: null,
            'end_date' => $request->end_date ?: null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil diperbarui!');
    }

    public function destroy(Voucher $voucher)
    {
        if (auth()->user()->role !== 'superadmin' && $voucher->partner_id !== auth()->user()->partner_id) {
            return redirect()->route('admin.vouchers.index')->with('error', 'Anda tidak berhak menghapus voucher ini.');
        }

        $voucher->delete();
        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil dihapus!');
    }
}
