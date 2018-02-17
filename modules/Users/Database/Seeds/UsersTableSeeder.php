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
            'remember_token' => '',
            'profile_id'     => 1,
            'api_token'      => $this->uniqueToken(),
        ));

        $user->load('meta');

        $user->first_name = 'Site';
        $user->last_name  = 'Administrator';
        $user->location   = 'Craiova, Romania';

        $user->activated       = 1;
        $user->activation_code = '';

        $user->save();

        $user->roles()->attach(array(1));

        //
        $user = User::create(array(
            'id'             => 2,
            'username'       => 'marcus',
            'password'       => Hash::make('marcus'),
            'email'          => 'marcus@novaframework.dev',
            'remember_token' => '',
            'profile_id'     => 1,
            'api_token'      => $this->uniqueToken(),
        ));

        $user->load('meta');

        $user->first_name = 'Marcus';
        $user->last_name  = 'Spears';
        $user->location   = 'London, UK';

        $user->activated       = 1;
        $user->activation_code = '';

        $user->save();

        $user->roles()->attach(array(2));

        //
        $user = User::create(array(
            'id'             => 3,
            'username'       => 'michael',
            'password'       => Hash::make('michael'),
            'email'          => 'michael@novaframework.dev',
            'remember_token' => '',
            'profile_id'     => 1,
            'api_token'      => $this->uniqueToken(),
        ));

        $user->load('meta');

        $user->first_name = 'Michael';
        $user->last_name  = 'White';
        $user->location   = 'Rome, Italy';

        $user->activated       = 1;
        $user->activation_code = '';

        $user->save();

        $user->roles()->attach(array(3));

        //
        $user = User::create(array(
            'id'             => 4,
            'username'       => 'john',
            'password'       => Hash::make('john'),
            'email'          => 'john@novaframework.dev',
            'remember_token' => '',
            'profile_id'     => 1,
            'api_token'      => $this->uniqueToken(),
        ));

        $user->load('meta');

        $user->first_name = 'John';
        $user->last_name  = 'Kennedy';
        $user->location   = 'Moscow, Russia';

        $user->activated       = 1;
        $user->activation_code = '';

        $user->save();

        $user->roles()->attach(array(4));

        //
        $user = User::create(array(
            'id'             => 5,
            'username'       => 'mark',
            'password'       => Hash::make('mark'),
            'email'          => 'mark@novaframework.dev',
            'remember_token' => '',
            'profile_id'     => 1,
            'api_token'      => $this->uniqueToken(),
        ));

        $user->load('meta');

        $user->first_name = 'Mark';
        $user->last_name  = 'Black';
        $user->location   = 'Paris, France';

        $user->activated       = 1;
        $user->activation_code = '';

        $user->save();

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
