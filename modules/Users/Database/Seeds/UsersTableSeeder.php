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
            'id'              => 1,
            'username'        => 'admin',
            'password'        => Hash::make('admin'),
            'email'           => 'admin@novaframework.dev',
            'realname'        => 'Site Administrator',
            'image'           => null,
            'remember_token'  => '',
            'api_token'       => $this->uniqueToken(),
            'activated'       => 1,
            'activation_code' => null,
        ));

        $user->roles()->attach(array(1));

        //
        $user = User::create(array(
            'id'              => 2,
            'username'        => 'marcus',
            'password'        => Hash::make('marcus'),
            'email'           => 'marcus@novaframework.dev',
            'realname'        => 'Marcus Spears',
            'image'           => null,
            'remember_token'  => '',
            'api_token'       => $this->uniqueToken(),
            'activated'       => 1,
            'activation_code' => null,
        ));

        $user->roles()->attach(array(2));

        //
        $user = User::create(array(
            'id'              => 3,
            'username'        => 'michael',
            'password'        => Hash::make('michael'),
            'email'           => 'michael@novaframework.dev',
            'realname'        => 'Michael White',
            'image'           => null,
            'remember_token'  => '',
            'api_token'       => $this->uniqueToken(),
            'activated'       => 1,
            'activation_code' => null
        ));

        $user->roles()->attach(array(3));

        //
        $user = User::create(array(
            'id'              => 4,
            'username'        => 'john',
            'password'        => Hash::make('john'),
            'email'           => 'john@novaframework.dev',
            'realname'        => 'John Kennedy',
            'image'           => null,
            'remember_token'  => '',
            'api_token'       => $this->uniqueToken(),
            'activated'       => 1,
            'activation_code' => null,
        ));

        $user->roles()->attach(array(4));

        //
        $user = User::create(array(
            'id'             => 5,
            'username'       => 'mark',
            'password'       => Hash::make('mark'),
            'email'          => 'mark@novaframework.dev',
            'realname'       => 'Mark Black',
            'image'          => null,
            'remember_token' => '',
            'api_token'      => $this->uniqueToken(),
            'activated'       => 1,
            'activation_code' => null,
        ));

        $user->roles()->attach(array(4));
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
