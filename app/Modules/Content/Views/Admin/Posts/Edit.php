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

<div class="row">

<div class="col-md-9">

<form action="<?= site_url('admin/users'); ?>" method='POST' enctype="multipart/form-data" role="form">

<div class="box box-default">
    <div class="box-body">
        <input name="title" id="title" type="text" style="border: 1px solid #dddddd; font-size: 18px; padding: 10px; width: 100%;" value="<?= Input::old('title'); ?>" placeholder="<?= __d('content', 'Enter title here'); ?>" autocomplete="off">

        <div class="clearfix"></div>
        <br>

        <textarea name="content" id="content" style="border: 1px solid #dddddd; width: 100%; padding: 10px; height: 600px; resize: vertical;" autocomplete="off"><?= Input::old('content'); ?></textarea>
    </div>
    <div class="box-footer">
         <a class="btn btn-primary btn-sm col-sm-2 pull-left" href="#" data-toggle="modal" data-target="" role="button"><?= __d('content', 'Add Media'); ?></a>
         <div id="edit-status" style="padding: 5px;" class="pull-right"></div>
    </div>
</div>

</form>

<script>

$(function () {
    // Bootstrap WYSIHTML5 - text editor
    $('#content').wysihtml5({
        locale: "<?= Language::code(); ?>",
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
        <div class="col-md-12">
            <div class="form-group" style="margin-bottom: 0;">
            <input name="slug" id="slug" type="text" class="form-control" value="<?= Input::old('slug'); ?>" placeholder="<?= __d('content', 'Enter slug here'); ?>" autocomplete="off">
            </div>
        </div>
    </div>
</div>

<?php } ?>

<div class="clearfix"></div>

</div>

<div class="col-md-3">

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
                        <?php if ($post->exists) { ?>
                        <option value="publish" <?= $status == 'publish' ? 'select="selected"' : ''; ?>><?= __d('content', 'Publish'); ?></option>
                        <?php } ?>
                        <option value="review" <?= $status == 'pending-review' ? 'selected="selected"' : ''; ?>><?= __d('content', 'Pending Review'); ?></option>
                        <option value="draft" <?= $status == 'draft' ? 'selected="selected"' : ''; ?>><?= __d('content', 'Draft'); ?></option>
                    </select>
                </div>
                <div class="clearfix"></div>
             </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="visibility" style="padding: 0; text-align: right; margin-top: 7px;"><?= __d('content', 'Visibility'); ?></label>
                <div class="col-sm-8" style="padding-right: 0;">
                    <select name="visibility" id="visibility" class="form-control select2" placeholder="" data-placeholder="<?= __d('content', 'Select the Visibility'); ?>" style="width: 100%;" autocomplete="off">
                        <option value="public"><?= __d('content', 'Public'); ?></option>
                        <option value="password"><?= __d('content', 'Password Protected'); ?></option>
                        <option value="private"><?= __d('content', 'Private'); ?></option>
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
        <input type="submit" name="submit" class="btn btn-success col-sm-6 pull-right" value="<?= $post->exists ? __d('content', 'Update') : __d('content', 'Publish'); ?>">
    </div>
</div>

<script>

$(function () {
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
            <?php if (! $post->exists) { ?>
            <div class="form-group">
                <label class="control-label" for="slug"><?= __d('content', 'Parent'); ?></label>
                <select name="parent" id="page-parent" class="form-control select2" placeholder="" data-placeholder="<?= __d('content', 'Select a parent Page'); ?>" style="width: 100%;" autocomplete="off">
                    <option></option>
                </select>
            </div>
            <?php } ?>
            <div class="form-group">
                <label class="control-label" for="slug"><?= __d('content', 'Order'); ?></label>
                <div class="clearfix"></div>
                <div class="col-md-4" style="padding: 0;">
                    <input name="order" id="page-order" type="number" class="form-control" min="1" max="8" value="<?= Input::old('order', 0); ?>" autocomplete="off">
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
    </div>
</div>

<?php } ?>

<?php if ($type == 'post') { ?>

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Categories'); ?></h3>
    </div>
    <div class="box-body" style="margin-bottom: 0;">
        <div class="md-12">
            <div class="form-group">
                <select name="parent" id="page-parent" class="form-control select2" multiple="multiple" placeholder="" data-placeholder="<?= __d('content', 'Select a Category'); ?>" style="width: 100%;" autocomplete="off">
                    <?= $categories; ?>
                </select>
            </div>
        </div>
        <div class="clearfix"></div>
        <hr>
        <h4><?= __d('content', 'Create a new Category'); ?></h4>
        <br>
        <div class="form-group">
        <input name="slug" id="slug" type="slug" class="form-control" value="<?= Input::old('slug'); ?>" placeholder="<?= __d('content', 'Name'); ?>">

        </div>
        <div class="form-group">
            <select name="parent" id="category-parent" class="form-control select2" placeholder="" data-placeholder="<?= __d('content', '-- Parent Category --'); ?>" style="width: 100%;" autocomplete="off">
                <option value="0"><?= __d('content', 'None'); ?></option>
                <?= $categories; ?>
            </select>
        </div>

        <div class="clearfix"></div>
    </div>
    <div class="box-footer">
        <input type="submit" name="submit" class="btn btn-success col-sm-6 pull-right" value="<?= __d('content', 'Add new Category'); ?>">
    </div>
</div>

<?php if ($post->exists) { ?>

<script>

$(function () {
    $('#page-parent').val('');
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
                <input type="text" name="tags" id="tags" class="form-control" autocomplete="off">
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

</section>
