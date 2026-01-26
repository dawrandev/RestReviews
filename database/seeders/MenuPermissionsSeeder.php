<?php

namespace Database\Seeders;

use App\Permissions\MenuSectionPermissions;
use App\Permissions\MenuItemPermissions;
use App\Permissions\RestaurantMenuItemPermissions;
use App\Permissions\ReviewPermissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MenuPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Create MenuSection permissions
        foreach (MenuSectionPermissions::all() as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create MenuItem permissions
        foreach (MenuItemPermissions::all() as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create RestaurantMenuItem permissions
        foreach (RestaurantMenuItemPermissions::all() as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Review permissions
        foreach (ReviewPermissions::all() as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::where('name', 'admin')->first();
        $superadminRole = Role::where('name', 'superadmin')->first();

        if ($adminRole) {
            $adminRole->givePermissionTo([
                // MenuSection
                MenuSectionPermissions::VIEW_ANY,
                MenuSectionPermissions::VIEW,
                MenuSectionPermissions::CREATE,
                MenuSectionPermissions::UPDATE,
                MenuSectionPermissions::DELETE,

                MenuItemPermissions::VIEW_ANY,
                MenuItemPermissions::VIEW,
                MenuItemPermissions::CREATE,
                MenuItemPermissions::UPDATE,
                MenuItemPermissions::DELETE,

                RestaurantMenuItemPermissions::VIEW_ANY,
                RestaurantMenuItemPermissions::VIEW,
                RestaurantMenuItemPermissions::CREATE,
                RestaurantMenuItemPermissions::UPDATE,
                RestaurantMenuItemPermissions::DELETE,

                ReviewPermissions::VIEW_ANY,
                ReviewPermissions::VIEW,
                ReviewPermissions::DELETE,
            ]);
        }

        if ($superadminRole) {
            $superadminRole->givePermissionTo([
                MenuSectionPermissions::VIEW_ANY,
                MenuSectionPermissions::VIEW,
                MenuItemPermissions::VIEW_ANY,
                MenuItemPermissions::VIEW,
                RestaurantMenuItemPermissions::VIEW_ANY,
                RestaurantMenuItemPermissions::VIEW,

                ReviewPermissions::VIEW_ANY,
                ReviewPermissions::VIEW,
                ReviewPermissions::DELETE,
            ]);
        }

        $this->command->info('Menu permissions created and assigned successfully!');
    }
}
