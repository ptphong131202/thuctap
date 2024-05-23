<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = \App\Enums\Permission::getPermisstion();
        foreach ($permissions as $key => $name) {
            if (!Permission::wherePermissionId($key)->exists()) {
                Permission::insert(
                    [
                        'permission_id' => $key,
                        'name' => $name
                    ]
                );
            }
        }
    }
}
