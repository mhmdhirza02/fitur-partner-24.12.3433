<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketTier extends Model
{
    protected $fillable = [
        'event_id',
        'name',
        'price',
        'stock',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Cek status tier saat ini: 'active', 'coming_soon', 'ended', 'sold_out'
     */
    public function getStatusAttribute()
    {
        if (!$this->is_active || $this->stock <= 0) {
            return 'sold_out';
        }

        $now = now();
        if ($this->start_date && $now->lt($this->start_date)) {
            return 'coming_soon';
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return 'ended';
        }

        return 'active';
    }
}
