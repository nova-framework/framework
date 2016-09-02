<?php
/**
 * Translator - Class to handle a Laravel-esque style Translations.
 *
 * NOTE: The real translation are made via the new Language API.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Validation;

use Symfony\Component\Translation\TranslatorInterface;


class Translator implements TranslatorInterface
{
    /**
     * The Language lines used by the Translator.
     *
     * @var array
     */
    protected $messages = array();

    /**
     * Create a new Translator instance.
     */
    public function __construct()
    {
        $lines = require __DIR__ .DS .'messages.php';

        $this->messages = array('validation' => $lines);
    }

    /**
     * Get the translation for a given key.
     *
     * @param  string $id
     * @param  array  $params
     * @param  string $domain
     * @param  string $locale
     * @return string
     */
    public function trans($id, array $params = array(), $domain = 'messages', $locale = null)
    {
        $line = array_get($this->messages, $id);

        if (! is_null($line)) {
            return $this->makeReplacements($line, $params);
        }

        return $id;
    }

    /**
     * Translates the given choice message by choosing a translation according to a number.
     *
     * @param string      $id         The message id (may also be an object that can be cast to string)
     * @param int         $number     The number to use to find the indice of the message
     * @param array       $parameters An array of parameters for the message
     * @param string|null $domain     The domain for the message or null to use the default
     * @param string|null $locale     The locale or null to use the default
     *
     * @return string The translated string
     *
     * @throws \InvalidArgumentException If the locale contains invalid characters
     */
    public function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null)
    {
        //
    }

    /**
     * Sets the current locale.
     *
     * @param string $locale The locale
     *
     * @throws \InvalidArgumentException If the locale contains invalid characters
     */
    public function setLocale($locale)
    {
        //
    }

    /**
     * Returns the current locale.
     *
     * @return string The locale
     */
    public function getLocale()
    {
        return 'en';
    }

    /**
     * Make the place-holder replacements on a line.
     *
     * @param  string $line
     * @param  array  $replaces
     * @return string
     */
    protected function makeReplacements($line, array $replaces)
    {
        foreach ($replaces as $key => $value) {
            $line = str_replace(':' .$key, $value, $line);
        }

        return $line;
    }
}
