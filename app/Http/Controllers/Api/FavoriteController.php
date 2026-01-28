<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FavoriteResource;
use App\Services\FavoriteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class FavoriteController extends Controller
{
    public function __construct(
        protected FavoriteService $favoriteService
    ) {}

    #[OA\Get(
        path: '/api/favorites',
        summary: 'Get user favorites',
        security: [['sanctum' => []]],
        tags: ['Favorites'],
        parameters: [
            new OA\Parameter(
                name: 'Accept-Language',
                in: 'header',
                required: false,
                description: 'Til kodi (uz, ru, kk, en). Default: kk',
                schema: new OA\Schema(type: 'string', enum: ['uz', 'ru', 'kk', 'en'], default: 'kk')
            ),
            new OA\Parameter(
                name: 'page',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'per_page',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Favorites list',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object')),
                        new OA\Property(property: 'meta', type: 'object'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated'
            )
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $client = $request->user();
        $perPage = $request->input('per_page', 15);

        $favorites = $this->favoriteService->getClientFavorites($client->id, $perPage);

        return response()->json([
            'success' => true,
            'data' => FavoriteResource::collection($favorites),
            'meta' => [
                'current_page' => $favorites->currentPage(),
                'last_page' => $favorites->lastPage(),
                'per_page' => $favorites->perPage(),
                'total' => $favorites->total(),
            ],
        ]);
    }

    #[OA\Post(
        path: '/api/restaurants/{id}/favorite',
        summary: 'Toggle favorite status (guest-friendly)',
        tags: ['Favorites'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['device_id'],
                properties: [
                    new OA\Property(
                        property: 'device_id',
                        type: 'string',
                        description: 'Qurilma ID (UUID yoki fingerprint)',
                        maxLength: 100,
                        example: '550e8400-e29b-41d4-a716-446655440000'
                    )
                ]
            )
        ),
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Favorite toggled',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'is_favorited', type: 'boolean')
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Restaurant not found'
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error'
            )
        ]
    )]
    public function toggle(Request $request, int $restaurantId): JsonResponse
    {
        // Validate device_id
        $request->validate([
            'device_id' => ['required', 'string', 'max:100'],
        ], [
            'device_id.required' => 'Qurilma identifikatori majburiy.',
            'device_id.string' => 'Qurilma identifikatori to\'g\'ri formatda emas.',
            'device_id.max' => 'Qurilma identifikatori juda uzun.',
        ]);

        $restaurant = \App\Models\Restaurant::find($restaurantId);
        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'Restoran topilmadi',
            ], 404);
        }

        // Get authenticated client if exists (nullable for guest support)
        $clientId = $request->user()?->id;
        $deviceId = $request->input('device_id');
        $ipAddress = $request->ip();

        $result = $this->favoriteService->toggleFavorite($clientId, $restaurantId, $deviceId, $ipAddress);

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => [
                'is_favorited' => $result['is_favorited'],
            ],
        ]);
    }
}
