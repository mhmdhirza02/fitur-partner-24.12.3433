<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = [
        'name',
        'logo_url',
        'is_approved',
    ];

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