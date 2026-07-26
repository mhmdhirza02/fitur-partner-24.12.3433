<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'event_id',
        'partner_id',
        'code',
        'name',
        'discount_type',
        'discount_value',
        'max_discount',
        'min_purchase',
        'quota',
        'used_count',
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

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Cek apakah voucher valid dan hitung besaran diskon
     */
    public function calculateDiscount($originalPrice)
    {
        if (!$this->is_active) {
            return 0;
        }

        if ($this->quota > 0 && $this->used_count >= $this->quota) {
            return 0;
        }

        if ($this->min_purchase > 0 && $originalPrice < $this->min_purchase) {
            return 0;
        }

        $now = now();
        if ($this->start_date && $now->lt($this->start_date)) {
            return 0;
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return 0;
        }

        if ($this->discount_type === 'percent') {
            $discount = ($originalPrice * $this->discount_value) / 100;
            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }
            return (int) $discount;
        }

        return (int) min($originalPrice, $this->discount_value);
    }
}
