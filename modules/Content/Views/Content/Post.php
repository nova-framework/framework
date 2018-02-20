<section class="page-header" style="margin-bottom: 10px;">
    <h1><?= __d('nodes', $title); ?></h1>
</section>

<!-- Main content -->
<section class="content">

<?= View::fetch('Partials/Messages'); ?>

<?php $hasSidebar = ! Widget::isEmptyPosition('content-posts-sidebar'); ?>

<?php $userIsAdmin = ! is_null($user = Auth::user()) && $user->hasRole('administrator'); ?>

<div class="row">

<div class="col-md-<?= $hasSidebar ? 9 : 12; ?>" style="padding-bottom: 40px;">

<?php $thumbnail = isset($post->thumbnail) && isset($post->thumbnail->attachment) ? site_url('content/media/serve/' .$post->thumbnail->attachment->name) .'?s=270' : ''; ?>

<?php $format = __d('content', '%B %d, %Y'); ?>
<div class="pull-left"><?= __d('content', '{0} by <b>{1}</b>', $post->updated_at->formatLocalized($format), $post->author->realname()); ?></div>

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
<?php if (($post->status == 'password') && ! $userIsAdmin && ! Session::has('content-unlocked-post-' .$post->id)) { ?>
<?= View::fetch('Partials/ProtectedContent', compact('post'), 'Content'); ?>
<?php } else { ?>
<?= $post->getContent(); ?>
<?php } ?>

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

<?php if ($post->type == 'revision') { ?>
<?php $date = $post->created_at->formatLocalized(__d('content', '%d %b %Y, %R')); ?>
<?= __d('content', 'Revision created at <b>{0}</b>, by <b>{1}</b>', $date, $post->author->username); ?>
<?php } else if ($userIsAdmin) { ?>
<a class="btn btn-sm btn-success pull-right" href="<?= site_url('admin/content/' .$post->id .'/edit'); ?>" title="<?= __d('content', 'Edit this Post'); ?>" role="button"><i class="fa fa-pencil"></i> <?= __d('content', 'Edit'); ?></a>
<?php } ?>

<div class="clearfix"></div>
<br>

<?= View::fetch('Partials/PostComments', compact('post'), 'Content'); ?>

</div>

<?php if ($hasSidebar) { ?>

<div class="posts-sidebar col-md-3">

<?= Widget::position('content-posts-sidebar'); ?>

</div>

<?php } ?>

<div class="clearfix"></div>

</div>

</section>
