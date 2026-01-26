<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    public function __construct(
        protected ReviewService $reviewService
    ) {}

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Review::class);

        $user = $request->user();
        $rating = $request->input('rating');

        // Get reviews based on user role
        if ($user->hasRole('superadmin')) {
            $reviews = $this->reviewService->getAllReviews(15, $rating);
            $statistics = $this->reviewService->getAllStatistics();
        } else {
            $reviews = $this->reviewService->getReviewsForAdmin($user, 15, $rating);
            $statistics = $this->reviewService->getStatisticsForAdmin($user);
        }

        return view('pages.reviews.index', compact('reviews', 'statistics'));
    }

    public function destroy(Review $review)
    {
        Gate::authorize('delete', $review);

        try {
            $this->reviewService->deleteReview($review);
            return redirect()->route('reviews.index')
                ->with('success', 'Отзыв успешно удален!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Ошибка при удалении отзыва: ' . $e->getMessage());
        }
    }
}
