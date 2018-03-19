<?php

namespace Modules\Content\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;


class DatabaseSeeder extends Seeder
{

    /**
     * Run the Database Seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        //
        $this->call('Modules\Content\Database\Seeds\TermsTableSeeder');
        $this->call('Modules\Content\Database\Seeds\TaxonomiesTableSeeder');
        $this->call('Modules\Content\Database\Seeds\PostsTableSeeder');
        $this->call('Modules\Content\Database\Seeds\CommentsTableSeeder');
    }
}
