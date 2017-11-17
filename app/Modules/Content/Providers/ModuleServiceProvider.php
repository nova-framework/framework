<?php

namespace App\Modules\Content\Providers;

use Nova\Http\Request;
use Nova\Modules\Support\Providers\ModuleServiceProvider as ServiceProvider;
use Nova\Support\Facades\Cache;

use Shared\Support\Facades\Widget;

use App\Modules\Content\Models\Post;


class ModuleServiceProvider extends ServiceProvider
{
    /**
     * The additional provider class names.
     *
     * @var array
     */
    protected $providers = array(
        'App\Modules\Content\Providers\AuthServiceProvider',
        'App\Modules\Content\Providers\EventServiceProvider',
        'App\Modules\Content\Providers\RouteServiceProvider',
    );


    /**
     * Bootstrap the Application Events.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        $path = realpath(__DIR__ .'/../');

        // Configure the Package.
        $this->package('Modules/Content', 'content', $path);

        // Bootstrap the Package.
        $path = $path .DS .'Bootstrap.php';

        $this->bootstrapFrom($path);

        // Register the Content Blocks.
        $this->registerContentBlocks($request);
    }

    /**
     * Register the Content module Service Provider.
     *
     * This service provider is a convenient place to register your modules
     * services in the IoC container. If you wish, you may make additional
     * methods or service providers to keep the code more focused and granular.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        //
    }

    /**
     * Register the Content Blocks to the Widgets Manager.
     */
    protected function registerContentBlocks(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return;
        }

        $blocks = Cache::remember('content.blocks', 1440, function ()
        {
            return Post::where('type', 'block')->where('status', 'publish')->get();
        });

        foreach ($blocks as $block) {
            $position = $block->block_widget_position ?: 'content';

            // Calculate the block visibility, then skip its registration if is not visible.
            if (! empty($path = $block->block_visibility_path)) {
                $pattern = str_replace('<front>', '/', $path);
            } else {
                $pattern = '*';
            }

            $parameters = array_filter(array_map('trim', explode("\n", $pattern)), function ($value)
            {
                return ! empty($value);
            });

            $pathMatches = call_user_func_array(array($request, 'is'), $parameters);

            if (empty($mode = $block->block_visibility_mode)) {
                $mode = 'show';
            }

            if (($pathMatches && ($mode == 'hide')) || (! $pathMatches && ($mode == 'show'))) {
                continue;
            }

            $parameters = array($block);

            Widget::register(
                'App\Modules\Content\Widgets\Block', 'content.block.' .$block->name, $position, $block->order, $parameters
            );
        }
    }
}
