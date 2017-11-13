<section class="content-header">
    <h1><?= $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('content', 'Dashboard'); ?></a></li>
        <li><?= __d('content', 'Menus'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="row">

<div class="col-md-4">

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Create a new Menu'); ?></h3>
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
        <a class="btn btn-success col-sm-6 pull-right" href="<?= site_url('admin/users/create'); ?>"><?= __d('users', 'Add new Menu'); ?></a>
    </div>
</div>

</div>

<div class="col-md-8">

<div class="box box-default">
    <div class="box-header">
        <h3 class="box-title"><?= __d('content', 'The registered {0}', $title); ?></h3>
        <div class="box-tools">
        <?= $menus->links(); ?>
        </div>
    </div>
    <div class="box-body no-padding">
        <?php $deletables = 0; ?>
        <?php if (! $menus->isEmpty()) { ?>
        <table id="left" class="table table-striped table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: left; vertical-align: middle;"><?= __d('content', 'Name'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('content', 'Slug'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('content', 'Count'); ?></th>
                <th style="text-align: right; vertical-align: middle;"><?= __d('content', 'Operations'); ?></th>
            </tr>
            <?php foreach ($menus as $menu) { ?>
            <tr>
                <td style="text-align: left; vertical-align: middle;" title="<?= $menu->description ?: __d('content', 'No description'); ?>" width="40%"><?= $menu->name; ?></td>
                <td style="text-align: left; vertical-align: middle;" width="35%"><?= $menu->slug; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="5%"><?= $menu->count; ?></td>
                <td style="text-align: right; vertical-align: middle;" width="20%">
                    <div class="btn-group" role="group" aria-label="...">
                        <a class="btn btn-sm btn-danger" href="#" data-toggle="modal" data-target="#modal-delete-dialog" data-id="<?= $menu->id; ?>" title="<?= __d('content', 'Delete this Menu'); ?>" role="button"><i class="fa fa-remove"></i></a>
                        <a class="btn btn-sm btn-success" href="<?= site_url('admin/menus/' .$menu->id .'/edit'); ?>" title="<?= __d('content', 'Edit this Menu'); ?>" role="button"><i class="fa fa-pencil"></i></a>
                        <a class="btn btn-sm btn-warning" href="<?= site_url('admin/menus/' .$menu->id); ?>" title="<?= __d('content', 'Manage the Items on this Menu'); ?>" target="_blank" role="button"><i class="fa fa-list"></i></a>
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
