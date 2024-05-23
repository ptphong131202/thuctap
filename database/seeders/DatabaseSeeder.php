<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(UsersTableSeeder::class);
        //$this->call(PermissionsTableSeeder::class);
        $this->call(HeDaoTaoSeeder::class);
        $this->call(KhoaDaoTaoSeeder::class);
        $this->call(NienkhoaSeeder::class);
        $this->call(LopHocSeeder::class);
        \App\Models\MonHoc::factory(10)->create();
        $this->call(SinhVienSeeder::class);
    }
}
