<?php

namespace Backend\Support;

use Nova\Support\Facades\Request;
use Nova\Support\Facades\View;
use Nova\Support\Arr;
use Nova\Support\Str;


class Menu
{
    /**
     * The partial View used for rendering this Menu.
     *
     * @var string
     */
    protected $view;

    /**
     * The current page URL.
     *
     * @var string
     */
    protected $currentUrl;

    /**
     * The item key which match the current URL.
     *
     * @var string
     */
    protected $currentKey;

    /**
     * The registered menu items.
     *
     * @var array
     */
    protected $items = array();


    /**
     * Create a new Menu instance.
     *
     * @param  string  $view
     */
    public function __construct($view)
    {
        $this->view = $view;

        $this->currentUrl = Request::url();
    }

    /*
     * Shortcut method for create a menu with a callback.
     * This will allow you to do things like fire an even on creation.
     *
     * @param callable $callback Callback to use after the menu creation
     * @return object
     */
    public static function make($view, $callback)
    {
        $menu = new static($view);

        call_user_func($callback, $menu);

        return $menu->sortItems();
    }

    /*
     * Render this Menu and return the result.
     *
     * @return string
     */
    public function render()
    {
        return View::fetch($this->view, array('menu' => $this));
    }

    /*
     * Add a menu item to the item stack
     *
     * @param string $key Dot separated hierarchy
     * @param string $name Text for the anchor
     * @param string $url URL for the anchor
     * @param integer $sort Sorting index for the items
     * @param string $icon URL to use for the icon
    */
    public function addItem($key, $name, $url, $sort = 0, $icon = null)
    {
        $item = array(
            'key'        => $key,
            'name'        => $name,
            'url'        => $url,
            'sort'        => $sort,
            'icon'        => $icon,

            // Add the children for convenience.
            'children'    => array(),
        );

        // Get the qualified item key.
        $key = str_replace('.', '.children.', $key);

        if (! Arr::has($this->items, $key)) {
            Arr::set($this->items, $key, $item);
        }

        if ($url == $this->currentUrl) {
            $this->currentKey = $key;
        }
    }

    /*
     * Method to find the active links
     *
     * @param array $item Item that needs to be checked if active
     * @return string
    */
    public function itemIsActive(array $item)
    {
        $url = trim($item['url'], '/');

        if ($this->currentUrl === $url) {
            return true;
        } else if (Str::startsWith($this->currentKey, $item['key'])) {
            return true;
        }

        return false;
    }

    /*
     * Method to sort through the menu items and put them in order
     *
     * @return void
    */
    protected function sortItems()
    {
        usort($this->items, function($a, $b)
        {
            if ($a['sort'] === $b['sort']) {
                return strcmp($a['name'], $b['name']);
            }

            return ($a['sort'] < $b['sort']) ? -1 : 1;
        });

        return $this;
    }

    /**
     * Gets the partial View used for rendering this Menu.
     *
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Gets the current page URL.
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->currentUrl;
    }

    /**
     * Gets the item key which match the current URL.
     *
     * @return string|null
     */
    public function getCurrentKey()
    {
        return $this->currentKey;
    }

    /**
     * Gets the registered menu items.
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }
}
