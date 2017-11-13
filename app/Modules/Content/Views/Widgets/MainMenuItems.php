<?php

use App\Modules\Content\Widgets\MainMenu;

foreach ($items as $item) {
    list ($title, $url, $children) = MainMenu::handleItem($item);

    if (! $children->isEmpty()) {
        $data = array(
            'menu'    => $menu,
            'items'   => $children,
            'siteUrl' => $siteUrl,
            'liClass' => 'dropdown-submenu',
            'caret'   => false,
        );
?>
<li<?= isset($liClass) ? sprintf(' class="%s"', $liClass) : ''; ?>>
    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?= $title; ?><?= $caret ? ' <span class="caret"></span>' : ''; ?></a>
    <ul class="dropdown-menu">
        <?= View::fetch('Widgets/MainMenuItems', $data, 'Content'); ?>
    </ul>
</li>
<?php } else { ?>
<li<?= ($siteUrl == $url) ? ' class="active"' : ''; ?>>
    <a href="<?= $url; ?>"><?= $title; ?></a>
</li>
<?php } ?>
<?php } ?>
