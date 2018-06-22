<?php

namespace Modules\Content\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;

use Modules\Content\Models\Tag;
use Modules\Content\Models\Taxonomy;


class TaxonomiesTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the table before seeding.
        Taxonomy::truncate();

        //
        $taxonomy = Taxonomy::create(array(
            'id'          => 1,
            'term_id'     => 1,
            'taxonomy'    => 'category',
            'description' => '',
            'parent_id'   => 0,
            'count'       => 0,
        ));

        $taxonomy = Taxonomy::create(array(
            'id'          => 2,
            'term_id'     => 2,
            'taxonomy'    => 'nav_menu',
            'description' => '',
            'parent_id'   => 0,
            'count'       => 0,
        ));

        $taxonomy = Taxonomy::create(array(
            'id'          => 3,
            'term_id'     => 3,
            'taxonomy'    => 'tag',
            'description' => '',
            'parent_id'   => 0,
            'count'       => 0,
        ));
    }
}
