<?php

namespace App\Modules\WebChat\Core;

use Nova\Support\Facades\Validator;

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

}
