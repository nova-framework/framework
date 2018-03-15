<?php

namespace Modules\Contacts\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;

use Modules\Contacts\Models\FieldGroup;


class FieldGroupsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the table before seeding.
        FieldGroup::truncate();

        //
        // The Contact Form.

        $contact = FieldGroup::create(array(
            'id'          => 1,
            'title'       => 'Contact Form',
            'order'       => 0,

            'contact_id'  => 1,

            //
            'created_by'  => 1,
        ));
    }
}
