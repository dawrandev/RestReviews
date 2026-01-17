<?php

namespace App\Permissions;

class RestaurantPermissions
{
    public const VIEW_ANY = 'view_any_restaurant';
    public const VIEW = 'view_restaurant';
    public const CREATE = 'create_restaurant';
    public const UPDATE = 'update_restaurant';
    public const DELETE = 'delete_restaurant';
    public const EDIT = 'edit_restaurant';
    public const SEARCH = 'search_restaurant';

    public static function all(): array
    {
        return [
            self::VIEW_ANY,
            self::VIEW,
            self::CREATE,
            self::UPDATE,
            self::DELETE,
            self::EDIT,
            self::SEARCH,
        ];
    }
}
