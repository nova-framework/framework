<?php

namespace App\Modules\Contacts\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;

use App\Modules\Contacts\Models\Contact;
use App\Modules\Content\Models\Taxonomy;


class ContactsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the table before seeding.
        Contact::truncate();

        //
        // The Default Contact.
        $contact = Contact::create(array(
            'id'          => 1,
            'name'        => 'Site Contact',
            'email'       => 'admin@novaframework.dev',
            'description' => 'The default site-wide Contact',
            'path' => 'content/contact-us',
        ));
    }
}
