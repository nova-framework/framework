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

<div class="row">

<div class="col-md-4">

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Create a new {0}', $name); ?></h3>
    </div>
    <div class="box-body">
        <div class="form-group">
            <label class="control-label" for="name"><?= __d('content', 'Name'); ?></label>
            <input name="name" id="name" type="text" class="form-control" value="<?= Input::old('name'); ?>" placeholder="<?= __d('content', 'Name'); ?>">
        </div>
        <div class="form-group">
            <label class="control-label" for="slug"><?= __d('content', 'Slug'); ?></label>
            <input name="slug" id="slug" type="text" class="form-control" value="<?= Input::old('slug'); ?>" placeholder="<?= __d('content', 'Slug'); ?>">
        </div>
        <div class="form-group" style=" margin-bottom: 0;">
            <label class="control-label" for="description"><?= __d('content', 'Description'); ?></label>
            <textarea name="description" id="description" class="form-control" rows="8" style="resize: none;" placeholder="<?= __d('content', 'Description'); ?>"><?= Input::old('description'); ?></textarea>
        </div>
    </div>
    <div class="box-footer">
        <a class="btn btn-success col-sm-6 pull-right" href="<?= site_url('admin/users/create'); ?>"><?= __d('users', 'Add new {0}', $name); ?></a>
    </div>
</div>

</div>

<div class="col-md-8">

<div class="box box-default">
    <div class="box-header">
        <h3 class="box-title"><?= __d('content', 'The registered {0}', $title); ?></h3>
        <div class="box-tools">
        <?= $models->links(); ?>
        </div>
    </div>
    <div class="box-body no-padding">
        <?php $deletables = 0; ?>
        <?php if (! $models->isEmpty()) { ?>
        <table id="left" class="table table-striped table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: left; vertical-align: middle;"><?= __d('content', 'Name'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('content', 'Slug'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('content', 'Count'); ?></th>
                <th style="text-align: right; vertical-align: middle;"><?= __d('content', 'Operations'); ?></th>
            </tr>
            <?php foreach ($models as $model) { ?>
            <tr>
                <td style="text-align: left; vertical-align: middle;" title="<?= $model->description ?: __d('content', 'No description'); ?>" width="40%"><?= $model->name; ?></td>
                <td style="text-align: left; vertical-align: middle;" width="35%"><?= $model->slug; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="5%"><?= $model->count; ?></td>
                <td style="text-align: right; vertical-align: middle;" width="20%">
                    <div class="btn-group" role="group" aria-label="...">
                        <a class="btn btn-sm btn-danger" href="#" data-toggle="modal" data-target="#modal-delete-dialog" data-id="<?= $model->id; ?>" title="<?= __d('content', 'Delete this {0}', $name); ?>" role="button"><i class="fa fa-remove"></i></a>
                        <a class="btn btn-sm btn-success" href="<?= site_url('admin/content/' .$model->id .'/edit'); ?>" title="<?= __d('content', 'Edit this {0}', $name); ?>" role="button"><i class="fa fa-pencil"></i></a>
                        <a class="btn btn-sm btn-warning" href="<?= site_url('admin/content/' .$type .'/' .$model->slug); ?>" title="<?= __d('content', 'View the Posts on this {0}', $name); ?>" target="_blank" role="button"><i class="fa fa-search"></i></a>
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

</div>

</div>

</section>
