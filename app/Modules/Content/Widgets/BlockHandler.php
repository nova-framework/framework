<?php

namespace App\Modules\Content\Widgets;

use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Request;
use Nova\Support\Facades\View;

use Shared\Widgets\Widget;

use App\Modules\Content\Models\Block;

use Carbon\Carbon;


class BlockHandler extends Widget
{
    /**
     * @var \App\Modules\Content\Models\Post
     */
    protected $post;


    public function __construct(Block $block)
    {
        $this->block = $block;
    }

    public function render()
    {
        if (! $this->blockAllowsRendering()) {
            return;
        }

        $content = preg_replace('/<!--\?(.*)\?-->/sm', '<?$1?>', $this->block->getContent());

        $data = array(
            'post'    => $this->block,
            'content' => $content,
        );

        return View::make('Widgets/Block', $data, 'Content')->render();
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

        $matches = call_user_func_array(array($request, 'is'), $parameters);

        if (empty($mode = $this->block->block_visibility_mode)) {
            $mode = 'show';
        }

        if (($matches && ($mode == 'hide')) || (! $matches && ($mode == 'show'))) {
            return false;
        }

        if (empty($filter = $this->block->block_visibility_filter)) {
            $filter = 'any';
        }

        $authenticated = Auth::check();

        if (($authenticated && ($filter == 'guest')) || (! $authenticated && ($filter == 'user'))) {
            return false;
        }

        return true;
    }
}
