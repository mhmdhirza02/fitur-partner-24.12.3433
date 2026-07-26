<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'category_id', 'partner_id', 'title', 'description', 'date',
        'location', 'price', 'stock', 'poster_path'
        ];

    protected $casts = [
        'date' => 'datetime',
    ];

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
