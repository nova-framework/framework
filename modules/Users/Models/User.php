<?php
/**
 * Users - A Users Model.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Users\Models;

use Nova\Auth\UserTrait;
use Nova\Auth\UserInterface;
use Nova\Auth\Reminders\RemindableTrait;
use Nova\Auth\Reminders\RemindableInterface;
use Nova\Database\ORM\Model as BaseModel;

use Shared\Database\ORM\FileField\FileFieldTrait;
use Shared\View\Presenter\PresentableTrait;

use Messages\Traits\HasMessagesTrait;
use System\Traits\HasNotificationsTrait;
use System\Traits\HasRoleTrait;


class User extends BaseModel implements UserInterface, RemindableInterface
{
    use UserTrait, RemindableTrait, HasRoleTrait, FileFieldTrait, HasMessagesTrait, HasNotificationsTrait, PresentableTrait;

    //
    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = array('role_id', 'username', 'password', 'first_name', 'last_name', 'email', 'active', 'image', 'activation_code');

    protected $hidden = array('password', 'activation_code', 'remember_token');

    public $files = array(
        'image' => array(
            'path'        => BASEPATH .'assets/images/users/:unique_id-:file_name',
            'defaultPath' => BASEPATH .'assets/images/users/no-image.png',
        ),
    );

    protected $presenter = 'Users\Presenters\UserPresenter';

}
