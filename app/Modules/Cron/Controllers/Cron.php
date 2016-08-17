<?php

namespace App\Modules\Cron\Controllers;

use Core\Config;
use Core\View;
use Core\Controller;

use Cron as CronManager;
use Response;

use Carbon\Carbon;


class Cron extends Controller
{
    protected $layout = false;


    public function run($token)
    {
        $options = Config::get('cron');

        if($options['token'] != $token) {
            return Response::make('', 403); // Error 403 (Access denied)
        }

        // Get the init timestamp.
        $timestamp = new Carbon();

        // Execute the registered CRON Task.
        $results = CronManager::execute();

        foreach($results as $item) {
            list($name, $result) = $item;

            if (is_null($result)) {
                continue;
            } else if (is_bool($result)) {
                $result = $result ? __d('cron', 'successfully executed.') : __d('cron', 'execution failed.');
            }

            $messages[] = '<b>' .$name .'</b> : ' .$result;
        }

        //
        $title = __d('cron', '{0} - Cron executed on {1}', Config::get('app.name'), $timestamp->formatLocalized('%d %b %Y, %R'));

        $message = '<p>' .implode('</p></p>', $messages) .'</p>';

        return $this->getView()
            ->with('title', $title)
            ->with('message', $message);
    }

}
