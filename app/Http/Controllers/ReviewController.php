<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['store']);
        $this->middleware('admin')->only(['index', 'destroy']);
    }

    public function index()
    {
        $reviews = Review::with(['user', 'product'])
            ->latest()
            ->paginate(20);

        return view('reviews.index', compact('reviews'));
    }

    public function store(Request $request, Product $product)
    {
        $user = Auth::user();

        if (!$this->userHasCompletedTransactionForProduct($user->id, $product->id)) {
            return redirect()
                ->route('products.show', $product)
                ->with('error', 'You can only review products you have purchased.');
        }

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:2000',
        ]);

        Review::updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $product->id,
            ],
            $data
        );

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Your review has been saved.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()
            ->back()
            ->with('success', 'Review deleted successfully.');
    }

    protected function userHasCompletedTransactionForProduct(int $userId, int $productId): bool
    {
        return TransactionItem::where('product_id', $productId)
            ->whereHas('transaction', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', 'completed');
            })
            ->exists();
    }
}

