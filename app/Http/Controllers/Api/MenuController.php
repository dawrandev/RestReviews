<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuItemResource;
use App\Http\Resources\MenuSectionResource;
use App\Services\MenuService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class MenuController extends Controller
{
    public function __construct(
        protected MenuService $menuService
    ) {}

    #[OA\Get(
        path: '/api/restaurants/{id}/menu',
        summary: 'Get restaurant menu',
        tags: ['Menu'],
        parameters: [
            new OA\Parameter(
                name: 'Accept-Language',
                in: 'header',
                required: false,
                description: 'Til kodi (uz, ru, kk, en). Default: kk',
                schema: new OA\Schema(type: 'string', enum: ['uz', 'ru', 'kk', 'en'], default: 'kk')
            ),
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Restaurant menu',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object')),
                    ]
                )
            )
        ]
    )]
    public function getRestaurantMenu(int $restaurantId): JsonResponse
    {
        $menu = $this->menuService->getRestaurantMenu($restaurantId);

        return response()->json([
            'success' => true,
            'data' => MenuSectionResource::collection($menu),
        ]);
    }

    #[OA\Get(
        path: '/api/menu-items/{id}',
        summary: 'Get menu item details',
        tags: ['Menu'],
        parameters: [
            new OA\Parameter(
                name: 'Accept-Language',
                in: 'header',
                required: false,
                description: 'Til kodi (uz, ru, kk, en). Default: kk',
                schema: new OA\Schema(type: 'string', enum: ['uz', 'ru', 'kk', 'en'], default: 'kk')
            ),
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Menu item details',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'object'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Menu item not found'
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $menuItem = $this->menuService->getMenuItemById($id);

        if (!$menuItem) {
            return response()->json([
                'success' => false,
                'message' => 'Taom topilmadi',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new MenuItemResource($menuItem),
        ]);
    }
}
