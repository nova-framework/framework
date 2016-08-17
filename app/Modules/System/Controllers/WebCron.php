<?php

namespace App\Modules\System\Controllers;

use Core\Config;
use Core\View;
use Core\Controller;

use Cron;
use Response;

use Carbon\Carbon;


class WebCron extends Controller
{
    protected $layout = false;

    /**
     * @var string The CRON token
     */
    protected $token;


    public function __construct()
    {
        $this->token = Config::get('cron.token');
    }

    public function run($token)
    {
        if ($this->token != $token) {
            return Response::make('', 403); // Error 403 (Access denied)
        }

        // Get the init timestamp.
        $timestamp = new Carbon();

        // Execute the registered CRON Task.
        $responses = Cron::execute();

        foreach($responses as $response) {
            list($name, $result) = $response;

            if (is_null($result)) {
                $result = __d('system', 'executed.');
            } else if (is_bool($result)) {
                $result = $result ? __d('system', 'successfully executed.') : __d('system', 'execution failed.');
            }

            $messages[] = '<b>' .$name .'</b> : ' .$result;
        }

        // Create the page information.
        $date = $timestamp->formatLocalized(__d('system', '%d %b %Y, %R'));

        $title = __d('system', '{0} - Cron executed on {1}', Config::get('app.name'), $date);

        $content = '<p>' .implode('</p></p>', $messages) .'</p>';

        return $this->getView()
            ->with('title', $title)
            ->with('content', $content);
    }

}
