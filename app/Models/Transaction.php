<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'event_id', 'ticket_tier_id', 'ticket_tier_name', 'order_id', 'customer_name', 'customer_email', 'customer_phone', 'total_price', 'voucher_code', 'discount_amount', 'status', 'snap_token'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketTier()
    {
        return $this->belongsTo(TicketTier::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
