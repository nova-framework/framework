<form id="page-form" action="<?= site_url('admin/menus/{0}/taxonomies', $menu->id); ?>" method='POST' role="form">

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $taxonomyType->label('title'); ?></h3>
    </div>
    <div class="box-body" style="min-height: 150px; max-height: 270px; padding-bottom: 20px;">
    <?= $items; ?>
    </div>
    <div class="box-footer">
        <input type="submit" name="submit" class="btn btn-primary col-sm-5 pull-right" value="<?= __d('content', 'Add to Menu'); ?>" />
    </div>
</div>

<input type="hidden" name="_token"   value="<?= csrf_token(); ?>" />
<input type="hidden" name="type" value="<?= $taxonomyType->name(); ?>" />

</form>
