<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PartnerReviewController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return view('partner.reviews', [
            'reviews' => $user->ratingsReceived()->with('client')->latest()->get(),
            'average' => $user->averageRating(),
        ]);
    }
}