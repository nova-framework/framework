<?php

namespace App\Modules\Users\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;
use Nova\Support\Facades\Hash;
use Nova\Support\Str;

use App\Modules\Users\Models\User;


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
        ));

        $user->load('meta');

        $user->meta->first_name = 'Site';
        $user->meta->last_name  = 'Administrator';
        $user->meta->location   = 'Craiova, Romania';

        $user->meta->activated       = 1;
        $user->meta->activation_code = '';

        $user->meta->api_token = $this->uniqueToken();

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
        ));

        $user->load('meta');

        $user->meta->first_name = 'Marcus';
        $user->meta->last_name  = 'Spears';
        $user->meta->location   = 'London, UK';

        $user->meta->activated       = 1;
        $user->meta->activation_code = '';

        $user->meta->api_token = $this->uniqueToken();

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
        ));

        $user->load('meta');

        $user->meta->first_name = 'Michael';
        $user->meta->last_name  = 'White';
        $user->meta->location   = 'Rome, Italy';

        $user->meta->activated       = 1;
        $user->meta->activation_code = '';

        $user->meta->api_token = $this->uniqueToken();

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
        ));

        $user->load('meta');

        $user->meta->first_name = 'John';
        $user->meta->last_name  = 'Kennedy';
        $user->meta->location   = 'Moscow, Russia';

        $user->meta->activated       = 1;
        $user->meta->activation_code = '';

        $user->meta->api_token = $this->uniqueToken();

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
        ));

        $user->load('meta');

        $user->meta->first_name = 'Mark';
        $user->meta->last_name  = 'Black';
        $user->meta->location   = 'Paris, France';

        $user->meta->activated       = 1;
        $user->meta->activation_code = '';

        $user->meta->api_token = $this->uniqueToken();

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
