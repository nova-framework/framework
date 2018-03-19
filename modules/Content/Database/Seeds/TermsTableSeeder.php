<?php

namespace Modules\Content\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;

use Modules\Content\Models\Term;


class TermsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the table before seeding.
        Term::truncate();

        //
        $term = Term::create(array(
            'id'     => 1,
            'name'   => 'Uncategorized',
            'slug'   => 'uncategorized',
            'group'  => 0,
        ));

        $term = Term::create(array(
            'id'     => 2,
            'name'   => 'Main Menu',
            'slug'   => 'main-menu',
            'group'  => 0,
        ));

        $term = Term::create(array(
            'id'     => 3,
            'name'   => 'Post Tag',
            'slug'   => 'post-tag',
            'group'  => 0,
        ));

        $term = Term::create(array(
            'id'     => 4,
            'name'   => 'Sample Tag',
            'slug'   => 'sample-tag',
            'group'  => 0,
        ));
    }
}
