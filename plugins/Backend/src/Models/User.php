<?php

namespace Backend\Models;

use Nova\Auth\Contracts\UserInterface;
use Nova\Auth\UserTrait;
use Nova\Database\ORM\Model as BaseModel;
use Nova\Support\Str;

use FileField\Database\ORM\FileFieldTrait;
use Notifications\NotifiableTrait;
use Reminders\Contracts\RemindableInterface;
use Reminders\RemindableTrait;


class User extends BaseModel implements UserInterface, RemindableInterface
{
	use UserTrait, RemindableTrait, NotifiableTrait, FileFieldTrait;

	//
	protected $table = 'users';

	protected $primaryKey = 'id';

	protected $fillable = array('role_id', 'username', 'password', 'first_name', 'last_name', 'email', 'image', 'location');

	protected $hidden = array('password', 'remember_token');

	public $files = array(
		'image' => array(
			'path'		=> BASEPATH .'assets/images/users/:unique_id-:file_name',
			'defaultPath' => BASEPATH .'assets/images/users/no-image.png',
		),
	);

	public function activities()
	{
		return $this->hasMany('Backend\Models\Activity', 'user_id', 'id');
	}

	public function role()
	{
		return $this->belongsTo('Backend\Models\Role', 'role_id', 'id', 'role');
	}

	public function messages()
	{
		return $this->hasMany('Backend\Models\Message', 'sender_id', 'id');
	}

	public function scopeActiveSince($query, $since)
	{
		return $query->with(array('activities' => function ($query)
		{
			return $query->orderBy('last_activity', 'DESC');

		}))->whereHas('activities', function ($query) use ($since)
		{
			return $query->where('last_activity', '>=', $since);
		});
	}

	public function hasRole($roles, $strict = false)
	{
		if (! array_key_exists('role', $this->relations)) {
			$this->load('role');
		}

		$slug = strtolower($this->role->slug);

		// Check if the User has a Root role.
		if (($slug === 'root') && ! $strict) {
			return true;
		}

		foreach ((array) $roles as $role) {
			if (strtolower($role) == $slug) {
				return true;
			}
		}

		return false;
	}

	public function fullName()
	{
		return trim($this->first_name .' ' .$this->last_name);
	}

	/**
	 * Present a localized date for User's Creation.
	 *
	 * @return string
	 */
	public function memberSince()
	{
		$format = __d('users', '%d %b %Y, %R');

		return $this->created_at->formatLocalized($format);
	}

	/**
	 * Present a link to the user's Gravatar.
	 *
	 * @param int $size
	 * @return string
	 */
	public function gravatar($size = 30)
	{
		$email = Str::lower($this->email);

		$hash = md5($email);

		return "//www.gravatar.com/avatar/{$hash}?size={$size}&default=identicon";
	}

	/**
	 * Present a link to the User's Profile Picture.
	 *
	 * @return string
	 */
	public function picture()
	{
		if ($this->image->exists()) {
			return asset('assets/images/users/' .basename($this->image->path));
		}

		// Fallback to AdminLTE's default image.
		return asset('assets/images/users/no-image.png');
	}
}
