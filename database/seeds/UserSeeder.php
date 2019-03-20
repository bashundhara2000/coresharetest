<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;

class UserSeeder extends Seeder{

    public function run(){
        DB::table('users')->delete();

        $adminRole = Role::whereName('administrator')->first();

        $user = User::create(array(
            'first_name'    => 'Dummy',
            'last_name'     => 'User',
            'email'         => 'marirajas92+1@gmail.com',
            'password'      => Hash::make('Ace12345'),
            'token'         => str_random(64),
            'activated'     => true
        ));
        $user->assignRole($adminRole);
    }
}
