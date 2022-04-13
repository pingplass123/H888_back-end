<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Owner;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->username = 'pingplass1';
        $user->password = Hash::make('670qeycc');
        $user->save();
        
        $owner = new Owner();
        $owner->idUser = $user->idUser;
        $owner->save();
    }
}
