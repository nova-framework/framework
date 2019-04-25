<?php

namespace Modules\Content\Controllers\Admin;

use Nova\Http\Request;
use Nova\Support\Facades\Cache;
use Nova\Support\Facades\Redirect;

use Modules\Content\Models\Block;
use Modules\Platform\Controllers\Admin\BaseController;


class Blocks extends BaseController
{

    public function index()
    {
        $blocks = Block::where('status', 'publish')->get();

        $positions = $blocks->groupBy(function ($item, $key)
        {
            return $item->block_widget_position;
        });

        return $this->createView()
            ->shares('title', __d('content', 'Widget Positions'))
            ->with('positions', $positions);
    }

    public function order(Request $request)
    {
        $position = $request->get('position');

        $items = json_decode(
            $request->get('items')
        );

        foreach ($items as $order => $item) {
            $block = Block::find($item->id);

            if (! is_null($block)) {
                $block->menu_order = $order;

                $block->save();
            }
        }

        // Invalidate the cached menu data.
        Cache::forget('content.blocks');

        return Redirect::back()
            ->with('success', __d('content', 'The Blocks order, from the position <b>{0}</b>, was successfully updated.', $position));
    }
}
