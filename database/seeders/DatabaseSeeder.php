<?php

namespace Database\Seeders;

use App\Models\branchesModel;
use App\Models\cabangModel;
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

        $this->call(ProdectStockSeeder::class);

        roleModels::create(['role' => 'admin']);
        roleModels::create(['role' => 'kasir']);
        roleModels::create(['role' => 'gudang']);
        roleModels::create(['role' => 'manajer']);
        roleModels::create(['role' => 'owner']);
        roleModels::create(['role' => 'Super Admin']);


        // User

        $idB = cabangModel::where('name', 'Cabang Utama Surakarta')->first();
        User::create([
            'name' => 'Admin Super',
            // 'email' => 'admin@admin.com',
            'username' => 'Sadmin',
            'password' => bcrypt('password'),
            'role_id' => 6,
            'branch_id' => $idB->id,
        ]);
        User::create([
            'name' => 'Admin',
            // 'email' => 'admin@admin.com',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'role_id' => 1,
            'branch_id' => $idB->id,
        ]);

        User::create([
            'name' => 'Kasir',
            // 'email' => 'kasir@kasir.com',
            'username' => 'kasir',
            'password' => bcrypt('password'),
            'role_id' => 2,
            'branch_id' => $idB->id,
        ]);
        User::create([
            'name' => 'Gudang',
            // 'email' => 'gudang@gudang.com',
            'username' => 'gudang',
            'password' => bcrypt('password'),
            'role_id' => 3,
            'branch_id' =>  $idB->id,
        ]);
        User::create([
            'name' => 'Manajer',
            // 'email' => 'manajer@manajer.com',
            'username' => 'manajer',
            'password' => bcrypt('password'),
            'role_id' => 4,
            'branch_id' =>  $idB->id,
        ]);
        User::create([
            'name' => 'Owner',
            // 'email' => 'owner@owner.com',
            'username' => 'owner',
            'password' => bcrypt('password'),
            'role_id' => 5,
            'branch_id' =>  $idB->id,
        ]);

         $this->call(JualSeeder::class);
    }
}
