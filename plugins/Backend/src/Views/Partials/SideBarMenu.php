<ul class="nav navbar-nav side-nav" id="side-menu">
	<li class="header"><?= __d('backend', 'ADMINISTRATION'); ?></li>
	<?php foreach ($menu->getItems() as $count => $item) { ?>
	<?php $children = Arr::get($item, 'children', array()); ?>
	<?php $active = $menu->itemIsActive($item); ?>
	<li <?= $active ? 'class="active"' : ''; ?>>
		<?php if (empty($children)) { ?>
		<a href="<?= $item['url']; ?>"><i class="fa fa-<?= $item['icon'] ?>"></i> <?= $item['name']; ?></a>
		<?php } else { ?>
		<a href="javascript:;" class="collapse-toggle <?= ! $active ? 'collapsed' : ''; ?>" data-toggle="collapse" data-target="#menu-children-<?= $count; ?>" aria-expanded="false" role="button">
			<i class="fa fa-<?= $item['icon'] ?>"></i> <?= $item['name']; ?>
			<span class="pull-right-container">
				<i class="fa fa-angle-left pull-right"></i>
			</span>
		</a>
		<ul id="menu-children-<?= $count; ?>" class="nav nav-second-level collapse <?= $active ? 'in' : ''; ?>">
			<?php foreach ($children as $child) { ?>
			<li <?= $menu->itemIsActive($child) ? 'class="active"' : ''; ?>>
				<a href="<?= $child['url']; ?>"><i class="fa fa-circle-o"></i> <?= $child['name']; ?></a>
			</li>
			<?php } ?>
		</ul>
		<?php } ?>
	</li>
<?php } ?>
</ul>

