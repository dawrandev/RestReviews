<?php

namespace App\Repositories;

use App\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ReviewRepository
{
    /**
     * Get all reviews for a restaurant with pagination.
     */
    public function getByRestaurantId(int $restaurantId, int $perPage = 15): LengthAwarePaginator
    {
        return Review::where('restaurant_id', $restaurantId)
            ->with(['client:id,first_name,last_name,image_path'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get all reviews by a client.
     */
    public function getByClientId(int $clientId, int $perPage = 15): LengthAwarePaginator
    {
        return Review::where('client_id', $clientId)
            ->with(['restaurant:id,branch_name'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Find review by ID.
     */
    public function findById(int $id): ?Review
    {
        return Review::with(['client', 'restaurant'])->find($id);
    }

    /**
     * Find review by client and restaurant.
     */
    public function findByClientAndRestaurant(int $clientId, int $restaurantId): ?Review
    {
        return Review::where('client_id', $clientId)
            ->where('restaurant_id', $restaurantId)
            ->first();
    }

    /**
     * Create a new review.
     */
    public function create(array $data): Review
    {
        return Review::create($data);
    }

    /**
     * Update an existing review.
     */
    public function update(Review $review, array $data): Review
    {
        $review->update($data);
        return $review->fresh(['client', 'restaurant']);
    }

    /**
     * Delete a review.
     */
    public function delete(Review $review): bool
    {
        return $review->delete();
    }

    /**
     * Get average rating for a restaurant.
     */
    public function getAverageRating(int $restaurantId): float
    {
        return Review::where('restaurant_id', $restaurantId)->avg('rating') ?? 0;
    }

    /**
     * Get review count for a restaurant.
     */
    public function getReviewCount(int $restaurantId): int
    {
        return Review::where('restaurant_id', $restaurantId)->count();
    }

    /**
     * Get rating distribution for a restaurant.
     */
    public function getRatingDistribution(int $restaurantId): array
    {
        $distribution = Review::where('restaurant_id', $restaurantId)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        return [
            1 => $distribution[1] ?? 0,
            2 => $distribution[2] ?? 0,
            3 => $distribution[3] ?? 0,
            4 => $distribution[4] ?? 0,
            5 => $distribution[5] ?? 0,
        ];
    }

    /**
     * Get paginated reviews for multiple restaurants (for admin)
     */
    public function getPaginatedByRestaurantIds(array $restaurantIds, int $perPage = 15, ?int $rating = null): LengthAwarePaginator
    {
        $query = Review::whereIn('restaurant_id', $restaurantIds)
            ->with(['client:id,first_name,last_name,image_path', 'restaurant:id,branch_name']);

        if ($rating) {
            $query->where('rating', $rating);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get all reviews paginated (for superadmin)
     */
    public function getAllPaginated(int $perPage = 15, ?int $rating = null, ?int $restaurantId = null): LengthAwarePaginator
    {
        $query = Review::with(['client:id,first_name,last_name,image_path', 'restaurant:id,branch_name']);

        if ($rating) {
            $query->where('rating', $rating);
        }

        if ($restaurantId) {
            $query->where('restaurant_id', $restaurantId);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get review statistics for multiple restaurants
     */
    public function getStatisticsByRestaurantIds(array $restaurantIds): array
    {
        $stats = Review::whereIn('restaurant_id', $restaurantIds)
            ->selectRaw('
                COUNT(*) as total_reviews,
                AVG(rating) as average_rating,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
            ')
            ->first();

        return [
            'total_reviews' => $stats->total_reviews ?? 0,
            'average_rating' => round($stats->average_rating ?? 0, 1),
            'five_star' => $stats->five_star ?? 0,
            'four_star' => $stats->four_star ?? 0,
            'three_star' => $stats->three_star ?? 0,
            'two_star' => $stats->two_star ?? 0,
            'one_star' => $stats->one_star ?? 0,
        ];
    }

    /**
     * Get all statistics (for superadmin)
     */
    public function getAllStatistics(): array
    {
        $stats = Review::selectRaw('
                COUNT(*) as total_reviews,
                AVG(rating) as average_rating,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
            ')
            ->first();

        return [
            'total_reviews' => $stats->total_reviews ?? 0,
            'average_rating' => round($stats->average_rating ?? 0, 1),
            'five_star' => $stats->five_star ?? 0,
            'four_star' => $stats->four_star ?? 0,
            'three_star' => $stats->three_star ?? 0,
            'two_star' => $stats->two_star ?? 0,
            'one_star' => $stats->one_star ?? 0,
        ];
    }
}
