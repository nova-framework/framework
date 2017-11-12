<section class="content-header">
    <h1><?= $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('content', 'Dashboard'); ?></a></li>
        <li><?= __d('content', 'Content'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<?php if (! isset($simple)) { ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Create a new {0}', $name); ?></h3>
    </div>
    <div class="box-body">
        <a class="btn btn-success col-sm-2 pull-right" href="<?= site_url('admin/content/create/' .$type); ?>"><?= __d('content', 'Create a new {0}', $name); ?></a>
    </div>
</div>

<?php } ?>

<div class="box box-default">
    <div class="box-header">
        <h3 class="box-title"><?= __d('content', 'The registered {0}', $title); ?></h3>
        <div class="box-tools">
        <?= $posts->links(); ?>
        </div>
    </div>
    <div class="box-body no-padding">
        <?php $deletables = 0; ?>
        <?php if (! $posts->isEmpty()) { ?>
        <table id="left" class="table table-striped table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: center; vertical-align: middle;"><?= __d('content', 'ID'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('content', 'Title'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('content', 'Author'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('content', 'Status'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('content', 'Updated At'); ?></th>
                <th style="text-align: right; vertical-align: middle;"><?= __d('content', 'Operations'); ?></th>
            </tr>
            <?php foreach ($posts as $post) { ?>
            <tr>
                <td style="text-align: center; vertical-align: middle;" width="5%"><?= $post->id; ?></td>
                <td style="text-align: left; vertical-align: middle;" width="41%" title="<?= $post->slug; ?>"><?= $post->title; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="15%"><?= $post->author->username; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="12%"><?= Arr::get($statuses, $post->status, __d('content', 'Unknown ({0})', $post->status)); ?></td>
                <td style="text-align: center; vertical-align: middle;" width="12%"><?= $post->updated_at->formatLocalized(__d('content', '%d %b %Y, %R')); ?></td>
                <td style="text-align: right; vertical-align: middle;" width="15%">
                    <div class="btn-group" role="group" aria-label="...">
                        <a class="btn btn-sm btn-danger" href="#" data-toggle="modal" data-target="#modal-delete-dialog" data-id="<?= $post->id; ?>" title="<?= __d('content', 'Delete this Post'); ?>" role="button"><i class="fa fa-remove"></i></a>
                        <a class="btn btn-sm btn-success" href="<?= site_url('admin/content/' .$post->id .'/edit'); ?>" title="<?= __d('content', 'Edit this Post'); ?>" role="button"><i class="fa fa-pencil"></i></a>
                        <a class="btn btn-sm btn-warning" href="<?= site_url('content/' .$post->slug); ?>" title="<?= __d('content', 'View the Details'); ?>" target="_blank" role="button"><i class="fa fa-search"></i></a>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
        <div class="alert alert-warning" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-warning"></i> <?= strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __d('content', 'No registered Posts'); ?></h4>
            <?= __d('content', 'There are no registered Posts.'); ?>
        </div>
        <?php } ?>
    </div>
</div>

</section>