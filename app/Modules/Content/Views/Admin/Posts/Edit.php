<section class="content-header">
    <h1><?= __d('content', $title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('content', 'Dashboard'); ?></a></li>
        <li><a href="<?= site_url('admin/content/' .$type); ?>"><?= $mode; ?></a></li>
        <li><?= $title; ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<style>

.bootstrap-wysihtml5-insert-link-modal .icheckbox_square-blue,
.bootstrap-wysihtml5-insert-link-modal .iradio_square-blue {
    margin-right: 10px;
}

.bootstrap-wysihtml5-insert-link-modal h3,
.bootstrap-wysihtml5-insert-image-modal h3 {
    margin: 0;
}

.bootstrap-wysihtml5-insert-link-modal a,
.bootstrap-wysihtml5-insert-image-modal a {
    width: 30%;
}

.bootstrap-wysihtml5-insert-link-modal a.close,
.bootstrap-wysihtml5-insert-image-modal a.close {
    width: auto;
    margin-top: -5px;
}

.bootstrap-wysihtml5-insert-link-modal a.btn-default,
.bootstrap-wysihtml5-insert-image-modal a.btn-default {
    float: left;
}

</style>

<div class="row">

<div class="col-md-9">

<form action="<?= site_url('admin/users'); ?>" method='POST' enctype="multipart/form-data" role="form">

<div class="box box-default">
    <div class="box-body">
        <input name="title" id="title" type="text" style="border: 1px solid #dddddd; font-size: 18px; padding: 10px; width: 100%;" value="<?= Input::old('title', $post->title); ?>" placeholder="<?= __d('content', 'Enter title here'); ?>" autocomplete="off">

        <div class="clearfix"></div>
        <br>

        <textarea name="content" id="content" style="border: 1px solid #dddddd; width: 100%; padding: 10px; height: 600px; resize: vertical;" autocomplete="off"><?= Input::old('content', $post->content); ?></textarea>
    </div>
    <div class="box-footer">
         <div id="edit-status" style="padding: 5px;" class="pull-left"></div>
         <a class="btn btn-success btn-sm col-sm-2 pull-right" href="#" data-toggle="modal" data-target="" role="button"><?= __d('content', 'Add Media'); ?></a>
         <div class="clearfix"></div>
    </div>
</div>

</form>

<script>

$(function () {
    // Bootstrap WYSIHTML5 - text editor
    $('#content').wysihtml5({
        locale: '<?= Language::code(); ?>',
        toolbar: {
            "font-styles": true,  // Font styling, e.g. h1, h2, etc. Default true
            "emphasis":    true,  // Italics, bold, etc. Default true
            "lists":       true,  // (Un)ordered lists, e.g. Bullets, Numbers. Default true
            "html":        true,  // Button which allows you to edit the generated HTML. Default false
            "link":        true,  // Button to insert a link. Default true
            "image":       true,  // Button to insert an image. Default true,
            "color":       false, // Button to change color of font
            "blockquote":  true,  // Blockquote

             // Use the FontAwesome icons.
            "fa":          true
        }
    });
});

</script>

<?php if ($type == 'page') { ?>

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Slug'); ?></h3>
    </div>
    <div class="box-body">
        <div class="form-group" style="margin-bottom: 0;">
            <input name="slug" id="page-slug" type="text" class="form-control" value="<?= Input::old('slug', $post->name); ?>" placeholder="<?= __d('content', 'Enter slug here'); ?>" autocomplete="off">
        </div>
    </div>
</div>

<?php } ?>

<div class="clearfix"></div>

</div>

<div class="col-md-3">

<form id="publish-form" action="<?= site_url('admin/content/' .$post->id); ?>" method='POST' role="form">

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Publish'); ?></h3>
    </div>
    <div class="box-body">
        <div class="md-12">
            <div class="form-group">
                <label class="col-sm-4 control-label" for="visibility" style="padding: 0; text-align: right; margin-top: 7px;"><?= __d('content', 'Status'); ?></label>
                <div class="col-sm-8" style="padding-right: 0;">
                    <select name="status" id="status" class="form-control select2" placeholder="" data-placeholder="<?= __d('content', 'Select the Status'); ?>" style="width: 100%;" autocomplete="off">
                        <?php if (! $creating) { ?>
                        <option value="publish" <?= $status == 'publish' ? 'select="selected"' : ''; ?>><?= __d('content', 'Publish'); ?></option>
                        <?php } ?>
                        <option value="draft" <?= $status == 'draft' ? 'selected="selected"' : ''; ?>><?= __d('content', 'Draft'); ?></option>
                        <option value="review" <?= $status == 'review' ? 'selected="selected"' : ''; ?>><?= __d('content', 'Pending Review'); ?></option>
                    </select>
                </div>
                <div class="clearfix"></div>
             </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="visibility" style="padding: 0; text-align: right; margin-top: 7px;"><?= __d('content', 'Visibility'); ?></label>
                <div class="col-sm-8" style="padding-right: 0;">
                    <select name="visibility" id="visibility" class="form-control select2" placeholder="" data-placeholder="<?= __d('content', 'Select the Visibility'); ?>" style="width: 100%;" autocomplete="off">
                        <option value="public"   <?= $visibility == 'public'   ? 'selected="selected"' : ''; ?>><?= __d('content', 'Public'); ?></option>
                        <option value="password" <?= $visibility == 'password' ? 'selected="selected"' : ''; ?>><?= __d('content', 'Password Protected'); ?></option>
                        <option value="private"  <?= $visibility == 'private'  ? 'selected="selected"' : ''; ?>><?= __d('content', 'Private'); ?></option>
                    </select>
                </div>
                <div class="clearfix"></div>
             </div>
            <div class="form-group" id="password-group" style="display: none;">
                <label class="col-sm-4 control-label" for="password" style="padding: 0; text-align: right; margin-top: 7px;"><?= __d('content', 'Password'); ?></label>
                <div class="col-sm-8" style="padding-right: 0;">
                    <input name="password" id="password" type="text" class="form-control" value="<?= Input::old('password'); ?>" placeholder="<?= __d('content', 'Enter password here'); ?>">
                </div>
             </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="box-footer">
        <input type="submit" name="submit" class="btn btn-success col-sm-6 pull-right" value="<?= ($creating) ? __d('content', 'Publish') : __d('content', 'Update'); ?>">
    </div>
</div>

<input type="hidden" name="creating" value="<?= (int) $creating; ?>" />
<input type="hidden" name="taxonomy" value="<?= ($type == 'tag') ? 'post_tag' : $type; ?>" />
<input type="hidden" name="_token"   value="<?= csrf_token(); ?>" />

</form>

<script>

$(function () {
    var type = '<?= $type; ?>';

    $('#publish-form').submit(function(event) {
        $(this).find('.publish-form-value').remove();

        $(this).append('<input class="publish-form-value" type="hidden" name="title" value="' + $('#title').val() + '" /> ');
        $(this).append('<input class="publish-form-value" type="hidden" name="content" value="' + $('#content').val() + '" /> ');

        if (type === 'page') {
            $(this).append('<input class="publish-form-value" type="hidden" name="slug" value="' + $('#page-slug').val() + '" /> ');
            $(this).append('<input class="publish-form-value" type="hidden" name="parent" value="' + $('#page-parent').val() + '" /> ');
            $(this).append('<input class="publish-form-value" type="hidden" name="order" value="' + $('#page-order').val() + '" /> ');
        } else {
            $(this).append('<input class="publish-form-value" type="hidden" name="categories" value="' + $('.category-checkbox:checked').serialize() + '" /> ');

            $(this).append('<input class="publish-form-value" type="hidden" name="tags" value="' + $('#tags').val() + '" /> ');
        }

        //event.preventDefault();
    });

    $('#visibility').change(function(e) {
        var visibility = $(this).val();

        if (visibility === 'password') {
            $('#password-group').show();
        } else {
            $('#password-group').hide();
        }
    });
});

</script>

<?php if ($type == 'page') { ?>

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Page Attributes'); ?></h3>
    </div>
    <div class="box-body" style="padding-bottom: 20px;">
        <div class="md-12">
            <?php if ($creating) { ?>
            <div class="form-group">
                <label class="control-label" for="slug"><?= __d('content', 'Parent'); ?></label>
                <select name="parent" id="page-parent" class="form-control select2" placeholder="" data-placeholder="<?= __d('content', 'Select a parent Page'); ?>" style="width: 100%;" autocomplete="off">
                    <?= $menuSelect; ?>
                </select>
            </div>
            <?php } else { ?>
            <input type="hidden" name="parent"   value="0" />
            <?php } ?>
            <div class="form-group">
                <label class="control-label" for="slug"><?= __d('content', 'Order'); ?></label>
                <div class="clearfix"></div>
                <div class="col-md-4" style="padding: 0;">
                    <input name="order" id="page-order" type="number" class="form-control" style="padding-right: 3px;" min="0" max="1000" value="<?= Input::old('order', $menuOrder); ?>" autocomplete="off">
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
    </div>
</div>

<?php if (! $creating) { ?>

<script>

$(function () {
    $('#page-parent').val('');
});

</script>

<?php } ?>

<?php } ?>

<?php if ($type == 'post') { ?>

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Categories'); ?></h3>
    </div>
    <div class="box-body" style="margin-bottom: 0;">
        <div style="max-width: 200px;">
            <?= $categories; ?>
        </div>
        <div class="clearfix"></div>
        <hr>
        <h4><?= __d('content', 'Create a new Category'); ?></h4>
        <br>
        <div class="form-group">
            <input name="name" id="category-name" type="text" class="form-control" value="<?= Input::old('slug'); ?>" placeholder="<?= __d('content', 'Name'); ?>">
        </div>
        <div class="form-group">
            <select name="parent" id="category-parent" class="form-control select2" placeholder="" data-placeholder="<?= __d('content', '-- Parent Category --'); ?>" style="width: 100%;" autocomplete="off">
                <option value="0"><?= __d('content', 'None'); ?></option>
                <?= $categorySelect; ?>
            </select>
        </div>

        <div class="clearfix"></div>
    </div>
    <div class="box-footer">
        <input type="submit" name="submit" class="btn btn-success col-sm-6 pull-right" value="<?= __d('content', 'Add new Category'); ?>">
    </div>
</div>

<?php if (! $creating) { ?>

<script>

$(function () {
    $('#category-parent').val('');
});

</script>

<?php } ?>

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Tags'); ?></h3>
    </div>
    <div class="box-body">
        <div class="form-group">
            <div class="input-group">
                <input type="text" name="tags" id="tags" class="form-control" value="<?= Input::old('tags', isset($tags) ? $tags : ''); ?>" autocomplete="off">
                <span class="input-group-btn">
                    <button class="btn btn-primary" type="button"><?= __d('content', 'Add'); ?></button>
                </span>
            </div>
        </div>
        <p class="text-muted"><?= __d('content', 'Separate tags with commas.'); ?></p>
    </div>
</div>

<?php } ?>

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Featured Image'); ?></h3>
    </div>
    <div class="box-body" style="padding-bottom: 20px;">
         <a href="#" data-toggle="modal" data-target="" role="button"><?= __d('content', 'Set featured image'); ?></a>
    </div>
</div>

<div class="clearfix"></div>

</div>

</div>

<div class="clearfix"></div>

<br>
<a class="btn btn-primary col-sm-2" href="<?= site_url('admin/content/' .Str::plural($type)); ?>"><?= __d('content', '<< Previous Page'); ?></a>

<div class="clearfix"></div>

</section>
