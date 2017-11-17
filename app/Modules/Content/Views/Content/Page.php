<section class="page-header">
    <h1><?= __d('nodes', $title); ?></h1>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<?php $thumbnail = isset($post->thumbnail) && isset($post->thumbnail->attachment) ? site_url('content/media/serve/' .$post->thumbnail->attachment->name) .'?s=270' : ''; ?>
<?php if (! empty($thumbnail)) { ?>
<div class="clearfix pull-left" style="margin: 0 20px 20px 0;"><img class="img-responsive img-thumbnail" src="<?= $thumbnail; ?>"></div>
<?php } ?>

<?php if (($post->status == 'password') && ! Session::has('content-unlocked-post-' .$post->id)) { ?>
<?= View::fetch('Partials/ProtectedContent', compact('post'), 'Content'); ?>
<?php } else { ?>
<?= $post->getContent(); ?>
<?php } ?>


<?php if ($post->type == 'revision') { ?>
<hr style="margin-bottom: 10px;">
<?php $date = $post->created_at->formatLocalized(__d('content', '%d %b %Y, %R')); ?>
<?= __d('content', 'Revision created at <b>{0}</b>, by <b>{1}</b>', $date, $post->author->username); ?>
<?php } else if (Auth::user()->hasRole('administrator')) { ?>
<hr style="margin-bottom: 10px;">
<a class="btn btn-sm btn-success pull-right" href="<?= site_url('admin/content/' .$post->id .'/edit'); ?>" title="<?= __d('content', 'Edit this Post'); ?>" role="button"><i class="fa fa-pencil"></i> <?= __d('content', 'Edit'); ?></a>
<div class="clearfix"></div>
<br>
<?php } ?>

<div class="clearfix"></div>
<br>

</section>
