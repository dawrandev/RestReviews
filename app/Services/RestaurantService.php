<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\User;
use App\Repositories\RestaurantRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class RestaurantService
{
    public function __construct(
        protected RestaurantRepository $restaurantRepository,
        protected QrCodeService $qrCodeService
    ) {}

    public function getRestaurantForUser(User $user, int $perPage = 10): LengthAwarePaginator
    {
        if ($user->hasRole('superadmin')) {
            return $this->restaurantRepository->getAll($perPage);
        }

        return $this->restaurantRepository->getByUserId($user->id, $perPage);
    }

    public function findRestaurant(int $id): ?Restaurant
    {
        return $this->restaurantRepository->findById($id);
    }

    public function createRestaurant(array $data, User $user): Restaurant
    {
        $data['user_id'] = $user->id;

        // Upload images
        if (isset($data['images'])) {
            $uploadedImages = [];
            foreach ($data['images'] as $image) {
                $uploadedImages[] = $this->uploadImage($image);
            }
            $data['images'] = $uploadedImages;
        }

        $restaurant = $this->restaurantRepository->create($data);

        // Generate QR Code after restaurant is created
        $this->generateRestaurantQrCode($restaurant);

        return $restaurant->fresh();
    }

    public function updateRestaurant(Restaurant $restaurant, array $data): Restaurant
    {
        // Upload new images
        if (isset($data['new_images'])) {
            $uploadedImages = [];
            foreach ($data['new_images'] as $image) {
                $uploadedImages[] = $this->uploadImage($image);
            }
            $data['new_images'] = $uploadedImages;
        }

        return $this->restaurantRepository->update($restaurant, $data);
    }

    public function deleteRestaurant(Restaurant $restaurant): bool
    {
        // Delete all images from storage
        foreach ($restaurant->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        // Delete QR code
        if ($restaurant->qr_code) {
            $this->qrCodeService->deleteQrCode($restaurant->qr_code);
        }

        return $this->restaurantRepository->delete($restaurant);
    }

    public function deleteImage(int $imageId): bool
    {
        return $this->restaurantRepository->deleteImage($imageId);
    }

    private function uploadImage($file): string
    {
        return $file->store('restaurants', 'public');
    }

    public function canUserCreateRestaurant(User $user): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->hasRole('admin')) {
            return $user->restaurants->isEmpty(); // Admin can create only if doesn't have restaurant
        }

        return false;
    }

    protected function generateRestaurantQrCode(Restaurant $restaurant): void
    {
        // Generate URL for restaurant show page
        $url = route('restaurants.show', $restaurant->id);

        // Generate QR code filename
        $filename = 'restaurant_' . $restaurant->id . '_' . time();

        // Generate and save QR code
        $qrCodePath = $this->qrCodeService->generateQrCode($url, $filename);

        // Update restaurant with QR code path
        $restaurant->update(['qr_code' => $qrCodePath]);
    }
}
