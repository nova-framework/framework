<?php $children = Arr::get($item, 'children', array()); ?>
<?php if (! empty($children)) { ?>
<li class="treeview <?= $item['active'] ? 'active' : ''; ?>">
    <a href="#"><i class="fa fa-<?= $item['icon'] ?>"></i> <span><?= $item['title']; ?></span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
    <?php foreach ($children as $child) { ?>
        <?= View::partial('Partials/Backend/SidebarItems', 'AdminLite', array('item' => $child)); ?>
    <?php } ?>
    </ul>
</li>
<?php } else if ($item['url'] !== '#') { ?>
<li <?= $item['active'] ? "class='active'" : ""; ?>>
    <a href="<?= $item['url']; ?>"><i class="fa fa-<?= $item['icon'] ?>"></i> <span><?= $item['title']; ?></span></a>
</li>
<?php } ?>
