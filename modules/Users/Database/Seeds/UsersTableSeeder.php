<?php

namespace Modules\Users\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;
use Nova\Support\Facades\Hash;
use Nova\Support\Str;

use Modules\Users\Models\User;


class UsersTableSeeder extends Seeder
{
    protected $tokens = array();


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the table before seeding.
        User::truncate();

        //
        $user = User::create(array(
            'id'             => 1,
            'username'       => 'admin',
            'password'       => Hash::make('admin'),
            'email'          => 'admin@novaframework.dev',
            'first_name'     => 'Site',
            'last_name'      => 'Administrator',
            'picture'        => null,
            'remember_token' => '',
            'api_token'      => $this->uniqueToken(),
        ));

        $user->roles()->attach(array(1));

        $user->createMeta(array(
            'location'        => 'Craiova, Romania',
            'activated'       => 1,
            'activation_code' => null,
        ));

        //
        $user = User::create(array(
            'id'             => 2,
            'username'       => 'marcus',
            'password'       => Hash::make('marcus'),
            'email'          => 'marcus@novaframework.dev',
            'first_name'     => 'Marcus',
            'last_name'      => 'Spears',
            'picture'        => null,
            'remember_token' => '',
            'api_token'      => $this->uniqueToken(),
        ));

        $user->roles()->attach(array(2));

        $user->createMeta(array(
            'location'        => 'London, UK',
            'activated'       => 1,
            'activation_code' => null,
        ));

        //
        $user = User::create(array(
            'id'             => 3,
            'username'       => 'michael',
            'password'       => Hash::make('michael'),
            'email'          => 'michael@novaframework.dev',
            'first_name'     => 'Michael',
            'last_name'      => 'White',
            'picture'        => null,
            'remember_token' => '',
            'api_token'      => $this->uniqueToken(),
        ));

        $user->roles()->attach(array(3));

        $user->createMeta(array(
            'location'        => 'Rome, Italy',
            'activated'       => 1,
            'activation_code' => null,
        ));

        //
        $user = User::create(array(
            'id'             => 4,
            'username'       => 'john',
            'password'       => Hash::make('john'),
            'email'          => 'john@novaframework.dev',
            'first_name'     => 'John',
            'last_name'      => 'Kennedy',
            'picture'        => null,
            'remember_token' => '',
            'api_token'      => $this->uniqueToken(),
        ));

        $user->roles()->attach(array(4));

        $user->createMeta(array(
            'location'        => 'Moscow, Russia',
            'activated'       => 1,
            'activation_code' => null,
        ));

        //
        $user = User::create(array(
            'id'             => 5,
            'username'       => 'mark',
            'password'       => Hash::make('mark'),
            'email'          => 'mark@novaframework.dev',
            'first_name'     => 'Mark',
            'last_name'      => 'Black',
            'picture'        => null,
            'remember_token' => '',
            'api_token'      => $this->uniqueToken(),
        ));

        $user->roles()->attach(array(4));

        $user->createMeta(array(
            'location'        => 'Paris, France',
            'activated'       => 1,
            'activation_code' => null,
        ));
    }

    protected function uniqueToken($length = 60)
    {
        while (true) {
            $token = Str::random($length);

            if (! in_array($token, $this->tokens)) {
                array_push($this->tokens, $token);

                return $token;
            }
        }
    }
}
