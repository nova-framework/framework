<section class="page-header" style="margin-bottom: 10px;">
    <h1><?= __d('nodes', $title); ?></h1>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<?php $hasSidebar = ! Widget::isEmptyPosition('content.posts.sidebar'); ?>

<?php $userIsAdmin = ! is_null($user = Auth::user()) && $user->hasRole('administrator'); ?>

<div class="row">

<div class="col-md-<?= $hasSidebar ? 9 : 12; ?>" style="padding-bottom: 40px;">

<?php $thumbnail = isset($post->thumbnail) && isset($post->thumbnail->attachment) ? site_url('content/media/serve/' .$post->thumbnail->attachment->name) .'?s=270' : ''; ?>

<?php $format = __d('content', '%d %b %Y'); ?>
<div class="pull-left"><?= __d('content', '{0}, by <b>{1}</b>', $post->updated_at->formatLocalized($format), $post->author->realname()); ?></div>

<?php $categories = $post->taxonomies->where('taxonomy', 'category'); ?>
<?php if (! $categories->isEmpty()) { ?>
<?php $count = 0; ?>
<div class="pull-right" style="font-weight: bold;">
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

<?php $comments = $post->comments->where('approved', '1'); ?>

<?php if (($comments->count() > 0) || ($post->comment_status == 'open')) { ?>
<h3><?= __d('content', 'Comments'); ?></h3>
<hr>
<?php } ?>

<?php if ($comments->count() > 0) { ?>
<ul>
    <?php foreach($comments as $comment) { ?>
    <li>
        <a rel="nofollow" style="font-weight: bold;" target="_blank" href="<?= urlencode($comment->author_url); ?>"><?= e($comment->author); ?></a>
        <div class="comment-body" style="margin-top: 10px; margin-bottom: 25px;">
        <?= e($comment->content); ?>
        </div>
    </li>
    <?php } ?>
</ul>
<?php } else if ($post->comment_status == 'open') { ?>
<?= __d('content', 'Be the first to comment.'); ?>
<?php } ?>

<div class="clearfix"></div>
<br>
<br>

<?php if ($post->comment_status == 'open') { ?>

<h3><?= __d('content', 'Leave a Reply'); ?></h3>
<hr>

<div class="col-md-8 col-md-offset-2">

<form action="<?= site_url('content/' .$post->id .'/comment'); ?>" method="POST">

    <?= csrf_field() ?>
    <input type="hidden" name="post_id" value="<?= $post->id; ?>" />

    <div class="form-group<?= $errors->has('comment_author') ? ' has-error' : ''; ?>">
        <label for="comment_author"><?= __d('content', 'Name'); ?> *</label> <br/>
        <input type="text" name="comment_author" class="form-control" value="<?= Input::old('comment_author'); ?>" />

        <?php if ($errors->has('comment_author')) { ?>
            <span class="help-block">
                <strong><?= $errors->first('comment_author'); ?></strong>
            </span>
        <?php } ?>
    </div>
    <div class="form-group<?= $errors->has('comment_author_email') ? ' has-error' : ''; ?>">
        <label for="comment_author_email"><?= __d('content', 'Email Address'); ?> *</label> <br/>
        <input type="text" name="comment_author_email" class="form-control" value="<?= Input::old('comment_author_email'); ?>" />

        <?php if ($errors->has('comment_author_email')) { ?>
            <span class="help-block">
                <strong><?= $errors->first('comment_author_email'); ?></strong>
            </span>
        <?php } ?>
    </div>
    <div class="form-group">
        <label for="comment_author_url"><?= __d('content', 'Website'); ?></label> <br/>
        <input type="text" name="comment_author_url" class="form-control" value="<?= Input::old('comment_author_url'); ?>" />
    </div>
    <div class="form-group">
        <label for="comment_content"><?= __d('content', 'Message'); ?></label> <br/>
        <textarea cols="60" rows="6" class="form-control" name="comment_content"><?= Input::old('comment_content'); ?></textarea>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary pull-right col-md-3" value="<?= __d('content', 'Submit Comment'); ?>" />
    </div>
</form>

</div>

<?php } ?>

<div class="clearfix"></div>

</div>

<?php if ($hasSidebar) { ?>

<div class="col-md-3">

<?= Widget::position('content.posts.sidebar'); ?>

</div>

<?php } ?>

<div class="clearfix"></div>

</div>

</section>
