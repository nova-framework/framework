<?php

foreach ($items as $item) {
    if (! empty($children = $item['children'])) {
        $data = array(
            'items'   => $children,
            'siteUrl' => $siteUrl,
            'liClass' => 'dropdown-submenu',
            'caret'   => false,
        );
?>
<li<?= isset($liClass) ? sprintf(' class="%s"', $liClass) : ''; ?>>
    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?= $item['title']; ?> <?= $caret ? '<span class="caret"></span>' : ''; ?></a>
    <ul class="dropdown-menu">
        <?= View::fetch('Widgets/MainMenuItems', $data, 'Content'); ?>
    </ul>
</li>
<?php } else { ?>
<li<?= ($siteUrl == $item['url']) ? ' class="active"' : ''; ?>>
    <a href="<?= $item['url']; ?>"><?= $item['title']; ?></a>
</li>
<?php } ?>
<?php } ?>
