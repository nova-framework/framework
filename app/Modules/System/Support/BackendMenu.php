<?php

namespace App\Modules\System\Support;

use Nova\Auth\Access\GateInterface;
use Nova\Auth\UserInterface;
use Nova\Support\Facades\Event;
use Nova\Support\Facades\Gate;
use Nova\Support\Facades\Request;
use Nova\Support\Arr;
use Nova\Support\Str;


class BackendMenu
{

    /**
     * Get the menu items for the specified Menu and User.
     *
     * @param  string  $name
     * @param  mixed  $user
     * @param  string|null  $url
     * @return array
     */
    public static function getItems($name, UserInterface $user, $url = null)
    {
        if (is_null($url)) {
            $url = Request::url();
        }

        $instance = new static();

        return $instance->handle($name, $user, $url);
    }

    /**
     * Get the menu items for the specified Menu and User.
     *
     * @param  string  $name
     * @param  mixed  $user
     * @param  string|null  $url
     * @return array
     */
    protected function handle($name, UserInterface $user, $url)
    {
        $gate = Gate::forUser($user);

        // The item path which coresponds with the current URL.
        $path = '';

        // Fire the Event and retrieve the results.
        $results = Event::fire($name, array($user));

        // Process the Event results.
        $items = array();

        foreach ($results as $result) {
            if (! is_array($result)) {
                continue;
            }

            foreach ($result as $item) {
                $key = str_replace('.', '.children.', $item['path']);

                if (Arr::has($items, $key)) {
                    continue;
                }

                // Ensure the children array existence.
                else if (! isset($item['children'])) {
                    $item['children'] = array();
                }

                // Check if the user is allowed to use this menu item.
                if (! $this->itemIsAllowed($item, $user, $gate)) {
                    continue;
                }

                // Add the item to the menu items array.
                Arr::set($items, $key, $item);

                if (($item['url'] == $url) && empty($path)) {
                    $path = $item['path'];
                }
            }
        }

        return $this->prepareItems($items, $path, $url);
    }

    /**
     * Determine if the menu item usage is allowed by the specified User Roles.
     *
     * @param  array  $item
     * @param  mixed  $user
     * @param  \Nova\Auth\Access\GateInterface  $gate
     * @return boolean
     */
    protected function itemIsAllowed(array $item, UserInterface $user, GateInterface $gate)
    {
        if (isset($item['role']) && ($item['role'] !== 'any')) {
            $roles = explode(',', $item['role']);

            if (! $user->hasRole($roles)) {
                return false;
            }
        }

        if (! isset($item['can'])) {
            return true;
        }

        $abilities = explode('|', $item['can']);

        foreach ($abilities as $ability) {
            list($ability, $parameters) = array_pad(explode(':', $ability, 2), 2, array());

            if (is_string($parameters)) {
                $parameters = explode(',', $parameters);
            }

            if (call_user_func(array($gate, 'allows'), $ability, $parameters)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Prepare the given menu items.
     *
     * @param  array  $items
     * @param  string $path
     * @param  string $url
     * @return array
     */
    protected function prepareItems(array $items, $path, $url)
    {
        foreach ($items as &$item) {
            $active = false;

            if (($item['url'] == $url) || Str::startsWith($path, $item['path'])) {
                $active = true;
            }

            $item['active'] = $active;

            if (! empty($children = $item['children'])) {
                $item['children'] = $this->prepareItems($children, $path, $url);
            }
        }

        // Sort the menu items by their weight and title.
        usort($items, function ($a, $b)
        {
            if ($a['weight'] === $b['weight']) {
                return strcmp($a['title'], $b['title']);
            }

            return ($a['weight'] < $b['weight']) ? -1 : 1;
        });

        return $items;
    }
}
