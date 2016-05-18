<?php
/**
 * Translator - Class to handle a Laravel-esque style Translations.
 *
 * NOTE: The real strings translation is made via the new \Core\Language API.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Validation;


class Translator
{
    /**
     * The Language lines used by the Translator.
     *
     * @var array
     */
    protected $loaded = array();

    /**
     * Create a new Translator instance.
     *
     * @param array $lines
     */
    public function __construct(array $lines = array())
    {
        if (! empty($lines)) {
            $this->setLines($lines);
        }
    }

    /**
     * Set the Language lines used by the Translator.
     *
     * @param  array $lines
     * @return void
     */
    public function setLines(array $lines)
    {
        $this->loaded = array('validation' => $lines);
    }

    /**
     * Get the translation for a given key.
     *
     * @param  string $id
     * @param  array  $parameters
     * @param  string $domain
     * @param  string $locale
     * @return string
     */
    public function trans($id, array $parameters = array(), $domain = 'messages', $locale = null)
    {
        $line = array_get($this->loaded, $id);

        if (is_null($line)) {
            return $id;
        }

        return $this->makeReplacements($line, $parameters);
    }

    /**
     * Make the place-holder replacements on a line.
     *
     * @param  string $line
     * @param  array  $replace
     * @return string
     */
    protected function makeReplacements($line, array $replace)
    {
        foreach ($replace as $key => $value) {
            $line = str_replace(':' .$key, $value, $line);
        }

        return $line;
    }
}
