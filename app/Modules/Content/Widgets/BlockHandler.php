<?php

namespace App\Modules\Content\Widgets;

use Nova\Container\Container;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Request;
use Nova\Support\Facades\View;

use Shared\Widgets\Widget;

use App\Modules\Content\Models\Block;

use Carbon\Carbon;


class BlockHandler extends Widget
{
    /**
     * @var \Nova\Container\Container
     */
    protected $container;

    /**
     * @var \App\Modules\Content\Models\Block
     */
    protected $block;


    public function __construct(Container $container, Block $block)
    {
        $this->container = $container;

        $this->block = $block;
    }

    public function render()
    {
        if (! $this->blockAllowsRendering()) {
            return;
        }

        $block = $this->block;

        // Render the Block Handler instance.
        $content = '';

        if (! empty($name = $block->block_handler_class)) {
            $parameters = $block->block_handler_param;

            if (! is_array($parameters)) {
                $parameters = array($parameters);
            }

            $content = $this->callBlockHandler($name, $parameters);
        }

        return View::make('Widgets/Block', compact('block', 'content'), 'Content')->render();
    }

    protected function callBlockHandler($name, $parameters)
    {
        $instance = $this->container->make($name);

        return $this->container->call(array($instance, 'render'), $parameters);
    }

    protected function blockAllowsRendering()
    {
        $request = Request::instance();

        // Calculate the block visibility, then skip its registration if is not visible.
        if (! empty($path = $this->block->block_visibility_path)) {
            $pattern = str_replace('<front>', '/', $path);
        } else {
            $pattern = '*';
        }

        $parameters = array_filter(array_map('trim', explode("\n", $pattern)), function ($value)
        {
            return ! empty($value);
        });

        $pathMatches = call_user_func_array(array($request, 'is'), $parameters);

        if (empty($mode = $this->block->block_visibility_mode)) {
            $mode = 'show';
        }

        if ($pathMatches && ($mode == 'hide')) {
            return false;
        } else if (! $pathMatches && ($mode == 'show')) {
            return false;
        }

        $authenticated = Auth::check();

        if (empty($filter = $this->block->block_visibility_filter)) {
            $filter = 'any';
        }

        if (($filter == 'guest') && $authenticated) {
            return false;
        } else if (($filter == 'user') && ! $authenticated) {
            return false;
        }

        return true;
    }
}
