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
     * Create or update a review.
     */
    public function createOrUpdateReview(int $clientId, int $restaurantId, array $data): Review
    {
        $existingReview = $this->reviewRepository->findByClientAndRestaurant($clientId, $restaurantId);

        if ($existingReview) {
            return $this->reviewRepository->update($existingReview, $data);
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
     * Check if client can review a restaurant.
     */
    public function canClientReview(int $clientId, int $restaurantId): bool
    {
        // For now, any authenticated client can review
        // You can add additional logic here (e.g., check if they've visited)
        return true;
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
    public function getAllReviews(int $perPage = 15, ?int $rating = null): LengthAwarePaginator
    {
        return $this->reviewRepository->getAllPaginated($perPage, $rating);
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
