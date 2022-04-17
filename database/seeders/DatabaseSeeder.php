<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::collection('roles')->delete();

        DB::collection('roles')->insert(['nom' => 'Encadrant']);
        DB::collection('roles')->insert(['nom' => 'ChefDepartement']);
        DB::collection('roles')->insert(['nom' => 'ServiceFormation']);
        DB::collection('roles')->insert(['nom' => 'Coordinateur']);
    }
}
