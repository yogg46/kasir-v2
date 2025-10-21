<?php

namespace Database\Seeders;

use App\Models\roleModels;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();



        roleModels::create(['role' => 'admin']);
        roleModels::create(['role' => 'kasir']);
        roleModels::create(['role' => 'gudang']);
        roleModels::create(['role' => 'manajer']);
        roleModels::create(['role' => 'owner']);

        // User

        User::create([
            'name' => 'Admin',
            // 'email' => 'admin@admin.com',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'role_id' => 1,
            'branch_id' => null,
        ]);

        User::create([
            'name' => 'Kasir',
            // 'email' => 'kasir@kasir.com',
            'username' => 'kasir',
            'password' => bcrypt('password'),
            'role_id' => 2,
            'branch_id' => null,
        ]);
        User::create([
            'name' => 'Gudang',
            // 'email' => 'gudang@gudang.com',
            'username' => 'gudang',
            'password' => bcrypt('password'),
            'role_id' => 3,
            'branch_id' => null,
        ]);
        User::create([
            'name' => 'Manajer',
            // 'email' => 'manajer@manajer.com',
            'username' => 'manajer',
            'password' => bcrypt('password'),
            'role_id' => 4,
            'branch_id' => null,
        ]);
        User::create([
            'name' => 'Owner',
            // 'email' => 'owner@owner.com',
            'username' => 'owner',
            'password' => bcrypt('password'),
            'role_id' => 5,
            'branch_id' => null,
        ]);

        $this->call(ProdectStockSeeder::class);
    }
}
