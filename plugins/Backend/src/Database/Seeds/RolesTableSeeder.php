<?php

namespace Backend\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;

use Backend\Models\Role;


class RolesTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		// Truncate the table before seeding.
		Role::truncate();

		Role::create(array(
			'id'			=> 1,
			'name'			=> 'Root',
			'slug'			=> 'root',
			'description'	=> 'Use this account with extreme caution. When using this account it is possible to cause irreversible damage to the system.',
		));

		Role::create(array(
			'id'			=> 2,
			'name'			=> 'Administrator',
			'slug'			=> 'administrator',
			'description'	=> 'Full access to create, edit, and update companies, and orders.',
		));

		Role::create(array(
			'id'			=> 3,
			'name'			=> 'Manager',
			'slug'			=> 'manager',
			'description'	=> 'Ability to create new companies and orders, or edit and update any existing ones.',
		));

		Role::create(array(
			'id'			=> 4,
			'name'			=> 'Company Manager',
			'slug'			=> 'company-manager',
			'description'	=> 'Able to manage the company that the user belongs to, including adding sites, creating new users and assigning licences.',
		));

		Role::create(array(
			'id'			=> 5,
			'name'			=> 'User',
			'slug'			=> 'user',
			'description'	=> 'A standard user that can have a licence assigned to them. No administrative features.',
		));

	}
}
