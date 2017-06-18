<?php

namespace Backend\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;


class DatabaseSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		//
		//$this->call('Backend\Database\Seeds\FoobarTableSeeder');
		$this->call('Backend\Database\Seeds\RolesTableSeeder');
		$this->call('Backend\Database\Seeds\UsersTableSeeder');
	}

}
