<?php

namespace App\Modules\WebChat\Core;

use Nova\Support\Facades\Assets;
use Nova\Support\Facades\Validator;
use Nova\Support\Facades\View;

use App\Core\BackendController;
use App\Modules\System\Exceptions\ValidationException;


class Controller extends BackendController
{

    protected function validate(array $data, array $rules, array $messages = array(), array $attributes = array())
    {
        $validator = Validator::make($data, $rules, $messages, $attributes);

        // Go Exception if the data validation fails.
        if ($validator->fails()) {
            throw new ValidationException('Validation failed', $validator->errors());
        }
    }

    protected function before()
    {
        // Additional assets required.
        $css = Assets::fetch('css', array(
            'https://cdnjs.cloudflare.com/ajax/libs/emojione/2.2.7/assets/css/emojione.min.css',
            resource_url('css/style.css', 'WebChat'),
        ));

        $js = Assets::fetch('js', array(
            'https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/emojione/2.2.7/lib/js/emojione.min.js',
            resource_url('js/simplewebrtc-latest.js', 'WebChat'),
            vendor_url('plugins/slimScroll/jquery.slimscroll.min.js', 'almasaeed2010/adminlte'),
        ));

        // Share the defined assets to Views.
        View::share('css', $css);
        View::share('js', $js);

        //
        parent::before();
    }

}
