<?php

namespace Modules\System\Http\Controllers;

use Nova\Support\Facades\Config;
use Nova\Support\Facades\Response;
use Nova\Support\Facades\View;

use Plugins\Cron\Facades\Cron;

use App\Core\BaseController;

use Carbon\Carbon;


class CronRunner extends BaseController
{
    /**
     * @var string The CRON token
     */
    protected $token;


    public function __construct()
    {
        $this->token = Config::get('system::cron.token');
    }

    public function index($token)
    {
        if ($this->token != $token) {
            return Response::make('', 403); // Error 403 (Access denied)
        }

        // Get the execution date and time as translated string.
        $format = __d('system', '%d %b %Y, %H:%M');

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
        $responses = Cron::execute();

        // Prepare the CRON task messages.
        if (! empty($responses)) {
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

            $result = '<p>' .implode('</p></p>', $messages) .'</p>';
        } else {
            $result = '<p>' .__d('system', 'All tasks successfully executed.') .'</p>';
        }

        return $result;
    }

}
