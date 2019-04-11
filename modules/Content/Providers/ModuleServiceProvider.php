<?php

namespace Modules\Content\Providers;

use Nova\Http\Request;
use Nova\Packages\Support\Providers\ModuleServiceProvider as ServiceProvider;
use Nova\Support\Facades\Cache;
use Nova\Support\Facades\Config;
use Nova\Support\Arr;

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
        'Modules\Content\Providers\PlatformServiceProvider',
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

        //
        // Conditionally register the Content Blocks.

        if ($this->app->runningInConsole() || $request->ajax() || $request->wantsJson()) {
            return;
        }

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

        // Register the Content Types.
        // $this->registerContentTypes();
    }

    /**
     * Register the Content Types.
     */
    protected function registerContentTypes()
    {
        //
        // Register the Post types.

        $config = Config::get('content.types.posts', array(
            'attachment' => array('type' => 'Modules\Content\Platform\Types\Posts\Attachment'),
            'block'      => array('type' => 'Modules\Content\Platform\Types\Posts\Block'),
            'customLink' => array('type' => 'Modules\Content\Platform\Types\Posts\CustomLink'),
            'menuItem'   => array('type' => 'Modules\Content\Platform\Types\Posts\MenuItem'),
            'page'       => array('type' => 'Modules\Content\Platform\Types\Posts\Page'),
            'post'       => array('type' => 'Modules\Content\Platform\Types\Posts\Post'),
        ));

        array_walk($config, function ($data)
        {
            $className = Arr::get($data, 'type');

            PostType::register(
                $className, Arr::get($data, 'options', array())
            );
        });

        //
        // Register the Taxonomy types.

        $config = Config::get('content.types.taxonomies', array(
            'category' => array('type' => 'Modules\Content\Platform\Types\Taxonomies\Category'),
            'menu'     => array('type' => 'Modules\Content\Platform\Types\Taxonomies\Menu'),
            'tag'      => array('type' => 'Modules\Content\Platform\Types\Taxonomies\Tag'),
        ));

        array_walk($config, function ($data)
        {
            $className = Arr::get($data, 'type');

            TaxonomyType::register(
                $className, Arr::get($data, 'options', array())
            );
        });
    }

    /**
     * Register the Content Blocks to the Widgets Manager.
     */
    protected function registerContentBlocks()
    {
        $blocks = Cache::remember('content.blocks', 1440, function ()
        {
            return Block::where('status', 'publish')->get();
        });

        foreach ($blocks as $block) {
            $position = $block->block_widget_position ?: 'content';

            $name = sprintf('content.block.%s', $block->name);

            Widget::register(
                'Modules\Content\Widgets\BlockHandler', $name, $position, $block->menu_order, array($this->app, $block)
            );
        }
    }
}
