<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'pingplass1',
            'password' => Hash::make('670qeycc'),
        ]);


        DB::table('users')->insert([
            'id' => 2,
            'name' => 'pingplass2',
            'password' => Hash::make('670qeycc'),
        ]);


        DB::table('users')->insert([
            'id' => 3,
            'name' => 'pingplass3',
            'password' => Hash::make('670qeycc'),
        ]);

        DB::table('owner')->insert([
            'idOwner' => 1,
            'idUser' => 1,
        ]);

        DB::table('admins')->insert([
            'idAdmin' => 1,
            'idUser' => 2,
            'name' => 'ping',
        ]);

        DB::table('customers')->insert([
            'idCustomer' => 1,
            'idUser' => 3,
            'name' => 'pingCustomers',
            'created_by' => 2,
        ]);

    }
}
