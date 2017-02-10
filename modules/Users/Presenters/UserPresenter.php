<?php

namespace Modules\Users\Presenters;

use Nova\Support\Str;

use Plugins\Presenter\View\Presenter;


class UserPresenter extends Presenter
{
    /**
     * Present the User's name.
     *
     * @return string
     */
    public function name()
    {
        return $this->first_name .' ' .$this->last_name;
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

        return "//www.gravatar.com/avatar/{$hash}?size={$size}&default=mm";
    }

    /**
     * Present a link to the User's Profile Picture.
     *
     * @return string
     */
    public function picture()
    {
        if ($this->image->exists()) {
            return resource_url('images/users/' .basename($this->image->path));
        }

        // Fallback to AdminLTE's default image.
        return vendor_url('dist/img/avatar5.png', 'almasaeed2010/adminlte');
    }
}
