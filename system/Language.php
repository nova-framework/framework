<?php
/**
 * Language - Language handler.
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 15th, 2015
 */


namespace Nova;

use Nova\Helpers\Inflector;
use Nova\Config;
use Nova\Error;

/**
 * Language class to load the requested domain language file.
 */
class Language
{
    private $code   = 'en';
    private $info   = 'English';
    private $name   = 'English';
    private $locale = 'en-US';

    /**
     * Variable holds array with language.
     *
     * @var array
     */
    private $messages = array();

    // The domain instances array.
    private static $instances = array();

    /**
     * Language constructor.
     * @param string $domain
     * @param string $code
     */
    public function __construct($domain, $code)
    {
        $languages = Config::get('languages');

        if(isset($code, $languages)) {
            $info = $languages[$code];

            $this->code = $code;

            $this->info   = $info['info'];
            $this->name   = $info['name'];
            $this->locale = $info['locale'];
        }
        else {
            $code = 'en';
        }

        //
        $pathName = Inflector::classify($domain);

        $langPath = '';

        if($pathName == 'System') {
            $langPath = SYSPATH;
        }
        else if($pathName == 'App') {
            $langPath = APPPATH;
        }
        else if(is_dir(APPPATH.'Modules'.DS.$pathName)) {
            $langPath = APPPATH.'Modules/'.$pathName;
        }
        else if(is_dir(APPPATH.'Templates'.DS.$pathName)) {
            $langPath = APPPATH.'Templates/'.$pathName;
        }

        if(empty($langPath)) {
            return;
        }

        $filePath = str_replace('/', DS, $langPath.'/Language/'.$code.'/messages.php');

        // Check if the language file is readable.
        if(! is_readable($filePath)) {
            return;
        }

        // Get the domain messages from the language file.
        $messages = include($filePath);

        // Final Consistency check.
        if(is_array($messages) && ! empty($messages)) {
            $this->messages = $messages;
        }
    }

    /**
     * Get instance of language with domain and code (optional).
     * @param string $domain Optional custom domain
     * @param string $code Optional custom language code.
     * @return Language
     */
    public static function &get($domain = 'app', $code = LANGUAGE_CODE)
    {
        // The ID code is something like: 'en/system', 'en/app', 'en/file_manager' or 'en/template/admin'
        $id = $code.'/'.$domain;

        // Initialize the domain instance, if not already exists.
        if(! isset(self::$instances[$id])) {
            self::$instances[$id] = new self($domain, $code);
        }

        return self::$instances[$id];
    }

    /**
     * Translate a message with optional formatting
     * @param string $message Original message.
     * @param array $params Optional params for formatting.
     * @return string
     */
    public function translate($message, $params = array())
    {
        // Update the current message with the domain translation, if we have one.
        if(isset($this->messages[$message]) && ! empty($this->messages[$message])) {
            $message = $this->messages[$message];
        }

        if(empty($params)) {
            return $message;
        }

        // Standard Message formatting, using the standard PHP Intl and its MessageFormatter.
        // The message string should be formatted using the standard ICU commands.
        return \MessageFormatter::formatMessage($this->locale, $message, $params);

        // The VSPRINTF alternative for Message formatting, for those die-hard against ICU.
        // The message string should be formatted using the standard PRINTF commands.
        //return vsprintf($message, $arguments);
    }

    // Public Getters

    /**
     * Get current code
     * @return string
     */
    public function code()
    {
        return $this->code;
    }

    /**
     * Get current info
     * @return string
     */
    public function info()
    {
        return $this->info;
    }

    /**
     * Get current name
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Get current locale
     * @return string
     */
    public function locale()
    {
        return $this->locale;
    }

    /**
     * Get all messages
     * @return array
     */
    function messages()
    {
        return $this->messages;
    }

}
