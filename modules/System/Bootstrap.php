<?php
/**
 * Bootstrap - the Module's specific Bootstrap.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */

use Modules\System\Exceptions\ValidationException;


App::error(function(ValidationException $exception, $code)
{
    $errors = $exception->getErrors();

    return Redirect::back()->withInput()->withErrors($errors);
});

