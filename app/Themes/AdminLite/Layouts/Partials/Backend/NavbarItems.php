<?php $children = Arr::get($item, 'children', array()); ?>
<?php if (! empty($children) || isset($item['content'])) { ?>
<li class="dropdown <?= $item['active'] ? 'active' : ''; ?> <?= isset($item['class']) ? $item['class'] : ''; ?>">
    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
        <i class="fa fa-<?= $item['icon'] ?>"></i> <span><?= $item['title']; ?></span>
        <?php if (isset($item['label']) && is_array($item['label'])) { ?>
        <?php list ($class, $count) = $item['label']; ?>
        <span class="label label-<?= $class; ?>" <?= ($count === 0) ? 'style="display: none;"' : ''; ?>><?= $count; ?></span>
        <?php } else if (! empty($children)) { ?>
        <span class="caret"></span>
        <?php } ?>
    </a>
    <ul class="dropdown-menu" role="menu">
    <?php if (isset($item['content'])) { ?>
        <?= $item['content']; ?>
    <?php } else { ?>
    <?php foreach ($children as $child) { ?>
        <?= View::partial('Partials/Frontend/NavbarItems', 'AdminLite', array('item' => $child)); ?>
    <?php } ?>
    <?php } ?>
    </ul>
</li>
<?php } else if ($item['url'] !== '#') { ?>
<li <?= $item['active'] ? "class='active'" : ""; ?>>
    <a href="<?= $item['url']; ?>">
        <i class="fa fa-<?= $item['icon'] ?>"></i> <span><?= $item['title']; ?></span>
    </a>
</li>
<?php } ?>
