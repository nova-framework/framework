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
            'slug'        => 'name',
            'type'        => 'text',
            'order'       => 0,
            'rules'       => 'required|valid_name',

            'options'     => array(
                'default' => '',
            ),

            'field_group_id' => 1,

            //
            'created_by'  => 1,
        ));

        $item = FieldItem::create(array(
            'id'          => 2,
            'title'       => 'E-mail Address',
            'slug'        => 'email',
            'type'        => 'text',
            'order'       => 1,
            'rules'       => 'required|email',

            'options'     => array(
                'default' => '',
            ),

            'field_group_id' => 1,

            //
            'created_by'  => 1,
        ));

        $item = FieldItem::create(array(
            'id'          => 3,
            'title'       => 'Website',
            'slug'        => 'website',
            'type'        => 'text',
            'order'       => 2,
            'rules'       => 'sometimes|required|url',

            'options'     => array(
                'default' => '',
            ),

            'field_group_id' => 1,

            //
            'created_by'  => 1,
        ));

        $item = FieldItem::create(array(
            'id'          => 4,
            'title'       => 'Message',
            'slug'        => 'message',
            'type'        => 'textarea',
            'order'       => 3,
            'rules'       => 'required|valid_text',

            'options'     => array(
                'rows' => 5,
            ),

            'field_group_id' => 1,

            //
            'created_by'  => 1,
        ));

        $item = FieldItem::create(array(
            'id'          => 5,
            'title'       => 'Attachment',
            'slug'        => 'attachment',
            'type'        => 'file',
            'order'       => 4,
            'rules'       => 'max:10240|mimes:zip,rar,pdf,png,jpg,jpeg,doc,docx',

            'options'     => null,

            'field_group_id' => 1,

            //
            'created_by'  => 1,
        ));
    }
}
