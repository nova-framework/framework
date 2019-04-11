<section class="content-header">
    <h1><?= __d('content', $title); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('content', 'Dashboard'); ?></a></li>
        <li><a href="<?= site_url('admin/content/' .$postType->slug()); ?>"><?= $postType->label('items'); ?></a></li>
        <li><?= $title; ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= View::fetch('Partials/Messages'); ?>

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

.tag-item {
    padding: 5px;
    display: inline-block;
}

.tag-item a:hover {
    color: #dd4b39;
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

        <textarea name="content" class="blue" id="content" style="border: 1px solid #dddddd; width: 100%; padding: 10px; height: 550px; resize: vertical;" autocomplete="off"><?= Input::old('content', $post->content); ?></textarea>
    </div>
    <div class="box-footer">
        <?php $format = __d('content', '%B %d, %Y at %l:%M %p'); ?>
        <div id="edit-status" style="padding: 5px;" class="pull-left text-muted"><?= __d('content', 'Last edited by <b>{0}</b> on {1}', $lastEditor->username, $post->updated_at->formatLocalized($format)); ?></div>
        <a class="btn btn-success btn-sm col-sm-2 btn_upload_image pull-right" href="#" file_type="image" selecter="contentEditorInsertMedia" role="button"><?= __d('content', 'Add Media'); ?></a>
        <div class="clearfix"></div>
    </div>
</div>

</form>

<script>

function contentEditorInsertMedia(url, type, upload) {
    var wysihtml5Editor = $('#content').data("wysihtml5").editor;

    if (type === 'image') {
        wysihtml5Editor.composer.commands.exec("insertImage", { src: url, alt: "Image" });
    } else if (type === 'file') {
        wysihtml5Editor.composer.commands.exec("createLink", { href: url, target: "_blank" });
    }
}

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
        },
        stylesheets: JSON.parse('<?= json_encode($stylesheets) ?>'),

        // The Parser Rules.
        //parserRules: '<?= asset_url("vendor/bootstrap-wysihtml5/parser_rules/advanced_and_extended.json"); ?>'

        // Disable the HTML Parser at all.
        parser: function (html) {
            return html;
        }
    });
});

</script>

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

