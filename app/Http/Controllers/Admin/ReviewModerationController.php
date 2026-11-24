<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;

class ReviewModerationController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Rating::with('client', 'partner')
            ->when($request->query('filter') === 'low', fn ($q) => $q->where('score', '<=', 2))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function destroy(Rating $rating)
    {
        $rating->delete();

        return back()->with('success', 'Avis supprimé.');
    }
}