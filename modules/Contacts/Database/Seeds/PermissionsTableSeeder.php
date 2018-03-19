<?php

namespace Modules\Contacts\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;

use Modules\Permissions\Traits\ManagePermissionsTrait;


class PermissionsTableSeeder extends Seeder
{
    use ManagePermissionsTrait;


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permissions = array(
            // Contacts.
            array(
                'name'  => 'View the Contacts List',
                'slug'  => 'module.contacts.contacts.lists',
                'group' => 'contacts',

                'roles' => array(1, 2),
            ),
            array(
                'name'  => 'View the Contacts',
                'slug'  => 'module.contacts.contacts.view',
                'group' => 'contacts',

                'roles' => array(1, 2),
            ),
            array(
                'name'  => 'Create new Contacts',
                'slug'  => 'module.contacts.contacts.create',
                'group' => 'contacts',

                'roles' => array(1, 2),
            ),
            array(
                'name'  => 'Update the Contacts',
                'slug'  => 'module.contacts.contacts.update',
                'group' => 'contacts',

                'roles' => array(1, 2),
            ),
            array(
                'name'  => 'Delete Contacts',
                'slug'  => 'module.contacts.contacts.delete',
                'group' => 'contacts',

                'roles' => array(1, 2),
            ),

            // Contact Messages.
            array(
                'name'  => 'View the Contact Messages List',
                'slug'  => 'module.contacts.messages.lists',
                'group' => 'contacts',

                'roles' => array(1, 2),
            ),
            array(
                'name'  => 'View the Contact Messages',
                'slug'  => 'module.contacts.messages.view',
                'group' => 'contacts',

                'roles' => array(1, 2),
            ),
            array(
                'name'  => 'Create new Contact Messages',
                'slug'  => 'module.contacts.messages.create',
                'group' => 'contacts',

                'roles' => array(1, 2, 3, 4),
            ),
            array(
                'name'  => 'Delete Contact Messages',
                'slug'  => 'module.contacts.messages.delete',
                'group' => 'contacts',

                'roles' => array(1, 2),
            ),
        );

        $this->createPermissions($permissions);
    }
}
