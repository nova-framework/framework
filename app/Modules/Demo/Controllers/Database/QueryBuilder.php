<?php
/**
 * Welcome controller
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 17th, 2015
 */

namespace App\Modules\Demo\Controllers\Database;

use Nova\Core\View;
use Nova\Database\Query\Builder\Facade as DB;
use App\Modules\Demo\Core\BaseController;

/**
 * Sample Themed Controller with its typical usage.
 */
class QueryBuilder extends BaseController
{

    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function beforeFlight()
    {
        // Leave to parent's method the Flight decisions.
        return parent::beforeFlight();
    }

    protected function afterFlight($result)
    {
        // Do some processing there, even deciding to stop the Flight, if case.

        // Leave to parent's method the Flight decisions.
        return parent::afterFlight($result);
    }

    /**
     * CakePHP style - Define Welcome page message and set the Controller's variables.
     */
    public function index()
    {
        $message = '';

        //
        $data = DB::table('users')->where('username', '!=', 'marcus')->count();

        $text = "
\$data = DB::table('users')->where('username', '!=', 'marcus')->count();

var_export(\$data, true);
        ";

        $message .= self::highlightText($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = DB::table('users')->get();

        $text = "
\$data = DB::table('users')->get();

var_export(\$data, true);
        ";

        $message .= self::highlightText($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = DB::table('users')->find(3);

        $text = "
\$data = DB::table('users')->find(3);

var_export(\$data, true);
        ";

        $message .= self::highlightText($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = DB::table('users')
            ->whereIn('id', array(1, 2, 4))
            ->orderBy('username')
            ->limit(2)
            ->get();

        $text = "
\$data = DB::table('users')
    ->whereIn('id', array(1, 2, 4))
    ->orderBy('username')
    ->limit(2)
    ->get();

var_export(\$data, true);
        ";

        $message .= self::highlightText($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = DB::table('users')
            ->where('username', '!=', 'admin')
            ->orderBy('email', 'DESC')
            ->limit(2)
            ->offset(1)
            ->get();

        $text = "
\$data = DB::table('users')
    ->where('username', '!=', 'admin')
    ->orderBy('email', 'DESC')
    ->limit(2)
    ->offset(1)
    ->get();

var_export(\$data, true);
        ";

        $message .= self::highlightText($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $query = DB::query(
            'SELECT * FROM ' .DB_PREFIX .'users WHERE username != ? ORDER BY ? LIMIT ? OFFSET ?',
            array('admin', 'email DESC', 2, 1)
        );

        $data = $query->get();

        $text = "
\$query = DB::query(
    'SELECT * FROM ' .DB_PREFIX .'users WHERE username != ? ORDER BY ? LIMIT ? OFFSET ?',
    array('admin', 'email DESC', 2, 1)
);

\$data = \$query->get();

var_export(\$data, true);
        ";

        $message .= self::highlightText($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $query = DB::query('SELECT * FROM ' .DB_PREFIX .'users WHERE username != :username ORDER BY :orderBy LIMIT :limit OFFSET :offset',
            array(
                ':username' => 'admin',
                ':orderBy'  => 'email DESC',
                ':limit'    => 2,
                ':offset'   => 1
            )
        );

        $data = $query->get();

        $text = "
\$query = DB::query(
    'SELECT * FROM ' .DB_PREFIX .'users WHERE username != :username ORDER BY :orderBy LIMIT :limit OFFSET :offset',
    array(
        ':username' => 'admin',
        ':orderBy' => 'email DESC',
        ':limit' => 2,
        ':offset' => 1
    )
);

\$data = \$query->get();

var_export(\$data, true);
        ";

        $message .= self::highlightText($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $userInfo = array(
            'username' => 'virgil',
            'email'    => 'virgil@novaframework.dev'
        );

        $userId = DB::table('users')->insert($userInfo);


        $text = "
\$userInfo = array(
    'username' => 'virgil',
    'email'    => 'virgil@novaframework.dev'
);

\$userId = DB::table('users')->insert(\$userInfo);

var_export(\$userId, true);
        ";

        $message .= self::highlightText($text);
        $message .= '<pre>'.var_export($userId, true).'</pre><br>';

        //
        $data = DB::table('users')->where('id', $userId)->first();

        $text = "
\$data = DB::table('users')->where('id', \$userId)->first();

var_export(\$data, true);
        ";

        $message .= self::highlightText($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $userInfo = array(
            'email' => 'modified@novaframework.dev'
        );

        $result = DB::table('users')->where('id', $userId)->update($userInfo);

        $text = "
\$userInfo = array(
    'email' => 'modified@novaframework.dev'
);

\$result = DB::table('users')->where('id', \$userId)->update(\$userInfo);

var_export(\$result, true);
        ";

        $message .= self::highlightText($text);
        $message .= '<pre>'.var_export($result, true).'</pre>';

        $data = DB::table('users')->where('id', $userId)->asObject()->first();

        $text = "
\$data = DB::table('users')->where('id', \$userId)->asObject()->first();

var_export(\$data, true);
        ";

        $message .= self::highlightText($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $result = DB::table('users')->where('username', 'virgil')->delete();

        $text = "
\$result = DB::table('users')->where('username', 'virgil')->delete();

var_export(\$result, true);
        ";

        $message .= self::highlightText($text);
        $message .= '<pre>'.var_export($result, true).'</pre><br>';

        //
        $data = DB::table('users')->get();

        $text = "
\$data = DB::table('users')->get();

var_export(\$data, true);
        ";

        $message .= self::highlightText($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        // Setup the View variables.
        $this->title(__d('demo', 'Query Builder Demo'));

        $this->set('message', $message);
    }

    private static function highlightText($text)
    {
        $text = trim($text);
        $text = highlight_string("<?php " . $text, true);  // highlight_string requires opening PHP tag or otherwise it will not colorize the text
        $text = trim($text);
        $text = preg_replace("|^\\<code\\>\\<span style\\=\"color\\: #[a-fA-F0-9]{0,6}\"\\>|", "", $text, 1);  // remove prefix
        $text = preg_replace("|\\</code\\>\$|", "", $text, 1);  // remove suffix 1
        $text = trim($text);  // remove line breaks
        $text = preg_replace("|\\</span\\>\$|", "", $text, 1);  // remove suffix 2
        $text = trim($text);  // remove line breaks
        $text = preg_replace("|^(\\<span style\\=\"color\\: #[a-fA-F0-9]{0,6}\"\\>)(&lt;\\?php&nbsp;)(.*?)(\\</span\\>)|", "\$1\$3\$4", $text);  // remove custom added "<?php "

        // Finall processing.
        $text = '<div style="font-weight: bold; margin-bottom: 10px;">'.$text.'</div>';

        return $text;
    }

}
