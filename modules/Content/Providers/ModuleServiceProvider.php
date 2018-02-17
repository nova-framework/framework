<?php

namespace Modules\Content\Providers;

use Nova\Http\Request;
use Nova\Modules\Support\Providers\ModuleServiceProvider as ServiceProvider;
use Nova\Support\Facades\Cache;

use Shared\Support\Facades\Widget;

use Modules\Content\Models\Block;


class ModuleServiceProvider extends ServiceProvider
{
    /**
     * The additional provider class names.
     *
     * @var array
     */
    protected $providers = array(
        'Modules\Content\Providers\AuthServiceProvider',
        'Modules\Content\Providers\EventServiceProvider',
        'Modules\Content\Providers\RouteServiceProvider',
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
        //$this->package('Modules/Content', 'content', $path);

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

        // Configure the Package.
        $path = realpath(__DIR__ .'/../');

        $this->package('Modules/Content', 'content', $path);
    }

    /**
     * Register the Content Blocks to the Widgets Manager.
     */
    protected function registerContentBlocks(Request $request)
    {
        if ($this->app->runningInConsole() || $request->ajax() || $request->wantsJson()) {
            return;
        }

        $blocks = Cache::remember('content.blocks', 1440, function ()
        {
            return Block::where('status', 'publish')->get();
        });

        foreach ($blocks as $block) {
            $position = $block->block_widget_position ?: 'content';

            $name = 'content.block.' .$block->name;

            Widget::register(
                'Modules\Content\Widgets\BlockHandler', $name, $position, $block->menu_order, array($this->app, $block)
            );
        }
    }
}
