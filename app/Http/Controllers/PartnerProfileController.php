<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerProfileController extends Controller
{
    public function show($id)
    {
        $partner = Partner::with('events')->findOrFail($id);
        
        // Get reviews using the attribute we defined
        $reviews = $partner->reviews;
        
        $averageRating = $reviews->count() > 0 ? $reviews->avg('rating') : 0;
        
        return view('partners.show', compact('partner', 'reviews', 'averageRating'));
    }
}
