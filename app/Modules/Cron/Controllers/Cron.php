<?php

namespace App\Modules\Cron\Controllers;

use Core\Config;
use Core\View;
use Core\Controller;

use App\Modules\Cron\Core\Manager;


class Cron extends Controller
{
    protected $layout = false;


    public function run($token)
    {
        $options = Config::get('cron');

        if($options['token'] != $token) {
            return Response::make('', 403); // Error 403 (Access denied)
        }

        //
        $timestamp = time();

        // Perform the required operations.
        $messages = array();

        $adapters = Manager::getAdapters();

        foreach($adapters as $adapter) {
            $instance = Manager::getAdapter($adapter);

            if(! is_null($instance)) {
                $result = $instance->execute();
            } else {
                $result = __d('cron', 'Cron Adapter not found: %s.', $adapter);
            }

            if(is_bool($result)) {
                $message = '<b>' .$adapter .':</b> ' .($result ? __d('cron', 'succesfully executed.') : __d('cron', 'execution failed.'));
            } else if(is_array($result)) {
                $message = '<b>' .$adapter .':</b><br>' .implode('<br>', $result);
            } else if(is_string($result)) {
                $message = '<b>' .$adapter .':</b> ' .$result;
            }

            $messages[] = $message;
        }

        //
        $dateFormat = __d('cron', 'jS M Y H:i:s');

        $title = __d('cron', '{0} - Cron executed on {1}', Config::get('app.name'), date($dateFormat, $timestamp));

        $message = '<p>' .implode('</p></p>', $messages) .'</p>';

        return $this->getView()
            ->with('title', $title)
            ->with('message', $message);
    }

}
