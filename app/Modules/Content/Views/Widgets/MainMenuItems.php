<?php

use App\Modules\Content\Widgets\MainMenu;

foreach ($items as $item) {
    list ($title, $url, $children) = MainMenu::handleItem($item);

    if ($children->isEmpty()) {
?>
<li <?= ($siteUrl == $url) ? 'class="active"' : ''; ?>>
    <a href="<?= $url; ?>"><?= $title; ?></a>
</li>
<?php } else { ?>
<li>
    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?= $title; ?> <span class="caret"></span></a>
    <ul class="dropdown-menu multi-level">
    <?= View::fetch('Widgets/MainMenuDropdown', array('menu' => $menu, 'items' => $children, 'siteUrl' => $siteUrl), 'Content'); ?>
    </ul>
</li>
<?php } ?>
<?php } ?>
