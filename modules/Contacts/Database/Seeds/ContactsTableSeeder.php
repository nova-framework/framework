<?php

namespace Modules\Contacts\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;

use Modules\Contacts\Models\Contact;


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
            'path'        => 'content/contact-us',
            'description' => 'The default site-wide Contact',

            //
            'created_by'  => 1,
        ));
    }
}
