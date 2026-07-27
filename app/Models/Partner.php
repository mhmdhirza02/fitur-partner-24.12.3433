<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Partner extends Model
{
    protected $fillable = [
        'name',
        'logo_url',
        'is_approved',
    ];

    public function getLogoUrlAttribute($value)
    {
        if (empty($value) || str_contains($value, 'placehold.co')) {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name ?? 'Partner') . '&background=4f46e5&color=fff&size=128&bold=true';
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        // Cek apakah ada di storage lokal (public disk)
        if (Storage::disk('public')->exists($value)) {
            return asset(ltrim($value, '/'));
        }

        // Clean path dari prefix 'storage/partners/', '/storage/partners/', 'partners/', atau 'storage/'
        $cleanPath = ltrim(preg_replace('/^(storage\/)?(partners\/)?/', '', ltrim($value, '/')), '/');

        if (Storage::disk('public')->exists('partners/' . $cleanPath)) {
            return asset('storage/partners/' . $cleanPath);
        }

        // Gunakan URL Supabase S3 (bucket partners)
        $baseUrl = rtrim(env('AWS_URL', 'https://bcmuergskjegjquvrpkc.supabase.co/storage/v1/object/public'), '/');
        return $baseUrl . '/partners/' . $cleanPath;
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function getReviewsAttribute()
    {
        return \App\Models\Review::whereHas('transaction.event', function ($query) {
            $query->where('partner_id', $this->id);
        })->latest()->get();
    }
}