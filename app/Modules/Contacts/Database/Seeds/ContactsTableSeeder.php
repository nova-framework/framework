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
        $message = trim('
[input type="text" name="author" label="Name" columns="12"  validation="required"]

[input type="text" name="author_email" label="E-mail Address" columns="12"  validation="required|email"]

[textarea name="message" label="Message" columns="12" rows="10" validation="required"]

[input type="submit" name="submit" label="Submit Message"]
        ') ."\n";

        $contact = Contact::create(array(
            'id'          => 1,
            'name'        => 'Site Contact',
            'email'       => 'admin@novaframework.dev',
            'message'     => $message,
            'path'        => 'content/contact-us',
            'description' => 'The default site-wide Contact',
        ));
    }
}
