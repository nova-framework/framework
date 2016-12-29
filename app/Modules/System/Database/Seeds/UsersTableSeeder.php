<?php

namespace App\Modules\System\Database\Seeds;

use Nova\Database\Seeder;
use Nova\Database\ORM\Model;
use App\Models\User;
use Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'id'              => 1,
            'role_id'         => 1,
            'username'        => 'admin',
            'password'        => Hash::make('admin'),
            'realname'        => 'Administrator',
            'email'           => 'admin@novaframework.dev',
            'active'          => 1,
            'activation_code' => '',
            'remember_token'  => '',
        ]);
        User::create([
            'id'              => 2,
            'role_id'         => 2,
            'username'        => 'marcus',
            'password'        => Hash::make('marcus'),
            'realname'        => 'Marcus Spears',
            'email'           => 'marcus@novaframework.dev',
            'active'          => 1,
            'activation_code' => '',
            'remember_token'  => '',
        ]);
        User::create([
            'id'              => 3,
            'role_id'         => 3,
            'username'        => 'michael',
            'password'        => Hash::make('michael'),
            'realname'        => 'Michael White',
            'email'           => 'michael@novaframework.dev',
            'active'          => 1,
            'activation_code' => '',
            'remember_token'  => '',
        ]);
        User::create([
            'id'              => 4,
            'role_id'         => 5,
            'username'        => 'john',
            'password'        => Hash::make('john'),
            'realname'        => 'John Kennedy',
            'email'           => 'john@novaframework.dev',
            'active'          => 1,
            'activation_code' => '',
            'remember_token'  => '',
        ]);
        User::create([
            'id'              => 5,
            'role_id'         => 5,
            'username'        => 'mark',
            'password'        => Hash::make('mark'),
            'realname'        => 'Mark Black',
            'email'           => 'mark@novaframework.dev',
            'active'          => 1,
            'activation_code' => '',
            'remember_token'  => '',
        ]);
    }
}
