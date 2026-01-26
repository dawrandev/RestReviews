<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuItemResource;
use App\Http\Resources\RestaurantListResource;
use App\Services\DiscoveryService;
use App\Services\MenuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class SearchController extends Controller
{
    public function __construct(
        protected DiscoveryService $discoveryService,
        protected MenuService $menuService
    ) {}

    #[OA\Get(
        path: '/api/search',
        summary: 'Global search for restaurants and menu items',
        tags: ['Search'],
        parameters: [
            new OA\Parameter(
                name: 'Accept-Language',
                in: 'header',
                required: false,
                description: 'Til kodi (uz, ru, kk, en). Default: kk',
                schema: new OA\Schema(type: 'string', enum: ['uz', 'ru', 'kk', 'en'], default: 'kk')
            ),
            new OA\Parameter(name: 'q', in: 'query', required: true, schema: new OA\Schema(type: 'string', minLength: 2)),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Search results',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'restaurants', type: 'object'),
                                new OA\Property(property: 'menu_items', type: 'object'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error'
            )
        ]
    )]
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $query = $request->input('q');
        $perPage = $request->input('per_page', 15);

        // Search restaurants
        $searchResults = $this->discoveryService->search($query, $perPage);

        // Search menu items
        $menuItems = $this->menuService->searchMenuItems($query);

        $restaurants = $searchResults['restaurants'];

        return response()->json([
            'success' => true,
            'data' => [
                'restaurants' => [
                    'data' => RestaurantListResource::collection($restaurants),
                    'meta' => [
                        'current_page' => $restaurants->currentPage(),
                        'last_page' => $restaurants->lastPage(),
                        'per_page' => $restaurants->perPage(),
                        'total' => $restaurants->total(),
                    ],
                ],
                'menu_items' => [
                    'data' => MenuItemResource::collection($menuItems),
                    'total' => $menuItems->count(),
                ],
            ],
        ]);
    }
}
