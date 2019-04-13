<?php

namespace Modules\Content\Providers;

use Nova\Http\Request;
use Nova\Packages\Support\Providers\ModuleServiceProvider as ServiceProvider;
use Nova\Support\Facades\Cache;
use Nova\Support\Facades\Config;
use Nova\Support\Arr;

use Shared\Support\Facades\Widget;

use Modules\Content\Models\Block;
use Modules\Content\Support\Facades\ContentLabel;
use Modules\Content\Support\Facades\PostType;
use Modules\Content\Support\Facades\TaxonomyType;


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
        $this->package('Modules/Content', 'content', $path);

        // Bootstrap the Package.
        $path = $path .DS .'Bootstrap.php';

        $this->bootstrapFrom($path);

        //
        // Conditionally register the Content Blocks.

        $disableBlocks = $request->ajax() || $request->wantsJson();

        if (! $this->app->runningInConsole() && ! $disableBlocks) {
            $this->registerContentBlocks();
        }
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

        // Register the Content Types.
        $this->registerPostTypes();

        $this->registerTaxonomyTypes();
    }

    /**
     * Register the Post Types.
     */
    protected function registerPostTypes()
    {
        $config = Config::get('content.types.posts', array(
            'attachment'    => array('type' => 'Modules\Content\Platform\Types\Posts\Attachment'),
            'block'         => array('type' => 'Modules\Content\Platform\Types\Posts\Block'),
            'nav_menu_item' => array('type' => 'Modules\Content\Platform\Types\Posts\MenuItem'),
            'page'          => array('type' => 'Modules\Content\Platform\Types\Posts\Page'),
            'post'          => array('type' => 'Modules\Content\Platform\Types\Posts\Post'),
        ));

        foreach ($config as $name => $data) {
            $type = Arr::get($data, 'type');

            PostType::register(
                $type, Arr::get($data, 'options', array())
            );

            ContentLabel::register($name, $type);
        }

        ContentLabel::register('custom', function ()
        {
            return array(
                'name'  => __d('content', 'Custom Link'),
                'title' => __d('content', 'Custom Links'),
            );
        });
    }

    /**
     * Register the Taxonomy Types.
     */
    protected function registerTaxonomyTypes()
    {
        $config = Config::get('content.types.taxonomies', array(
            'category' => array('type' => 'Modules\Content\Platform\Types\Taxonomies\Category'),
            'nav_menu' => array('type' => 'Modules\Content\Platform\Types\Taxonomies\Menu'),
            'post_tag' => array('type' => 'Modules\Content\Platform\Types\Taxonomies\Tag'),
        ));

        foreach ($config as $name => $data) {
            $type = Arr::get($data, 'type');

            TaxonomyType::register(
                $type, Arr::get($data, 'options', array())
            );

            ContentLabel::register($name, $type);
        }
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
