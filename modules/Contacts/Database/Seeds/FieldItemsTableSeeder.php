<?php

namespace Modules\Contacts\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;

use Modules\Contacts\Models\FieldItem;


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
            'title'       => 'Name',
            'name'        => 'author',
            'type'        => 'text',
            'order'       => 0,
            'rules'       => 'required|valid_name',

            'options'     => array(
                'placeholder' => '',
                'default'     => '',
            ),

            'visibble' => 1,

            'field_group_id' => 1,
            'created_by'     => 1,
        ));

        $item = FieldItem::create(array(
            'id'          => 2,
            'title'       => 'E-mail Address',
            'name'        => 'author_email',
            'type'        => 'text',
            'order'       => 1,
            'rules'       => 'required|email',

            'options'     => array(
                'placeholder' => '',
                'default'     => '',
            ),

            'visibble' => 1,

            'field_group_id' => 1,
            'created_by'     => 1,
        ));

        $item = FieldItem::create(array(
            'id'          => 3,
            'title'       => 'Website',
            'name'        => 'author_url',
            'type'        => 'text',
            'order'       => 2,
            'rules'       => 'sometimes|required|url',

            'options'     => array(
                'placeholder' => '',
                'default'     => '',
            ),

            'visibble' => 0,

            'field_group_id' => 1,
            'created_by'     => 1,
        ));

        $item = FieldItem::create(array(
            'id'          => 4,
            'title'       => 'Message',
            'name'        => 'message',
            'type'        => 'textarea',
            'order'       => 3,
            'rules'       => 'required|valid_text',

            'options'     => array(
                'placeholder' => '',
                'rows'        => 10,
            ),

            'visibble' => 1,

            'field_group_id' => 1,
            'created_by'     => 1,
        ));
    }
}
