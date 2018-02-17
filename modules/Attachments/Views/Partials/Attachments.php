<?php

$attachable   = isset($attachable) ? $attachable : '';

$downloadable = isset($downloadable) ? $downloadable : false;
$deletable    = isset($deletable)    ? $deletable    : false;

$maxFiles    = isset($maxFiles)    ? $maxFiles    : Config::get('attachments::uploader.maxFiles',    10);
$maxFilesize = isset($maxFilesize) ? $maxFilesize : Config::get('attachments::uploader.maxFilesize', 1000);

$files = isset($files) ? $files : array();

?>

<div id ="dropzone" class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title "><?= __d('attachments', 'Attachments'); ?></h3>
    </div>
    <div class="box-body no-padding">
        <div id="empty-files" style="padding: 10px 10px 20px 25px;">
            <h4><i class="icon fa fa-info-circle"></i> <strong><?= __d('attachments', 'No attached files!'); ?></strong></h4>
            <br>
            <?= __d('attachments', 'For attaching files, please click the button bellow or drag and drop files within this widget.'); ?>
        </div>
        <table id="files-table" class="table table-striped table-hover table-responsive" style="display: none;">
            <thead>
                <tr class="bg-navy disabled">
                    <th style="text-align: center; vertical-align: middle;" width="5%"><?= __d('attachments', 'ID'); ?></th>
                    <th style="text-align: center; vertical-align: middle;" width="55%"><?= __d('attachments', 'File'); ?></th>
                    <th style="text-align: center; vertical-align: middle;" width="15%"><?= __d('attachments', 'Type'); ?></th>
                    <th style="text-align: center; vertical-align: middle;" width="10%"><?= __d('attachments', 'Size'); ?></th>
                    <th style="text-align: right; vertical-align: middle;" width="15%"><?= __d('attachments', 'Operations'); ?></th>
                </tr>
            </thead>
            <tbody id="previews" class="dropzone-previews">
                <tr>
                    <td style="text-align: center; vertical-align: middle;" width="5%">
                        <div class="fileid">-</div>
                    </td>
                    <td style="text-align: center; vertical-align: middle;" width="55%">
                        <div class="name" data-dz-name></div>
                        <strong class="error text-danger" data-dz-errormessage></strong>
                    </td>
                    <td style="text-align: center; vertical-align: middle;" width="15%">
                        <div class="type"></div>
                    </td>
                    <td style="text-align: center; vertical-align: middle;" width="10%">
                        <div class="size" data-dz-size></div>
                    </td>
                    <td style="text-align: right; vertical-align: middle;" width="15%">
                        <span class="working"><i class="fa fa-cog fa-spin" aria-hidden="true" style="margin: 8px 9px 8px;"></i></span>
                        <div class="progress active" style="display: none; border: 1px solid #008d4c; background: #fff; height: 30px; margin: 0;" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                            <div class="progress-bar progress-bar-success" style="width: 25%; padding: 4px; font-weight: bold;">0%</div>
                        </div>
                        <div class="btn-group pull-right actions" role="group" aria-label='...' style="display: none;">
                            <a class="btn btn-sm btn-warning preview" href="#" data-toggle="modal" data-target="#modal-preview-dialog" title="<?= __d('attachments', 'Show this Attachment'); ?>" role="button"><i class="fa fa-search"></i></a>
                            <?php if ($downloadable) { ?>
                            <a class="btn btn-sm btn-success download" href="#" title="<?= __d('requests', 'Download this Attachment'); ?>" role="button"><i class="fa fa-download"></i></a>
                            <?php } ?>
                            <?php if ($deletable) { ?>
                            <a data-dz-remove class="btn btn-sm btn-danger" href="#" title="<?= __d('attachments', 'Delete this Attachment'); ?>" role="button"><i class="fa fa-remove"></i></a>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="actions" class="box-footer">
        <div class="btn btn-primary col-sm-2 fileinput-button pull-right" style="display: block;">
            <i class="fa fa-upload"></i> <?= __d('attachments', 'Add files...'); ?>
        </div>
    </div>
</div>

<div class="modal modal-default" id="modal-confirm-dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="<?= __d('attachments', 'Close'); ?>" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-confirm-title"><?= __d('attachments', 'Are you sure?'); ?></h4>
            </div>
            <div class="modal-body">
                <p id="modal-confirm-message"></p>
            </div>
            <div class="modal-footer">
                <button id="modal-confirm-cancel-button" data-dismiss="modal" class="btn btn-primary pull-left col-md-3" type="button"><?= __d('attachments', 'Cancel'); ?></button>
                <button id="modal-confirm-delete-button" data-dismiss="modal" class="btn btn-success pull-right col-md-3" type="button"><?= __d('attachments', 'Confirm'); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<div class="modal modal-default" id="modal-preview-dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 97% !important; margin-left: 1.5%; margin-top: 1.7%;">
        <div class="modal-content">
            <div class="modal-header" style="padding: 10px;">
                <button aria-label="<?= __d('attachments', 'Close'); ?>" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-preview-title" style="margin: 0;">Preview</h4>
            </div>
            <div class="modal-body no-padding">
                <iframe class="modal-preview-iframe" frameborder="0" style="width: 100%; height: 100%;" src=""></iframe>
            </div>
            <div class="modal-footer" style="padding: 5px;">
                <button id="model-preview-button" data-dismiss="modal" class="btn btn-primary col-sm-1 pull-right" type="button"><?= __d('attachments', 'Close'); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<script>

$(function () {
    $('#modal-preview-dialog').on('show.bs.modal', function (event) {
        var height = $(window).height() - 155;

        $(this).find(".modal-body").css("height", height);

        //
        var button = $(event.relatedTarget); // Button that triggered the modal

        var filename = button.attr('data-name');

        $('.modal-preview-title').html(filename);

        $('.modal-preview-iframe').attr('src', button.attr('data-url'));
    });

    $("#modal-preview-dialog").on('hidden.bs.modal', function () {
        $('.modal-preview-iframe').attr('src', '');
    });
});

</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.2.0/min/dropzone.min.js"></script>

<script>

$(function () {
    var authId    = '<?= $authId; ?>';
    var authGuard = '<?= $authGuard; ?>';

    var setupAttachment = function(preview, name, type, id, url, download) {
        preview.attr('data-id', id);

        preview.find('.fileid').html(id);
        preview.find('.type').html(type);

        preview.find('.download').attr('href', download);

        // Setup the modal preview for the current (mime)type.
        var unavailable = ! type.match(/image.*/)
                       && ! type.match(/audio.*/)
                       && ! type.match(/video.*/)
                       && (type !== 'application/pdf')
                       && (type !== 'application/x-shockwave-flash');

        if (unavailable) {
            preview.find('.preview').remove();
        } else {
            preview.find('.preview').attr('data-name', name);
            preview.find('.preview').attr('data-url', url);
        }

        // Insert a hidden input in the preview element, for notifying back the attachment ID.
        var html = '<input name="attachment[]" class="upload-field-ids" type="hidden" value="' + id + '"/>';

        $('#attachable-form').append(html);
    }

    // Get the template HTML and remove it from the document.
    var previewTemplate = $("#previews").html();

    $("#previews").html('');

    // Custom confirmation dialog.
    Dropzone.confirm = function(question, accepted, rejected) {
        $('#modal-confirm-message').html(question);

        $('#modal-confirm-dialog').modal('toggle');

        $('#modal-confirm-delete-button').off('click').on('click', accepted);

        if (rejected === null) {
            return;
        }

        $('#modal-confirm-cancel-button').off('click').on('click', rejected);
    }

    // For using a table, we need this.
    Dropzone.createElement = function(string) {
        var el = $(string);

        return el[0];
    };

    var dropzone = new Dropzone("#dropzone", {
        url: "<?= site_url('attachments'); ?>", // Set the url.
        chunking: true,
        maxFiles: <?= $maxFiles; ?>,
        maxFilesize: <?= $maxFilesize; ?>, // MB
        parallelUploads: 1,
        //acceptedFiles: 'image/*, application/pdf',
        previewTemplate: previewTemplate,
        autoQueue: true,
        previewsContainer: "#previews", // Define the container to display the previews.
        clickable: ".fileinput-button", // Define the element that should be used as click trigger to select files.

        // Dictionary.
        dictDefaultMessage:           "<?= __d('attachments', 'Drop files here to upload'); ?>",
        dictFallbackMessage:          "<?= __d('attachments', 'Your browser does not support drag\'n\'drop file uploads.'); ?>",
        dictFallbackText:             "<?= __d('attachments', 'Please use the fallback form below to upload your files like in the olden days.'); ?>",
        dictFileTooBig:               "<?= __d('attachments', 'File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.'); ?>",
        dictInvalidFileType:          "<?= __d('attachments', 'You can\'t upload files of this type.'); ?>",
        dictResponseError:            "<?= __d('attachments', 'Server responded with {{statusCode}} code.'); ?>",
        dictCancelUpload:             "<?= __d('attachments', 'Cancel upload'); ?>",
        dictCancelUploadConfirmation: "<?= __d('attachments', 'Are you sure you want to cancel this upload?'); ?>",
        dictRemoveFile:               "<?= __d('attachments', 'Remove file'); ?>",
        dictRemoveFileConfirmation:   "<?= __d('attachments', 'Are you sure you want to remove this attachment?'); ?>",
        dictMaxFilesExceeded:         "<?= __d('attachments', 'You can not upload any more files.'); ?>",

        // The framework's CSRF Token.
        headers: {
            'X-CSRF-Token': '<?= csrf_token(); ?>'
        },

        // For chunked files uploading.
        params: function(files, xhr, chunk) {
            if (! chunk) {
                return;
            }

            var chunkSize = this.options.chunkSize;

            return {
                uuid:       chunk.file.upload.uuid,
                chunk:      chunk.index,
                chunk_size: chunkSize,
                total:      chunk.file.size,
                chunks:     chunk.file.upload.totalChunkCount,
                offset:     chunk.index * chunkSize,
                name:       chunk.file.name
            };
        },
        chunksUploaded: function (file, done) {
            $.ajax({
                type: 'POST',
                url:  '<?= site_url("attachments/done"); ?>',
                data: {
                    uuid: file.upload.uuid,
                    name: file.name,
                    type: file.type,
                    size: file.size,

                    // Setup the ownership.
                    auth_id:    authId,
                    auth_guard: authGuard
                },
                dataType: 'json',
                success: function(data, textStatus, xhr) {
                    var preview = $(file.previewElement);

                    setupAttachment(preview, file.name, file.type, data.id, data.url, data.download);

                    done();
                },
                error: function (xhr, textStatus, errorThrown) {
                    var response = xhr.responseText;

                    if (xhr.getResponseHeader("content-type") && ~ xhr.getResponseHeader("content-type").indexOf("application/json")) {
                        try {
                            var data = JSON.parse(response);

                            response = data.error;
                        } catch (error) {
                            response = "Invalid JSON response from server.";
                        }
                    }

                    file.status = Dropzone.ERROR;

                    dropzone.emit("error", file, response, xhr);

                    dropzone.emit("complete", file);
                }
            });
        }
    });

    dropzone.on("addedfile", function(file) {
        $("#empty-files").hide();
        $("#files-table").show();
    });

    dropzone.on("sending", function(file, xhr, formData) {
        // Setup the ownership.
        formData.append('auth_id',    authId);
        formData.append('auth_guard', authGuard);

        // Setup the preview.
        var preview = $(file.previewElement);

        preview.find('.working').hide();
        preview.find('.progress').show();

        // Disable the submit buttons.
        $(".submit-button").attr('disabled', 'disabled');
    });

    dropzone.on("success", function(file, response) {
        var preview = $(file.previewElement);

        if (! file.upload.chunked) {
            setupAttachment(preview, file.name, file.type, response.id, response.url, response.download);
        }

        // Notify the User.
        var title = "<?= __d('attachments', 'File successfully attached'); ?>";

        var message = sprintf("<?= __d('attachments', 'The file <b>%s</b> was successfully attached.'); ?>", file.name);

        notify(title, message, 'info');
    });

    dropzone.on("error", function(file, message, xhr) {
        notify("<?= __d('attachments', 'ERROR'); ?>", message, 'danger');
    });

    dropzone.on("complete", function(file, response) {
        var preview = $(file.previewElement);

        if ((file.id !== undefined) && (file.url !== undefined) && (file.download !== undefined)) {
            setupAttachment(preview, file.name, file.type, file.id, file.url, file.download);
        }

        preview.find('.working').hide();
        preview.find('.progress').hide();

        preview.find('.actions').show();
    });

    dropzone.on("uploadprogress", function(file, progress, bytesSent) {
        if (file.upload.chunked) {
            bytesSent = 0;

            for (var i = 0; i < file.upload.totalChunkCount; i++) {
                if ((file.upload.chunks[i] !== undefined) && (file.upload.chunks[i].bytesSent !== undefined)) {
                    bytesSent += file.upload.chunks[i].bytesSent;
                }
            }

            progress = (100 *bytesSent) / file.size;
        }

        progress = Math.round(progress);

        var width = Math.max(progress, 25);

        //
        var preview = $(file.previewElement);

        preview.find('.progress-bar').css('width', width + '%');

        preview.find('.progress-bar').html(progress + '%');
    });

    // Enable the submit buttons when nothing's uploading anymore.
    dropzone.on("queuecomplete", function(progress) {
        $(".submit-button").removeAttr('disabled');
    });

    dropzone.on("maxfilesexceeded", function(file) {
        this.removeFile(file);
    });

    dropzone.on("removedfile", function(file) {
        // Hide the previews table if there are no more files.
        if (this.files.length == 0) {
            $("#empty-files").show();
            $("#files-table").hide();
        }

        // Ask the server to delete the current file via its ID.
        var preview = $(file.previewElement);

        if(! preview.attr('data-id')) {
            return;
        }

        var id = preview.attr('data-id');

        $('.upload-field-ids[value="' + id + '"]').remove();

        // Remove the attachment from server.
        var url = sprintf('%s/attachments/%d/destroy', '<?= site_url(); ?>', id);

        $.ajax({
            type: 'POST',
            url:  url,
            data: {
                id: id
            },
            dataType: 'json',
            success: function(data, textStatus, xhr) {
                var title = "<?= __d('attachments', 'Attached file deleted'); ?>";

                var message = sprintf("<?= __d('attachments', 'The attached file <b>%s</b> was successfully deleted.'); ?>", file.name);

                notify(title, message, 'success');
            },
            error: function (xhr, textStatus, errorThrown) {
                //
            }
        });
    });

    // Add the existing files into Dropzone uploader.
    var existingFiles = <?= json_encode($files); ?>;

    for (i = 0; i < existingFiles.length; i++) {
        var file = existingFiles[i];

        file.accepted = true;

        // Add the existing file.
        dropzone.files.push(file);

        dropzone.emit("addedfile", file);
        dropzone.emit("complete", file);
    }

    // Update the Dropzone's max files reached.
    dropzone._updateMaxFilesReachedClass();
});

</script>
