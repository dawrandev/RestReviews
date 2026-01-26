<?php

namespace App\Permissions;

class ReviewPermissions
{
    public const VIEW_ANY = 'view_any_review';
    public const VIEW = 'view_review';
    public const DELETE = 'delete_review';

    public static function all(): array
    {
        return [
            self::VIEW_ANY,
            self::VIEW,
            self::DELETE,
        ];
    }
}
