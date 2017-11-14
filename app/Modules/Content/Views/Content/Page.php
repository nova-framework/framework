<section class="page-header">
    <h1><?= __d('nodes', $title); ?></h1>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<?= $post->content; ?>

<?php if (Auth::user()->hasRole('administrator')) { ?>
<hr>
<a class="btn btn-sm btn-success pull-right" href="<?= site_url('admin/content/' .$post->id .'/edit'); ?>" title="<?= __d('content', 'Edit this Post'); ?>" role="button"><i class="fa fa-pencil"></i> <?= __d('content', 'Edit'); ?></a>
<?php } ?>

<div class="clearfix"></div>

</div>