<?php $deletables = $restorables = 0; ?>
<?php if (! $revisions->isEmpty()) { ?>
<?php $format = __d('content', '%d %b %Y, %R'); ?>

<div class="box box-widget">
    <div class="box-header">
        <h3 class="box-title"><?= __d('content', 'Revisions : {0}', $post->revision->count()); ?></h3>
        <div class="box-tools">
            <a href="<?= site_url('admin/content/' .$post->id .'/revisions'); ?>" class="btn btn-primary btn-sm pull-right" role="button"><i class="fa fa-list"></i> <?= __d('content', 'View all'); ?></a>
        </div>
    </div>
    <div class="box-body no-padding">
       <table id="left" class="table table-striped table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: center; vertical-align: middle;"><?= __d('content', 'Revision'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('content', 'Title'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('content', 'Created By'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('content', 'Created At'); ?></th>
                <th style="text-align: right; vertical-align: middle;"><?= __d('content', 'Operations'); ?></th>
            </tr>
            <?php foreach ($revisions as $revision) { ?>
            <?php $deletables++; ?>
            <?php $restorables++; ?>
            <?php preg_match('#^(?:\d+)-revision-v(\d+)$#', $revision->name, $matches); ?>
            <tr>
                <td style="text-align: center; vertical-align: middle;" width="10%"><?= $version = $matches[1]; ?></td>
                <td style="text-align: left; vertical-align: middle;" width="40%"><?= $revision->title ?: __d('content', 'Untitled'); ?></td>
                <td style="text-align: center; vertical-align: middle;" width="20%"><?= $revision->author->username; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="15%"><?= $revision->created_at->formatLocalized($format); ?></td>
                <td style="text-align: right; vertical-align: middle;" width="15%">
                    <div class="btn-group" role="group" aria-label="...">
                        <a class="btn btn-sm btn-danger" href="#" data-toggle="modal" data-target="#modal-delete-revision-dialog" data-id="<?= $revision->id; ?>" title="<?= __d('content', 'Delete this revision'); ?>" role="button"><i class="fa fa-remove"></i></a>
                        <a class="btn btn-sm btn-success" href="#" data-toggle="modal" data-target="#modal-restore-revision-dialog" data-id="<?= $revision->id; ?>" data-version="<?= $version; ?>" title="<?= __d('content', 'Restore this revision'); ?>" role="button"><i class="fa fa-repeat"></i></a>
                        <a class="btn btn-sm btn-warning" href="<?= site_url('content/' .$revision->slug); ?>" title="<?= __d('content', 'View this revision'); ?>" target="_blank" role="button"><i class="fa fa-search"></i></a>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>

<?php } ?>

<?php if ($deletables > 0) { ?>

<div class="modal modal-default" id="modal-delete-revision-dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="<?= __d('content', 'Close'); ?>" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __d('content', 'Delete this {0} revision?', $name); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __d('content', 'Are you sure you want to remove this {0} revision, the operation being irreversible?', $name); ?></p>
                <p><?= __d('content', 'Please click the button <b>Delete</b> to proceed, or <b>Cancel</b> to abandon the operation.'); ?></p>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-primary pull-left col-md-3" type="button"><?= __d('content', 'Cancel'); ?></button>
                <form id="modal-delete-revision-form" action="" method="POST">
                    <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
                    <input type="submit" name="button" class="btn btn btn-danger pull-right col-md-3" value="<?= __d('content', 'Delete'); ?>">
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<script>

$(function () {
    $('#modal-delete-revision-dialog').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal

        $('#modal-delete-revision-form').attr('action', '<?= site_url("admin/content"); ?>/' + button.data('id') + '/destroy');
    });
});

</script>

<?php } ?>

<?php if ($restorables > 0) { ?>

<div class="modal modal-default" id="modal-restore-revision-dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="<?= __d('content', 'Close'); ?>" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __d('content', 'Restore this {0} revision?', $name); ?></h4>
            </div>
            <div class="modal-body">
                <p class="question"><?= __d('content', 'Are you sure you want to restore this {0} revision?', $name); ?></p>
                <p><?= __d('content', 'Please click the button <b>Restore</b> to proceed, or <b>Cancel</b> to abandon the operation.'); ?></p>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-primary pull-left col-md-3" type="button"><?= __d('content', 'Cancel'); ?></button>
                <form id="modal-restore-revision-form" action="" method="POST">
                    <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
                    <input type="submit" name="button" class="btn btn btn-danger pull-right col-md-3" value="<?= __d('content', 'Restore'); ?>">
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<script>

$(function () {
    $('#modal-restore-revision-dialog').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal

        var question = sprintf("<?= __d('content', 'Are you sure you want to restore the {0} to the revision <b>#%s</b> ?', $name); ?>", button.data('version'));

        $('#modal-restore-revision-dialog').find('.question').html(question);

        $('#modal-restore-revision-form').attr('action', '<?= site_url("admin/content"); ?>/' + button.data('id') + '/restore');
    });
});

</script>

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
            <?php if ($type == 'block') { ?>
            <input type="hidden" name="visibility" value="public" />
            <?php } else { ?>
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
            <?php } ?>
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
        <input name="submit" id="post-submit" type="submit" class="btn btn-success col-sm-6 pull-right" value="<?= ($creating) ? __d('content', 'Publish') : __d('content', 'Update'); ?>">
    </div>
</div>

<input type="hidden" name="creating" value="<?= (int) $creating; ?>" />
<input type="hidden" name="type" value="<?= $type; ?>" />
<input type="hidden" name="_token"   value="<?= csrf_token(); ?>" />

</form>

<script>

$(function () {
    $('#publish-form').submit(function(event) {
        $('#post-submit').prop('disabled', true);

        event.preventDefault();

        var type = '<?= $type; ?>';

        //
        var data = new FormData();

        data.append('status',     $('#status').val());
        data.append('visibility', $('#visibility').val());
        data.append('password',   $('#password').val());

        data.append('title',      $('#title').val());
        data.append('content',    $('#content').val());
        data.append('creating',   "<?= (int) $creating; ?>");
        data.append('type',       type);

        data.append('slug',       $('#page-slug').val());

        if (type === 'page') {
            data.append('parent', $('#page-parent').val());
            data.append('order',  $('#page-order').val());
        }

        // For the Blocks.
        else if (type === 'block') {
            data.append('order', $('#page-order').val());

            data.append('block-show-title', $('#block-show-title').is(":checked") ? 1 : 0);

            data.append('block-show-mode',   $('#block-show-mode').val());
            data.append('block-show-path',   $('#block-show-path').val());
            data.append('block-show-filter', $('#block-show-filter').val());

            data.append('block-position', $('#block-position').val());
        }

        // For the Posts.
        else {
            data.append('categories', $('.category-checkbox:checked').serialize());
        }

        data.append('thumbnail',  $('#thumbnail').val());

        $.ajax({
            url: "<?= site_url('admin/content/' .$post->id); ?>",
            data: data,
            processData: false,
            contentType: false,
            type: 'POST',

            //
            success: function(data) {
                if (data.redirectTo === 'refresh') {
                    window.location.reload(true);
                } else {
                    window.location.href = data.redirectTo;
                }
            },
            complete: function() {
                $('#post-submit').prop('disabled', false);
            }
        });
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

<?php if (($type == 'page') || ($type == 'block')) { ?>

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', '{0} Attributes', $name); ?></h3>
    </div>
    <div class="box-body" style="padding-bottom: 20px;">
        <div class="md-12">
            <?php if ($creating && ($type != 'block')) { ?>
            <div class="form-group">
                <label class="control-label" for="slug"><?= __d('content', 'Parent'); ?></label>
                <select name="parent" id="page-parent" class="form-control select2" placeholder="" data-placeholder="<?= __d('content', 'Select a parent Page'); ?>" style="width: 100%;" autocomplete="off">
                    <?= $menuSelect; ?>
                </select>
            </div>
            <?php } else { ?>
            <input type="hidden" name="parent"   value="0" />
            <?php } ?>
            <?php if ($type == 'block') { ?>
            <div class="clearfix"></div>
            <div class="form-group">
                <?php $position  = $post->block_widget_position ?: 'content'; ?>
                <label class="control-label" for="block-position"><?= __d('content', 'Position'); ?></label>
                <input name="block-position" id="block-position" type="text" class="form-control" value="<?= $position; ?>" placeholder="<?= __d('content', 'Enter position here'); ?>" autocomplete="off">
            </div>
            <?php } ?>
            <div class="clearfix"></div>
            <div class="form-group">
                <label class="control-label" for="slug"><?= __d('content', 'Order'); ?></label>
                <div class="clearfix"></div>
                <div class="col-md-6" style="padding: 0;">
                    <input name="order" id="page-order" type="number" class="form-control" style="padding-right: 3px;" min="0" max="1000" value="<?= Input::old('order', $post->menu_order); ?>" autocomplete="off">
                </div>
            </div>
            <?php if ($type == 'block') { ?>
            <div class="clearfix"></div>
            <div class="form-group" style="padding-top: 15px;">
                <?php $blockFilter = $post->block_visibility_user ?: 'any'; ?>
                <label class="control-label" for="block-show-filter"><?= __d('content', 'Filter'); ?></label>
                <select name="block-show-filter" id="block-show-filter" class="form-control select2" placeholder="" data-placeholder="<?= __d('content', 'Select the Authentication Filter'); ?>" style="width: 100%;" autocomplete="off">
                    <option value="any"   <?= $blockFilter == 'any'   ? 'selected="selected"' : ''; ?>><?= __d('content', 'Show for any users'); ?></option>
                    <option value="user"  <?= $blockFilter == 'user'  ? 'selected="selected"' : ''; ?>><?= __d('content', 'Show for the authenticated users'); ?></option>
                    <option value="guest" <?= $blockFilter == 'guest' ? 'selected="selected"' : ''; ?>><?= __d('content', 'Show for the non-authenticated users'); ?></option>
                </select>
                <div class="clearfix"></div>
             </div>
            <div class="form-group">
                <label class="control-label" for="block-path"><?= __d('content', 'Paths'); ?></label>
                <textarea name="block-show-path" id="block-show-path" style="resize: none;" rows="5" class="form-control"><?= $post->block_visibility_path; ?></textarea>
            </div>
            <div class="form-group">
                <?php $blockMode  = $post->block_visibility_mode ?: 'show'; ?>
                <label class="control-label" for="slug"><?= __d('content', 'Mode'); ?></label>
                <select name="block-show-mode" id="block-show-mode" class="form-control select2" placeholder="" data-placeholder="<?= __d('content', 'Select the visibility mode'); ?>" style="width: 100%;" autocomplete="off">
                    <option value="show" <?= $blockMode == 'show' ? 'selected="selected"' : ''; ?>><?= __d('content', 'Show on the specified paths'); ?></option>
                    <option value="hide" <?= $blockMode == 'hide' ? 'selected="selected"' : ''; ?>><?= __d('content', 'Hide on the specified paths'); ?></option>
                </select>
            </div>
            <div class="form-group">
                <div class="col-md-1" style="padding: 0;">
                    <input type="checkbox" name="block-show-title" id="block-show-title" value="1" />
                </div>
                <div class="col-md-11" style="padding: 2px 10px;">
                    <label class="control-label" for="block-title" style="margin-right: 10px;"><?= __d('content', 'Show the Title'); ?></label>
                </div>
            </div>
            <?php } ?>
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
        <div id="categories-list" style="max-height: 270px;">
            <?= $categories; ?>
        </div>
        <div class="clearfix"></div>
        <hr>
        <h4><?= __d('content', 'Create a new Category'); ?></h4>
        <br>
        <form id="create-category-form" action="<?= site_url('admin/taxonomies'); ?>" method='POST' role="form">

        <div class="form-group">
            <input name="name" id="category-name" type="text" class="form-control" value="<?= Input::old('slug'); ?>" placeholder="<?= __d('content', 'Name'); ?>">
        </div>
        <div class="form-group">
            <select name="parent" id="category-parent" class="form-control select2" placeholder="" data-placeholder="<?= __d('content', '-- Parent Category --'); ?>" style="width: 100%;" autocomplete="off">
                <option value="0"><?= __d('content', 'None'); ?></option>
                <?= $categorySelect; ?>
            </select>
        </div>

        <input type="hidden" name="description" value="" />
        <input type="hidden" name="taxonomy" value="category" />

        </form>

        <div class="clearfix"></div>
    </div>
    <div class="box-footer">
        <a href="#" class="submit-create-category btn btn-primary col-sm-6 pull-right" role="button"><?= __d('content', 'Add new Category'); ?></a>
    </div>
</div>

<script>

$(function () {
    $('.submit-create-category').on('click', function (event) {
        event.preventDefault();

        if ($('#category-name').val() == '') {
            return;
        }

        var createCategoryForm = $('#create-category-form');

        var data = $('#create-category-form, .category-checkbox:checked').serialize();

        $.ajax({
            type: createCategoryForm.attr('method'),
            url:  createCategoryForm.attr('action'),
            data: $('#create-category-form, .category-checkbox:checked').serialize(),

            dataType: 'json',

            //
            success: function (data) {
                $('#category-name').val('');
                $('#category-parent').val('');

                $('#categories-list').html(data.categories);

                // Update the iCheck.
                $('input.category-checkbox').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue',
                    increaseArea: '20%' // optional
                });
            },
            error: function (data) {
                console.log('An error occurred.');

                console.log(data);
            },
        });
    });

});

</script>

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Tags'); ?></h3>
    </div>
    <div class="box-body" style="padding-bottom: 20px;">
        <form id="create-tags-form" action="<?= site_url('admin/content/' .$post->id .'/tags'); ?>" method='POST' role="form">

        <div class="form-group">
            <div class="input-group">
                <input type="text" name="tags" id="tags-input" class="form-control" value="">
                <span class="input-group-btn">
                    <input type="submit" name="submit" id="create-tags-button" class="btn btn-primary" value="<?= __d('content', 'Add'); ?>" type="button">
                </span>
            </div>
        </div>

        </form>
        <p class="text-muted"><?= __d('content', 'Separate tags with commas.'); ?></p>
        <div id="tags-list"><?= $tags; ?></div>
    </div>
</div>

<script>

$(function () {
    var createTagsForm = $('#create-tags-form');

    createTagsForm.on('submit', function (event) {
        event.preventDefault();

        $.ajax({
            type: createTagsForm.attr('method'),
            url:  createTagsForm.attr('action'),
            data: createTagsForm.serialize(),

            dataType: 'json',

            //
            success: function (data) {
                $('#tags-input').val('');

                var tagsList = $('#tags-list');

                for (var i = 0; i < data.length; i++) {
                    var item = data[i];

                    var html = '<div class="tag-item"><a class="delete-tag-link" href="#" data-id="' + item.id  + '"><i class="fa fa-times-circle"></i></a> ' + item.name + '</div>';

                    tagsList.append(html);
                }

                updateRemoveTagLinks();
            },
            error: function (data) {
                console.log('An error occurred.');

                console.log(data);
            },
        });
    });

    var updateRemoveTagLinks = function() {
        $('a.delete-tag-link').on('click', function (event) {
            event.preventDefault();

            var link = $(this);

            var id = link.data('id');

            var url = '<?= site_url("admin/content/" .$post->id ."/tags/"); ?>/' + id + '/detach';

            $.ajax({
                type: 'POST',
                url:  url,
                data: {
                    postId: '<?= $post->id; ?>',
                    tagId:  id
                },

                dataType: 'json',

                //
                success: function (data) {
                    link.closest('.tag-item').remove();
                },
                error: function (data) {
                    console.log('An error occurred.');

                    console.log(data);
                },
            });
        });
    }

    $('#tags-input').val('');

    updateRemoveTagLinks();
});

</script>


<?php } ?>

<?php if ($type != 'block') { ?>

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Featured Image'); ?></h3>
    </div>
    <div class="box-body">
        <?php $thumbId = (int) $post->thumbnail_id ?: 0; ?>
        <?php $thumbUrl = isset($post->thumbnail) && isset($post->thumbnail->attachment) ? site_url('content/media/serve/' .$post->thumbnail->attachment->name) : ''; ?>
        <input name="thumbnail" id="thumbnail" type="hidden" class="uploaded_image_selecter" value="<?= $thumbId; ?>">
        <a class="btn btn-primary btn-sm col-sm-6 pull-right btn_upload_image <?= ($thumbId > 0) ? 'hide' : ''; ?>" file_type="image" selecter="thumbnail"><?= __d('content', 'Set featured image'); ?></a>
        <div class='clearfix uploaded_image <?= ($thumbId == 0) ? 'hide' : ''; ?>'><img class="img-responsive img-thumbnail" src="<?= $thumbUrl; ?>"></div>
        <a class="btn btn-danger btn-sm col-sm-8 btn_remove_image <?= ($thumbId == 0) ? 'hide' : ''; ?>" style="margin-top: 15px;" file_type="image" selecter="attachment"></i> <?= __d('content', 'Remove featured image'); ?></a>
    </div>
</div>

<?php } ?>

<div class="clearfix"></div>

</div>

</div>

<div class="clearfix"></div>

<br>
<a class="btn btn-primary col-sm-2" href="<?= site_url('admin/content/' .Str::plural($type)); ?>"><?= __d('content', '<< Previous Page'); ?></a>

<div class="clearfix"></div>

</section>

<?= View::fetch('Modules/Content::Partials/FileManager'); ?>

