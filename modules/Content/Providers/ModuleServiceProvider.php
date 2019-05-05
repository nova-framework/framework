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
     * The default content types.
     */
    protected $contentTypes = array(
        'posts' => array(
            'attachment'    => 'Modules\Content\Platform\Types\Posts\Attachment',
            'block'         => 'Modules\Content\Platform\Types\Posts\Block',
            'nav_menu_item' => 'Modules\Content\Platform\Types\Posts\MenuItem',
            'page'          => 'Modules\Content\Platform\Types\Posts\Page',
            'post'          => 'Modules\Content\Platform\Types\Posts\Post',
        ),
        'taxonomies' => array(
            'category' => 'Modules\Content\Platform\Types\Taxonomies\Category',
            'nav_menu' => 'Modules\Content\Platform\Types\Taxonomies\Menu',
            'post_tag' => 'Modules\Content\Platform\Types\Taxonomies\Tag',
        ),
    );


    /**
     * Bootstrap the Application Events.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__ .'/../');

        // Configure the Package.
        $this->package('Modules/Content', 'content', $path);

        // Bootstrap the Package.
        $path = $path .DS .'Bootstrap.php';

        $this->bootstrapFrom($path);

        //
        // Register the Content Blocks.

        $this->registerContentBlocks();
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
        $this->registerPostTypes();

        $this->registerTaxonomyTypes();

        $this->registerContentLabels();
    }

    /**
     * Register the Post Types.
     */
    protected function registerPostTypes()
    {
        $config = $this->getContentTypesConfig('posts');

        foreach ($config as $name => $options) {
            $type = Arr::pull($options, 'uses');

            //
            PostType::register($type, $options);

            ContentLabel::register($name, $type);
        }
    }

    /**
     * Register the Taxonomy Types.
     */
    protected function registerTaxonomyTypes()
    {
        $config = $this->getContentTypesConfig('taxonomies');

        foreach ($config as $name => $options) {
            $type = Arr::pull($options, 'uses');

            //
            TaxonomyType::register($type, $options);

            ContentLabel::register($name, $type);
        }
    }

    /**
     * Register the additional Content Labels.
     */
    protected function registerContentLabels()
    {
        //
        // The custom links uses a pseudo-type called 'custom' on the Menu Items.

        ContentLabel::register('custom', function ()
        {
            return array(
                'name'  => __d('content', 'Custom Link'),
                'title' => __d('content', 'Custom Links'),
            );
        });
    }

    /**
     * Register the Content Blocks to the Widgets Manager.
     */
    protected function registerContentBlocks()
    {
        if ($this->app->runningInConsole()) {
            return;
        }

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

    /**
     * Returns the configured content types from the specified family.
     *
     * @param  string  $family
     * @return array
     */
    protected function getContentTypesConfig($family)
    {
        $config = Arr::get($this->contentTypes, $family, array());

        //
        $options = array_replace_recursive($config, Config::get("content.types.{$family}", array()));

        return array_filter(array_map(function ($option)
        {
            if (! is_string($option)) {
                return $option;
            }

            return array('uses' => $option);

        }, $options), function ($option)
        {
            if (! is_array($option)) {
                return false;
            }

            return ! empty($value = Arr::get($option, 'uses')) && is_string($value);
        });
    }
}
