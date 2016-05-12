<?php
/**
 * Redirect - Manage the HTTP redirection Responses.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Core;

use Core\Response;
use Helpers\Session;


/**
 * Class Redirect.
 */
class Redirect extends Response
{
    /**
     * Create a Redirect Response.
     *
     * <code>
     *      // Create a Redirect Response to a location within the application
     *      return Redirect::to('user/profile');
     *
     *      // Create a Redirect Response with a 301 status code
     *      return Redirect::to('user/profile', 301);
     * </code>
     *
     * @param  string    $url
     * @param  int       $status
     * @return Redirect
     */
    public static function to($url, $status = 302)
    {
        // The Content is a workaround for when the HTTP Headers are already sent.
        $content = '
<html>
<body onload="redirect_to(\'' .$url .'\');"></body>
<script type="text/javascript">function redirect_to(url) { window.location.href = url; }</script>
</body>
</html>';

        return static::make($content, $status)->header('Location', site_url($url));
    }

    /**
     * Add an item to the Session flash data.
     *
     * This is useful for "passing" status messages or other data to the next request.
     *
     * <code>
     *      // Create a Redirect Response and flash to the Session
     *      return Redirect::to('profile')->with('message', 'Welcome Back!');
     * </code>
     *
     * @param  string          $key
     * @param  mixed           $value
     * @return Redirect
     */
    public function with($key, $value)
    {
        Session::set($key, $value);

        return $this;
    }

    /**
     * Send the HTTP Headers and Content of the Response to the web-browser.
     *
     * @return void
     */
    public function send()
    {
        // Dump all output buffering first.
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        parent::send();
    }
}
