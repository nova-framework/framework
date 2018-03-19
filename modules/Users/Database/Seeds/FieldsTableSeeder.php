<?php

namespace Modules\Users\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;

use Modules\Users\Models\Field;


class FieldsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the table before seeding.
        Field::truncate();

        $item = Field::create(array(
            'id'         => 1,
            'type'       => 'text',
            'name'       => 'location',
            'value'      => 'Craiova, Romania',

            //
            'user_id'       => 1,
            'field_item_id' => 1,
        ));

        $item = Field::create(array(
            'id'         => 2,
            'type'       => 'text',
            'name'       => 'location',
            'value'      => 'London, UK',

            //
            'user_id'       => 2,
            'field_item_id' => 1,
        ));

        $item = Field::create(array(
            'id'         => 3,
            'type'       => 'text',
            'name'       => 'location',
            'value'      => 'Rome, Italy',

            //
            'user_id'       => 3,
            'field_item_id' => 1,
        ));

        $item = Field::create(array(
            'id'         => 4,
            'type'       => 'text',
            'name'       => 'location',
            'value'      => 'Moscow, Russia',

            //
            'user_id'       => 4,
            'field_item_id' => 1,
        ));

        $item = Field::create(array(
            'id'         => 5,
            'type'       => 'text',
            'name'       => 'location',
            'value'      => 'Paris, France',

            //
            'user_id'       => 5,
            'field_item_id' => 1,
        ));
    }
}
