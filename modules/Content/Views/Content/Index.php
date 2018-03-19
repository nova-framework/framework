<section class="page-header" style="margin-bottom: 10px;">
    <h1><?= __d('content', $title); ?></h1>
</section>

<!-- Main content -->
<section class="content">

<?= View::fetch('Partials/Messages'); ?>

<?php $hasSidebar = ! Widget::isEmptyPosition('content-posts-sidebar'); ?>

<div class="row">

<div class="col-md-<?= $hasSidebar ? 9 : 12; ?>">

<?php if (! $posts->isEmpty()) { ?>
<?php foreach ($posts as $post) { ?>
<?php $thumbnail = isset($post->thumbnail) && isset($post->thumbnail->attachment) ? site_url('content/media/serve/' .$post->thumbnail->attachment->name) .'?s=220' : ''; ?>

<h3><strong><?= $post->title; ?></strong></h3>
<hr style="margin-bottom: 10px;">
<?php $format = __d('content', '%d %b %Y'); ?>
<div class="pull-left"><?= __d('content', '{0}, by <b>{1}</b>', $post->updated_at->formatLocalized($format), $post->author->realname); ?></div>

<?php $categories = $post->taxonomies->where('taxonomy', 'category'); ?>
<?php if (! $categories->isEmpty()) { ?>
<?php $count = 0; ?>
<div class="pull-right" style="font-weight: bold;"><span class="fa fa-folder-open-o"></span>
<?php ob_start(); ?>
<?php foreach ($categories as $category) { ?>
<?= ($count > 0) ? ', ' : ''; ?><a href="<?= site_url('content/category/' .$category->slug); ?>"><?= $category->name; ?></a>
<?php $count++; ?>
<?php } ?>
<?= preg_replace('~>\s,\s<~m', '>, <', ob_get_clean()); ?>
</div>
<?php } ?>

<div class="clearfix"></div>
<hr style="margin-top: 10px;">
<?php if (! empty($thumbnail)) { ?>
<div class="clearfix pull-left" style="margin: 0 20px 20px 0;"><img class="img-responsive img-thumbnail" src="<?= $thumbnail; ?>"></div>
<?php } ?>
<div style="text-align: justify;">
<?= preg_replace("/^(.*)<!--more-->(.*)$/sm", "$1", $post->getContent()); ?>
</div>
<div class="clearfix"></div>

<hr style="margin-bottom: 10px;">

<?php $tags = $post->taxonomies->where('taxonomy', 'post_tag'); ?>
<?php if (! $tags->isEmpty()) { ?>
<?php $count = 0; $html = ''; ?>
<div class="pull-left"><i class="fa fa-tags"></i>
<?php ob_start(); ?>
<?php foreach ($tags as $tag) { ?>
<?= ($count > 0) ? ', ' : ''; ?><a href="<?= site_url('content/tag/' .$tag->slug); ?>"><?= $tag->name; ?></a>
<?php $count++; ?>
<?php } ?>
<?= preg_replace('~>\s,\s<~m', '>, <', ob_get_clean()); ?>
</div>
<?php } ?>

<a class="btn btn-xs btn-default col-md-2 pull-right" href="<?= site_url('content/' .$post->name); ?>" title="<?= __d('content', 'View this Post'); ?>" role="button"><?= __d('content', 'Read more ...'); ?></a>

<div class="clearfix"></div>
<br>
<br>

<?php } ?>

<?php if (! empty($paginator = $posts->links())) { ?>
<hr style="margin-bottom: 0;">
<?= $paginator; ?>
<?php } ?>

<?php } else { ?>
<p><?= __d('content', 'No posts found.'); ?></p>
<?php } ?>

<br>

</div>

<?php if ($hasSidebar) { ?>
<div class="posts-sidebar col-md-3">
<?= Widget::position('content-posts-sidebar'); ?>
</div>
<?php } ?>

<div class="clearfix"></div>
</div>

</section>
