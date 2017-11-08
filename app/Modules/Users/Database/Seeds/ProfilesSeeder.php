<?php

namespace App\Modules\Users\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;
use Nova\Support\Facades\Hash;
use Nova\Support\Str;

use App\Modules\Users\Models\Profile;


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
        Profile::truncate();

        //
        $profile = Profile::create(array(
            'id'          => 1,
            'name'        => 'User',
            'slug'        => 'user',
            'description' => __d('users', 'This Profile is associated to all registered Users.'),
        ));

        $fields = $profile->fields();

        //
        $fields->delete();

        $fields->create(array(
            'name'       => 'Name',
            'key'        => 'first_name',
            'type'       => 'App\Modules\Fields\Fields\StringField',
            'required'   => 1,
            'validation' => null,
        ));

        $fields->create(array(
            'name'       => 'Surname',
            'key'        => 'last_name',
            'type'       => 'App\Modules\Fields\Fields\StringField',
            'required'   => 1,
            'validation' => null,
        ));

        $fields->create(array(
            'name'       => 'Location',
            'key'        => 'location',
            'type'       => 'App\Modules\Fields\Fields\StringField',
            'required'   => 0,
            'validation' => null,
        ));
    }
}
