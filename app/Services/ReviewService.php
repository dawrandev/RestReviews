<?php

namespace App\Services;

use App\Models\Review;
use App\Repositories\ReviewRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReviewService
{
    public function __construct(
        protected ReviewRepository $reviewRepository
    ) {}

    /**
     * Get all reviews for a restaurant.
     */
    public function getRestaurantReviews(int $restaurantId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->reviewRepository->getByRestaurantId($restaurantId, $perPage);
    }

    /**
     * Get all reviews by a client.
     */
    public function getClientReviews(int $clientId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->reviewRepository->getByClientId($clientId, $perPage);
    }

    /**
     * Create or update a review (with device tracking).
     */
    public function createOrUpdateReview(?int $clientId, int $restaurantId, array $data): Review
    {
        // If client is authenticated, check by client_id
        if ($clientId) {
            $existingReview = $this->reviewRepository->findByClientAndRestaurant($clientId, $restaurantId);

            if ($existingReview) {
                return $this->reviewRepository->update($existingReview, $data);
            }
        }

        // For guests or new reviews, check by device_id
        $existingReviewByDevice = Review::where('device_id', $data['device_id'])
            ->where('restaurant_id', $restaurantId)
            ->first();

        if ($existingReviewByDevice) {
            return $this->reviewRepository->update($existingReviewByDevice, $data);
        }

        return $this->reviewRepository->create([
            'client_id' => $clientId,
            'restaurant_id' => $restaurantId,
            ...$data,
        ]);
    }

    /**
     * Update a review.
     */
    public function updateReview(Review $review, array $data): Review
    {
        return $this->reviewRepository->update($review, $data);
    }

    /**
     * Delete a review.
     */
    public function deleteReview(Review $review): bool
    {
        return $this->reviewRepository->delete($review);
    }

    /**
     * Get review statistics for a restaurant.
     */
    public function getRestaurantReviewStats(int $restaurantId): array
    {
        return [
            'average_rating' => round($this->reviewRepository->getAverageRating($restaurantId), 1),
            'total_reviews' => $this->reviewRepository->getReviewCount($restaurantId),
            'rating_distribution' => $this->reviewRepository->getRatingDistribution($restaurantId),
        ];
    }

    /**
     * Check if device can review (rate limiting: 3 reviews per day per restaurant).
     */
    public function canDeviceReview(string $deviceId, string $ipAddress, int $restaurantId): array
    {
        $today = now()->startOfDay();

        // Count reviews from this device and IP in the last 24 hours
        $reviewCount = Review::where('device_id', $deviceId)
            ->where('restaurant_id', $restaurantId)
            ->where('created_at', '>=', $today)
            ->count();

        $ipReviewCount = Review::where('ip_address', $ipAddress)
            ->where('restaurant_id', $restaurantId)
            ->where('created_at', '>=', $today)
            ->count();

        // Maximum 3 reviews per day per restaurant
        $maxReviews = 3;
        $canReview = $reviewCount < $maxReviews && $ipReviewCount < $maxReviews;

        return [
            'can_review' => $canReview,
            'reviews_count' => $reviewCount,
            'ip_reviews_count' => $ipReviewCount,
            'remaining' => $canReview ? ($maxReviews - max($reviewCount, $ipReviewCount)) : 0,
            'reset_at' => now()->endOfDay()->toIso8601String(),
        ];
    }

    /**
     * Get reviews for admin's restaurants
     */
    public function getReviewsForAdmin(\App\Models\User $user, int $perPage = 15, ?int $rating = null): LengthAwarePaginator
    {
        $restaurantIds = $user->restaurants->pluck('id')->toArray();
        return $this->reviewRepository->getPaginatedByRestaurantIds($restaurantIds, $perPage, $rating);
    }

    /**
     * Get all reviews (for superadmin)
     */
    public function getAllReviews(int $perPage = 15, ?int $rating = null, ?int $restaurantId = null): LengthAwarePaginator
    {
        return $this->reviewRepository->getAllPaginated($perPage, $rating, $restaurantId);
    }

    /**
     * Get review statistics for admin's restaurants
     */
    public function getStatisticsForAdmin(\App\Models\User $user): array
    {
        $restaurantIds = $user->restaurants->pluck('id')->toArray();
        return $this->reviewRepository->getStatisticsByRestaurantIds($restaurantIds);
    }

    /**
     * Get all statistics (for superadmin)
     */
    public function getAllStatistics(): array
    {
        return $this->reviewRepository->getAllStatistics();
    }

    /**
     * Find review by ID
     */
    public function findById(int $id): ?Review
    {
        return $this->reviewRepository->findById($id);
    }
}
