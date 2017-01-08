<?php

namespace App\Modules\Users\Presenters;

use Nova\Support\Str;

use Shared\View\Presenter\Presenter;


class UserPresenter extends Presenter
{
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
        if (isset($this->image)) {
            return resource_url('images/users/' .basename($this->image->path));
        }

        // Fallback to AdminLTE's default image.
        return vendor_url('dist/img/avatar5.png', 'almasaeed2010/adminlte');
    }
}
