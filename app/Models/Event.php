<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    protected $fillable = [
        'category_id', 'partner_id', 'title', 'description', 'date',
        'location', 'price', 'stock', 'poster_path'
        ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function getPosterUrlAttribute()
    {
        if (empty($this->poster_path) || $this->poster_path === '0') {
            return 'https://placehold.co/600x800/4f46e5/ffffff?text=Event+Poster';
        }

        if (str_starts_with($this->poster_path, 'http://') || str_starts_with($this->poster_path, 'https://')) {
            return $this->poster_path;
        }

        // Cek apakah ada di storage lokal (public disk)
        if (Storage::disk('public')->exists($this->poster_path)) {
            return asset('storage/' . $this->poster_path);
        }

        // Cek jika ada di lokal dengan atau tanpa prefix 'posters/'
        $cleanPath = ltrim(preg_replace('/^(posters\/)+/', '', $this->poster_path), '/');
        if (Storage::disk('public')->exists('posters/' . $cleanPath)) {
            return asset('storage/posters/' . $cleanPath);
        }
        if (Storage::disk('public')->exists($cleanPath)) {
            return asset('storage/' . $cleanPath);
        }

        // Jika tidak ada di lokal, gunakan URL Supabase S3 sesuai jalur aslinya yang disimpan saat upload
        return Storage::disk('s3')->url($this->poster_path);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
        
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function ticketTiers()
    {
        return $this->hasMany(TicketTier::class)->orderBy('price', 'asc');
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }
}
