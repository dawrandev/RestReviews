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
}
