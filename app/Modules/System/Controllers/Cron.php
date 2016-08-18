<?php

namespace App\Modules\System\Controllers;

use Core\Config;
use Core\View;
use Core\Controller;

use Cron as CronManager;
use Response;

use Carbon\Carbon;


class Cron extends Controller
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

    public function index($token)
    {
        if ($this->token != $token) {
            return Response::make('', 403); // Error 403 (Access denied)
        }

        // Get the execution date and time as translated string.
        $format = __d('system', '%d %b %Y, %R');

        $date = Carbon::now()->formatLocalized($format);

        // Execute the CRON tasks.
        $result = $this->executeCron();

        // Create the page information.
        $title = __d('system', '{0} - Cron executed on {1}', Config::get('app.name'), $date);

        return $this->getView()
            ->with('title', $title)
            ->with('content', $result);
    }

    protected function executeCron()
    {
        // Execute the registered CRON Task.
        $responses = CronManager::execute();

        // Prepare the CRON task messages.
        $messages = array();

        foreach($responses as $response) {
            list($name, $result) = $response;

            if (is_null($result)) {
                $result = __d('system', 'executed.');
            } else if (is_bool($result)) {
                $result = $result ? __d('system', 'successfully executed.') : __d('system', 'execution failed.');
            }

            $messages[] = '<b>' .$name .'</b> : ' .$result;
        }

        // Create the CRON execution repport and return it.
        $result = '<p>' .implode('</p></p>', $messages) .'</p>';

        return $result;
    }

}
