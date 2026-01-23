<?php

namespace App\Permissions;

class MenuPermissions
{
    public const VIEW_ANY_MENU = 'view_any_menu';
    public const VIEW_MENU_DETAILS = 'view_menu_details';
    public const CREATE_MENU = 'create_menu';
    public const EDIT_MENU = 'edit_menu';
    public const DELETE_MENU = 'delete_menu';

    public static function all(): array
    {
        return [
            self::VIEW_ANY_MENU,
            self::VIEW_MENU_DETAILS,
            self::CREATE_MENU,
            self::EDIT_MENU,
            self::DELETE_MENU,
        ];
    }
}
