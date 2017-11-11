<?php

namespace App\Modules\Content\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;

use App\Modules\Content\Models\Tag;
use App\Modules\Content\Models\Taxonomy;


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
            'description' => null,
            'parent_id'   => 0,
            'count'       => 0,
        ));

        $taxonomy = Taxonomy::create(array(
            'id'          => 2,
            'term_id'     => 2,
            'taxonomy'    => 'nav_menu',
            'description' => null,
            'parent_id'   => 0,
            'count'       => 0,
        ));

        $taxonomy = Taxonomy::create(array(
            'id'          => 3,
            'term_id'     => 3,
            'taxonomy'    => 'post_tag',
            'description' => null,
            'parent_id'   => 0,
            'count'       => 0,
        ));

        //
        $tag = Tag::create(array(
            'id'          => 4,
            'term_id'     => 4,
            'taxonomy'    => 'tag',
            'description' => null,
            'parent_id'   => 0,
            'count'       => 0,
        ));
    }
}
