<?php
/**
 * Users - A Users Model.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Modules\Users\Models;

use Nova\Auth\UserTrait;
use Nova\Auth\UserInterface;
use Nova\Auth\Reminders\RemindableTrait;
use Nova\Auth\Reminders\RemindableInterface;
use Nova\Database\ORM\Model as BaseModel;

use Shared\Database\ORM\FileField\FileFieldTrait;
use Shared\View\Presenter\PresentableTrait;

use Modules\Messages\Traits\HasMessagesTrait;
use Modules\System\Traits\HasNotificationsTrait;
use Modules\System\Traits\HasRoleTrait;


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

    protected $presenter = 'Modules\Users\Presenters\UserPresenter';

}
