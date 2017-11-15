<section class="page-header">
    <h1><?= __d('nodes', $title); ?></h1>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<?php $thumbnail = isset($post->thumbnail) && isset($post->thumbnail->attachment) ? site_url('content/media/serve/' .$post->thumbnail->attachment->name) .'?s=360' : ''; ?>
<?php if (! empty($thumbnail)) { ?>
<div class="clearfix pull-left" style="margin: 0 20px 20px 0;"><img class="img-responsive img-thumbnail" src="<?= $thumbnail; ?>"></div>
<?php } ?>

<div style="text-align: justify;">
<?= $post->getContent(); ?>
</div>

<?php if (Auth::user()->hasRole('administrator')) { ?>
<hr style="margin-bottom: 10px;">
<a class="btn btn-sm btn-success pull-right" href="<?= site_url('admin/content/' .$post->id .'/edit'); ?>" title="<?= __d('content', 'Edit this Post'); ?>" role="button"><i class="fa fa-pencil"></i> <?= __d('content', 'Edit'); ?></a>
<div class="clearfix"></div>
<br>
<?php } ?>

<div class="clearfix"></div>
<br>

</div>
