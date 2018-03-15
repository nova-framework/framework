<?php

namespace Modules\Users\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;

use Modules\Users\Models\FieldItem;


class FieldItemsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the table before seeding.
        FieldItem::truncate();

        //
        // The Default Contact.

        $item = FieldItem::create(array(
            'id'          => 1,
            'title'       => 'Location',
            'name'        => 'location',
            'type'        => 'text',
            'order'       => 0,
            'rules'       => 'required|valid_text',

            'options'     => array(
                'placeholder' => '',
                'default'     => '',
            ),

            'field_group_id' => 1,
            'created_by'     => 1,
        ));
    }
}
