<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = config('core.super_admin');
        if (! DB::table('users')->where('user_id', $user['user_id'])->exists()) {
            $user['password'] = bcrypt($user['password']);
            $user['type'] = 1; // can bo

            $canbo = $user['canbo'];
            $canbo['user_id'] = $user['user_id'];
            unset($user['canbo']);
            DB::table('users')->insert($user);
            DB::table('qlsv_canbo')->insert($canbo);

            $permissions = \App\Enums\Permission::getPermisstion();
            foreach ($permissions as $key => $name) {
                if ($key != \App\Enums\Permission::STUDENT) {
                    DB::table('users_permissions')->insert([
                        'user_id' => $user['user_id'],
                        'permission_id' => $key
                    ]);
                }
            }
        }
    }
}
