<?php $children = Arr::get($item, 'children', array()); ?>
<?php if (! empty($children)) { ?>
<li class="dropdown <?= $item['active'] ? 'active' : ''; ?>">
    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
        <i class="fa fa-<?= $item['icon'] ?>"></i> <span><?= $item['title']; ?></span> <span  class="caret"></span>
    </a>
    <ul class="dropdown-menu" role="menu">
    <?php foreach ($children as $child) { ?>
        <?= View::partial('Partials/Frontend/NavbarMenuItems', 'AdminLite', array('item' => $child)); ?>
    <?php } ?>
    </ul>
</li>
<?php } else if ($item['url'] !== '#') { ?>
<li <?= $item['active'] ? "class='active'" : ""; ?>>
    <a href="<?= $item['url']; ?>"><i class="fa fa-<?= $item['icon'] ?>"></i> <span><?= $item['title']; ?></span></a>
</li>
<?php } ?>
