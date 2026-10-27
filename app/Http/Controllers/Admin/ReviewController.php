<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['product', 'user'])
            ->when($request->filled('hidden'), function ($q) use ($request) {
                $q->where('is_hidden', $request->boolean('hidden'));
            })
            ->when($request->filled('rating'), fn ($q) => $q->where('rating', $request->rating))
            ->latest();

        $reviews = $query->paginate(15)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggle(Review $review): RedirectResponse
    {
        $review->update(['is_hidden' => ! $review->is_hidden]);

        $action = $review->is_hidden ? 'disembunyikan' : 'ditampilkan';

        return back()->with('success', "Review {$action}.");
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'Review dihapus.');
    }
}
