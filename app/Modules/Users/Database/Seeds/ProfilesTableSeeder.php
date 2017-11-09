<?php

namespace App\Modules\Users\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;
use Nova\Support\Facades\Hash;
use Nova\Support\Str;

use App\Modules\Users\Models\Profile;


class ProfilesTableSeeder extends Seeder
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
            'name'     => 'First Name',
            'key'      => 'first_name',
            'type'     => 'App\Modules\Fields\Types\StringType',
            'validate' => 'required|min:3|max:100|valid_name',
            'hidden'   => 0,
            'order'    => 1,
        ));

        $fields->create(array(
            'name'     => 'Last Name',
            'key'      => 'last_name',
            'type'     => 'App\Modules\Fields\Types\StringType',
            'validate' => 'required|min:3|max:100|valid_name',
            'hidden'   => 0,
            'order'    => 2,
        ));

        $fields->create(array(
            'name'     => 'Location',
            'key'      => 'location',
            'type'     => 'App\Modules\Fields\Types\StringType',
            'validate' => null,
            'hidden'   => 0,
            'order'    => 3,
        ));

        $fields->create(array(
            'name'     => 'Picture',
            'key'      => 'picture',
            'type'     => 'App\Modules\Fields\Types\ImageType',
            'validate' => 'max:1024|mimes:png,jpg,jpeg,gif',
            'hidden'   => 0,
            'order'    => 4,
        ));

        //
        // Hidden fields.

        $fields->create(array(
            'name'     => 'User Activated',
            'key'      => 'activated',
            'type'     => 'App\Modules\Fields\Types\IntegerType',
            'validate' => null,
            'hidden'   => 1,
            'order'    => 0,
        ));

        $fields->create(array(
            'name'     => 'Activation Code',
            'key'      => 'activation_code',
            'type'     => 'App\Modules\Fields\Types\StringType',
            'validate' => null,
            'hidden'   => 1,
            'order'    => 0,
        ));

        $fields->create(array(
            'name'     => 'API Token',
            'key'      => 'api_token',
            'type'     => 'App\Modules\Fields\Types\StringType',
            'validate' => null,
            'hidden'   => 1,
            'order'    => 0,
        ));
    }
}
