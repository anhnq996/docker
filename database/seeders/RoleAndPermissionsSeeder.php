<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionsSeeder extends Seeder
{
    public static array $role2Permits = [
        'administrator' => [
            //games
            'games'
        ],
    ];

    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create normal permissions
        foreach ($this->allPermitsNormalization() as $permit) {
            Permission::firstOrCreate(['guard_name' => 'web', 'name' => $permit]);
        }

        // create roles and assign created permissions
        foreach (static::$role2Permits as $roleName => $permits) {
            /* @var Role $role */
            $role = Role::firstOrCreate(['guard_name' => 'web', 'name' => $roleName]);
            $role->givePermissionTo($permits);
        }
    }

    /**
     * @return array
     */
    private function allPermitsNormalization(): array
    {
        return collect(static::$role2Permits)
            ->collapse()->unique()
            ->values()->all();
    }
}
