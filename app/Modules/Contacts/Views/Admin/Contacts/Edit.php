<section class="content-header">
    <h1><?= $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('contacts', 'Dashboard'); ?></a></li>
        <li><?= $title; ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<form id="page-form" action="<?= site_url('admin/contacts/' .$contact->id); ?>" method='POST' role="form">

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('contacts', 'Create a new Contact'); ?></h3>
    </div>
    <div class="box-body">
        <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">

        <div class="form-group">
            <label class="control-label" for="name"><?= __d('contacts', 'Name'); ?></label>
            <input name="name" id="name" type="text" class="form-control" value="<?= Input::old('name', $contact->name); ?>" placeholder="<?= __d('contacts', 'Name'); ?>">
        </div>
        <div class="form-group">
            <label class="control-label" for="email"><?= __d('contacts', 'E-mail'); ?></label>
            <input name="email" id="email" type="text" class="form-control" value="<?= Input::old('email', $contact->email); ?>" placeholder="<?= __d('contacts', 'E-mail'); ?>">
        </div>
        <div class="form-group">
            <label class="control-label" for="path"><?= __d('contacts', 'Paths'); ?></label>
            <textarea name="path" id="path" class="form-control" rows="4" style="resize: none;" placeholder="<?= __d('contacts', 'Paths'); ?>"><?= Input::old('path', $contact->path); ?></textarea>
        </div>
        <div class="form-group">
            <label class="control-label" for="description"><?= __d('contacts', 'Description'); ?></label>
            <textarea name="description" id="description" class="form-control" rows="4" style="resize: none;" placeholder="<?= __d('contacts', 'Description'); ?>"><?= Input::old('description', $contact->description); ?></textarea>
        </div>
        <div class="form-group">
            <label class="control-label" for="message"><?= __d('contacts', 'Content'); ?></label>
            <textarea name="message" id="message" class="form-control" rows="25" style="resize: none;" placeholder="<?= __d('contacts', 'Message'); ?>"><?= Input::old('message', $contact->message); ?></textarea>
        </div>

        </div>
    </div>
    <div class="box-footer">
        <input type="submit" name="submit"  class="btn btn-success col-sm-2 pull-right" value="<?= __d('contacts', 'Save'); ?>" />
    </div>
</div>

<input type="hidden" name="_token" value="<?= csrf_token(); ?>" />

</form>

<a class="btn btn-primary col-sm-2" href="<?= site_url('admin/contacts'); ?>"><?= __d('contacts', '<< Previous Page'); ?></a>

<div class="clearfix"></div>
<br>

</section>
