<section class="content-header">
    <h1><?= $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('content', 'Dashboard'); ?></a></li>
        <li><?= __d('content', 'Media'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<p>
    <button id="addNewUploads" class="btn btn-success btn-sm pull-right"><?= __d('content', 'Add New'); ?></button>
    <div class="clearfix"></div>
</p>

<form action="<?= site_url('admin/media/upload'); ?>" id="fm_dropzone_main" enctype="multipart/form-data" method="POST">
    <?= csrf_field() ?>
    <a id="closeDZ1"><i class="fa fa-times"></i></a>
    <div class="dz-message"><i class="fa fa-cloud-upload"></i><br><?= __d('content', 'Drop files here to upload'); ?></div>
</form>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Uploaded files'); ?></h3>
    </div>
    <div class="box-body">
        <ul class="files_container">
        </ul>
    </div>
</div>

</section>

<div class="modal fade" id="edit-file-modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 97%; !important; margin-left: 1.5%; margin-top: 1.7%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="myModalLabel">File: </h4>
            </div>
            <div class="modal-body" style="min-height: 550px; padding-top: 0;">
                    <div class="row">
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="fileObject">

                            </div>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4" style="padding-top: 15px;">
                        <div class="panel panel-default">
                            <div class="panel-heading"><?= __d('content', 'Edit the File information'); ?></div>
                            <div class="panel-body">
                            <form class="file-info-form" enctype="multipart/form-data" method="POST">
                                <input type="hidden" name="file_id" value="0">
                                <div class="form-group">
                                    <label for="filename">File Name</label>
                                    <div class="upload-filename"></div>
                                </div>
                                <div class="form-group">
                                    <label for="url">URL</label>
                                    <div class="upload-url"></div>
                                </div>
                                <div class="form-group">
                                    <label for="caption">Caption</label>
                                    <input class="form-control" placeholder="Caption" name="caption" type="text" value="">
                                </div>
                                <div class="form-group">
                                    <label for="caption">Description</label>
                                    <textarea class="form-control" placeholder="Description" rows="10" name="description" style="resize: none;"></textarea>
                                </div>
                                <button type="button" class="btn btn-success col-sm-4 pull-right">Save</button>
                            </form>
                            </div>
                        </div>
                        </div>
                    </div><!--.row-->
            </div>
            <div class="modal-footer" style="padding: 10px;">
                <button type="button" class="btn btn-danger col-sm-1" id="delFileBtn">Delete</button>
                <button type="button" class="btn btn-primary col-sm-1 pull-right" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.2.0/min/dropzone.min.js"></script>

<script>

var publicUrl = "<?= site_url('assets/files'); ?>";
var filesUrl = "<?= site_url('admin/media/serve'); ?>";

var fm_dropzone_main = null;

var cntFiles = null;

$(function () {
    fm_dropzone_main = new Dropzone("#fm_dropzone_main", {
        maxFilesize: 2,
        acceptedFiles: "image/*,application/pdf",

        // The framework's CSRF Token.
        headers: {
            'X-CSRF-Token': '<?= csrf_token(); ?>'
        },

        init: function() {
            this.on("complete", function(file) {
                this.removeFile(file);
            });

            this.on("success", function(file) {
                console.log("addedfile");
                console.log(file);

                loadUploadedFiles();
            });
        }
    });

    $("#fm_dropzone_main").slideUp();

    $("#addNewUploads").on("click", function() {
        $("#fm_dropzone_main").slideDown();
    });

    $("#closeDZ1").on("click", function() {
        $("#fm_dropzone_main").slideUp();
    });

    $("body").on("click", "ul.files_container .fm_file_sel", function() {
        var upload = $(this).attr("upload");

        upload = JSON.parse(upload);

        $("#edit-file-modal .modal-title").html("File: " + upload.title);
        $(".file-info-form").find(".upload-filename").html(upload.title);
        $(".file-info-form").find(".upload-url").html(publicUrl + '/' + upload.name);

        $(".file-info-form input[name=file_id]").val(upload.id);
        $(".file-info-form input[name=caption]").val(upload.caption);
        $(".file-info-form input[name=description]").val(upload.description);

        $("#edit-file-modal .fileObject").empty();

        if ($.inArray(upload.extension, ["jpg", "jpeg", "png", "gif", "bmp"]) > -1) {
            $("#edit-file-modal .fileObject").append('<img class="img-responsive" style="width: auto; height: 100%; max-height: 600px;" src="' + filesUrl + '/' + upload.name+'">');

            $("#edit-file-modal .fileObject").css("padding", "15px 0px");
        } else {
            switch (upload.extension) {
                case "pdf":
                    // TODO: Object PDF
                    $("#edit-file-modal .fileObject").append('<object width="100%" height="600" style="margin-top: 15px; border: 1px solid #dddddd;" data="' + filesUrl + '/' + upload.name + '"></object>');
                    $("#edit-file-modal .fileObject").css("padding", "0px");

                    break;
                default:
                    $("#edit-file-modal .fileObject").append('<i class="fa fa-file-text-o"></i>');
                    $("#edit-file-modal .fileObject").css("padding", "30px 0px");

                    break;
            }
        }

        var height = $(window).height() - 180;

        $("#edit-file-modal").find(".modal-body").css("height", height);

        $("#edit-file-modal").modal('show');
    });

    loadUploadedFiles();
});

function loadUploadedFiles() {
    // load folder files
    $.ajax({
        dataType: 'json',
        url: "<?= site_url('admin/media/uploaded'); ?>",
        success: function (json) {
            console.log(json);

            cntFiles = json.uploads;

            $("ul.files_container").empty();

            if (cntFiles.length > 0) {
                for (var index = 0; index < cntFiles.length; index++) {
                    var element = cntFiles[index];

                    var li = formatFile(element);

                    $("ul.files_container").append(li);
                }
            } else {
                $("ul.files_container").html("<div class='text-center text-danger' style='margin-top:40px;'>No Files</div>");
            }
        }
    });
}

function formatFile(upload) {
    var image = '';

    if ($.inArray(upload.extension, ["jpg", "jpeg", "png", "gif", "bmp"]) > -1) {
        image = '<img src="' + filesUrl + '/' + upload.name + '?s=130">';
    } else {
        switch (upload.extension) {
            case "pdf":
                image = '<i class="fa fa-file-pdf-o"></i>';
                break;

            default:
                image = '<i class="fa fa-file-o"></i>';
                break;
        }
    }

    return '<li><a class="fm_file_sel" data-toggle="tooltip" data-placement="top" title="' + upload.title + '" upload=\'' + JSON.stringify(upload) + '\'>' + image + '</a></li>';
}

</script>
