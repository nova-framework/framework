<?php

namespace Modules\Content\Traits;

use Nova\Support\Facades\Config;

use Thunder\Shortcode\ShortcodeFacade;


trait ShortcodesTrait
{
    /**
     * @var array
     */
    protected static $shortcodes = array();


    /**
     * @param string $tag the shortcode tag
     * @param \Closure $callback the shortcode handling function
     */
    public static function addShortcode($tag, $callback)
    {
        self::$shortcodes[$tag] = $callback;
    }

    /**
     * Removes a shortcode handler.
     *
     * @param string $tag the shortcode tag
     */
    public static function removeShortcode($tag)
    {
        if (isset(self::$shortcodes[$tag])) {
            unset(self::$shortcodes[$tag]);
        }
    }

    /**
     * Process the shortcodes.
     *
     * @param string $content the content
     * @return string
     */
    public function stripShortcodes($content)
    {
        $facade = new ShortcodeFacade();

        $this->parseClassShortcodes($facade);
        $this->parseConfigShortcodes($facade);

        return $facade->process($content);
    }
    /**
     * Process the shortcodes.
     *
     * @param string $content the content
     * @return string
     */
    public function parseShortcodes($content)
    {
        $facade = new ShortcodeFacade();

        $this->parseClassShortcodes($facade);
        $this->parseConfigShortcodes($facade);

        return $facade->parse($content);
    }

    /**
     * @param ShortcodeFacade $facade
     */
    private function parseClassShortcodes(ShortcodeFacade $facade)
    {
        foreach (self::$shortcodes as $tag => $callback) {
            $facade->addHandler($tag, $callback);
        }
    }

    /**
     * @param ShortcodeFacade $facade
     */
    private function parseConfigShortcodes(ShortcodeFacade $facade)
    {
        $shortcodes = Config::get('content::shortcodes', array());

        foreach ($shortcodes as $tag => $className) {
            $facade->addHandler($tag, array(new $className(), 'render'));
        }
    }
}
