<?php

namespace Backend\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;
use Nova\Support\Facades\Hash;

use Backend\Models\User;

use Faker\Factory as FakerFactory;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the table before seeding.
        User::truncate();

        User::create(array(
            'id'             => 1,
            'role_id'        => 1,
            'username'       => 'admin',
            'password'       => Hash::make('admin'),
            'first_name'     => 'Site',
            'last_name'      => 'Administrator',
            'email'          => 'admin@novaframework.dev',
            'remember_token' => '',
        ));

        User::create(array(
            'id'             => 2,
            'role_id'        => 2,
            'username'       => 'marcus',
            'password'       => Hash::make('marcus'),
            'first_name'     => 'Marcus',
            'last_name'      => 'Spears',
            'email'          => 'marcus@novaframework.dev',
            'remember_token' => '',
        ));

        User::create(array(
            'id'             => 3,
            'role_id'        => 3,
            'username'       => 'michael',
            'password'       => Hash::make('michael'),
            'first_name'     => 'Michael',
            'last_name'      => 'White',
            'email'          => 'michael@novaframework.dev',
            'remember_token' => '',
        ));

        User::create(array(
            'id'             => 4,
            'role_id'        => 5,
            'username'       => 'john',
            'password'       => Hash::make('john'),
            'first_name'     => 'John',
            'last_name'      => 'Kennedy',
            'email'          => 'john@novaframework.dev',
            'remember_token' => '',
        ));

        User::create(array(
            'id'             => 5,
            'role_id'        => 5,
            'username'       => 'mark',
            'password'       => Hash::make('mark'),
            'first_name'     => 'Mark',
            'last_name'      => 'Black',
            'email'          => 'mark@novaframework.dev',
            'remember_token' => '',
        ));

        //------------------------------------------------------------------------------
        // Mock Data
        //------------------------------------------------------------------------------

        $faker = FakerFactory::create('en_US');

        //
        for($i = 0; $i < 238; $i++) {
            $username = $faker->unique()->userName;

            User::create(array(
                'role_id'        => 5,
                'username'       => $username,
                'password'       => Hash::make($username),
                'first_name'     => $faker->firstName,
                'last_name'      => $faker->lastName,
                'email'          => $faker->unique()->email,
                'remember_token' => '',
            ));
        }
    }
}
