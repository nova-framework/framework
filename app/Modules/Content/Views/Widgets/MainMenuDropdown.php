<?php

use App\Modules\Content\Models\Post;
use App\Modules\Content\Widgets\MainMenu;

foreach ($items as $item) {
    $type = $item->menu_item_type;

    if ($type == 'custom') {
        $title = $item->title;

        $url = $item->menu_item_url;
    }

    // The item is not a Custom Link.
    else {
        $instance = $item->instance();

        if (($type == 'post') || ($type == 'page')) {
            $title = $instance->title;

            $url = site_url('content/' .$instance->name);
        } else if ($type == 'taxonomy') {
            $title = $instance->name;

            $url = site_url('content/category/' .$instance->slug);
        }
    }

    $item->load('children');

    $children = $item->children;

    MainMenu::sortItems($children);

    if ($children->isEmpty()) { ?>
<li <?= ($siteUrl == $url) ? 'class="active"' : ''; ?>>
    <a href="<?= $url; ?>"><?= $title; ?></a>
</li>
    <?php } else { ?>
<li class="dropdown-submenu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?= $title; ?></a>
    <ul class="dropdown-menu">
    <?= View::fetch('Widgets/MainMenuDropdown', array('menu' => $menu, 'items' => $children, 'siteUrl' => $siteUrl), 'Content'); ?>
    </ul>
</li>
<?php } ?>
<?php } ?>
