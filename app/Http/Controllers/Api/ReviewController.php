<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ReviewController extends Controller
{
    public function __construct(
        protected ReviewService $reviewService
    ) {}

    #[OA\Get(
        path: '/api/restaurants/{id}/reviews',
        summary: 'Get restaurant reviews',
        tags: ['Reviews'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Restaurant reviews list',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object')),
                        new OA\Property(property: 'statistics', type: 'object'),
                        new OA\Property(property: 'meta', type: 'object'),
                    ]
                )
            )
        ]
    )]
    public function index(Request $request, int $restaurantId): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $reviews = $this->reviewService->getRestaurantReviews($restaurantId, $perPage);

        // Get statistics
        $stats = $this->reviewService->getRestaurantReviewStats($restaurantId);

        return response()->json([
            'success' => true,
            'data' => ReviewResource::collection($reviews),
            'statistics' => $stats,
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }

    #[OA\Post(
        path: '/api/restaurants/{id}/reviews',
        summary: 'Create or update a review (guest-friendly)',
        tags: ['Reviews'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['device_id', 'rating'],
                properties: [
                    new OA\Property(
                        property: 'device_id',
                        type: 'string',
                        description: 'Qurilma ID (UUID yoki fingerprint)',
                        maxLength: 100,
                        example: '550e8400-e29b-41d4-a716-446655440000'
                    ),
                    new OA\Property(
                        property: 'rating',
                        type: 'integer',
                        description: 'Reyting (1 dan 5 gacha)',
                        minimum: 1,
                        maximum: 5,
                        example: 5
                    ),
                    new OA\Property(
                        property: 'comment',
                        type: 'string',
                        description: 'Izoh (ixtiyoriy, maksimum 1000 belgi)',
                        maxLength: 1000,
                        nullable: true,
                        example: 'Juda zo\'r restoran, taomlar mazali!'
                    )
                ]
            )
        ),
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Review created/updated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', type: 'object'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error'
            ),
            new OA\Response(
                response: 429,
                description: 'Rate limit exceeded (3 reviews per day per restaurant)',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Siz bugun bu restoranga maksimal miqdorda sharh qoldirdingiz. Ertaga qayta urinib ko\'ring.'),
                        new OA\Property(property: 'rate_limit', type: 'object', properties: [
                            new OA\Property(property: 'reviews_count', type: 'integer'),
                            new OA\Property(property: 'remaining', type: 'integer'),
                            new OA\Property(property: 'reset_at', type: 'string'),
                        ]),
                    ]
                )
            )
        ]
    )]
    public function store(StoreReviewRequest $request, int $restaurantId): JsonResponse
    {
        $validated = $request->validated();
        $deviceId = $validated['device_id'];
        $ipAddress = $request->ip();

        // Check rate limiting
        $rateLimitCheck = $this->reviewService->canDeviceReview($deviceId, $ipAddress, $restaurantId);

        if (!$rateLimitCheck['can_review']) {
            return response()->json([
                'success' => false,
                'message' => 'Siz bugun bu restoranga maksimal miqdorda sharh qoldirdingiz. Ertaga qayta urinib ko\'ring.',
                'rate_limit' => $rateLimitCheck,
            ], 429);
        }

        // Get authenticated client if exists (nullable for guest support)
        $clientId = $request->user()?->id;

        $review = $this->reviewService->createOrUpdateReview(
            $clientId,
            $restaurantId,
            [
                ...$validated,
                'ip_address' => $ipAddress,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Fikr-mulohaza muvaffaqiyatli saqlandi',
            'data' => new ReviewResource($review),
            'rate_limit' => [
                'remaining' => $rateLimitCheck['remaining'] - 1,
                'reset_at' => $rateLimitCheck['reset_at'],
            ],
        ], 201);
    }

    #[OA\Put(
        path: '/api/reviews/{id}',
        summary: 'Update a review',
        security: [['sanctum' => []]],
        tags: ['Reviews'],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'rating',
                        type: 'integer',
                        description: 'Reyting (1 dan 5 gacha)',
                        minimum: 1,
                        maximum: 5,
                        example: 4,
                        nullable: true
                    ),
                    new OA\Property(
                        property: 'comment',
                        type: 'string',
                        description: 'Izoh (ixtiyoriy, maksimum 1000 belgi)',
                        maxLength: 1000,
                        nullable: true,
                        example: 'Yangilangan izoh'
                    )
                ]
            )
        ),
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Review updated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', type: 'object'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated'
            ),
            new OA\Response(
                response: 403,
                description: 'Forbidden'
            ),
            new OA\Response(
                response: 404,
                description: 'Review not found'
            )
        ]
    )]
    public function update(UpdateReviewRequest $request, int $id): JsonResponse
    {
        $client = $request->user();
        $review = \App\Models\Review::find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Sharh topilmadi',
            ], 404);
        }

        if ($review->client_id !== $client->id) {
            return response()->json([
                'success' => false,
                'message' => 'Sizda bu sharhni tahrirlash huquqi yo\'q',
            ], 403);
        }

        $review = $this->reviewService->updateReview($review, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Sharh muvaffaqiyatli yangilandi',
            'data' => new ReviewResource($review),
        ]);
    }

    #[OA\Delete(
        path: '/api/reviews/{id}',
        summary: 'Delete a review',
        security: [['sanctum' => []]],
        tags: ['Reviews'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Review deleted',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated'
            ),
            new OA\Response(
                response: 403,
                description: 'Forbidden'
            ),
            new OA\Response(
                response: 404,
                description: 'Review not found'
            )
        ]
    )]
    public function destroy(Request $request, int $id): JsonResponse
    {
        $client = $request->user();
        $review = \App\Models\Review::find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Sharh topilmadi',
            ], 404);
        }

        if ($review->client_id !== $client->id) {
            return response()->json([
                'success' => false,
                'message' => 'Sizda bu sharhni o\'chirish huquqi yo\'q',
            ], 403);
        }

        $this->reviewService->deleteReview($review);

        return response()->json([
            'success' => true,
            'message' => 'Sharh muvaffaqiyatli o\'chirildi',
        ]);
    }
}
