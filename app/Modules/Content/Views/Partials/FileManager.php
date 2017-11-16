<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.2.0/min/dropzone.min.js"></script>

<div class="modal fade" id="fm" role="dialog" aria-labelledby="fileManagerLabel">
    <input type="hidden" id="image_selecter_origin" value="">
    <input type="hidden" id="image_selecter_origin_type" value="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= __d('content', 'Close'); ?>"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="fileManagerLabel"><?= __d('content', 'Select File'); ?></h4>
            </div>
            <div class="modal-body p0">
                <div class="row">
                    <div class="col-xs-3 col-sm-3 col-md-3">
                        <div class="fm_folder_selector">
                            <form action="<?= site_url('admin/media/upload'); ?>" id="fm_dropzone" enctype="multipart/form-data" method="POST">
                                <?= csrf_field() ?>
                                <div class="dz-message"><i class="fa fa-cloud-upload"></i><br><?= __d('content', 'Drop files here to upload'); ?></div>
                            </form>
                        </div>
                    </div>
                    <div class="col-xs-9 col-sm-9 col-md-9 pl0">
                        <div class="nav">
                            <div class="row">
                                <div class="col-xs-2 col-sm-7 col-md-7"></div>
                                <div class="col-xs-10 col-sm-5 col-md-5">
                                    <input type="search" class="form-control pull-right" placeholder="<?= __d('content', 'Search file name'); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="fm_file_selector">
                            <ul>

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

/* ================= File Manager ================= */
var baseUrl = "<?= site_url(); ?>";

var mediaUploadUrl   = "<?= site_url('admin/media/upload'); ?>";
var mediaUploadedUrl = "<?= site_url('admin/media/uploaded'); ?>";
var mediaServeUrl    = "<?= site_url('content/media/serve'); ?>";
var mediaPublicUrl   = "<?= site_url('assets/files'); ?>";

var cntFiles    = null;
var fm_dropzone = null;

$(document).ready(function() {
    function showFileManager(type, selector) {
        $("#image_selecter_origin_type").val(type);
        $("#image_selecter_origin").val(selector);

        $("#fm").modal('show');

        loadFileManagerFiles();
    }

    function getItemLI(upload) {
        var image = '';

        if ($.inArray(upload.extension, ["jpg", "jpeg", "png", "gif", "bmp"]) > -1) {
            image = '<img src="' + mediaServeUrl + '/' + upload.name +'?s=130">';
        }
        else {
            switch (upload.extension) {
                case "pdf":
                    image = '<i class="fa fa-file-pdf-o"></i>';
                    break;
                default:
                    image = '<i class="fa fa-file-text-o"></i>';
                    break;
            }
        }

        return '<li><a class="fm_file_sel" data-toggle="tooltip" data-placement="top" title="' + upload.name +'" upload=\'' + JSON.stringify(upload) + '\'>' + image + '</a></li>';
    }

    function loadFileManagerFiles() {
        // load uploaded files
        $.ajax({
            dataType: 'json',
            url: mediaUploadedUrl,
            success: function ( json ) {
                console.log(json);

                cntFiles = json.uploads;

                $(".fm_file_selector ul").empty();

                if (cntFiles.length) {
                    for (var index = 0; index < cntFiles.length; index++) {
                        var element = cntFiles[index];

                        var li = getItemLI(element);

                        $(".fm_file_selector ul").append(li);
                    }
                }
                else {
                    $(".fm_file_selector ul").html("<div class='text-center text-danger' style='margin-top:40px;'><?= __d('content', 'No Files'); ?></div>");
                }
            }
        });
    }

    $("#fm input[type=search]").keyup(function () {
        var sstring = $(this).val().trim();

        console.log(sstring);

        if (sstring != "") {
            $(".fm_file_selector ul").empty();

            for (var index = 0; index < cntFiles.length; index++) {
                var upload = cntFiles[index];

                if (upload.name.toUpperCase().includes(sstring.toUpperCase())) {
                    var li = getItemLI(upload);

                    $(".fm_file_selector ul").append(li);
                }
            }
        } else {
            loadFileManagerFiles();
        }
    });

    $(".btn_upload_image").on("click", function() {
        showFileManager("image", $(this).attr("selecter"));
    });

    $(".btn_upload_file").on("click", function() {
        showFileManager("file", $(this).attr("selecter"));
    });

    $(".btn_upload_files").on("click", function() {
        showFileManager("files", $(this).attr("selecter"));
    });

    fm_dropzone = new Dropzone("#fm_dropzone", {
        maxFilesize: 200,
        acceptedFiles: "image/*,application/pdf",
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

                loadFileManagerFiles();
            });
        }
    });

    $(".btn_remove_image").on("click", function() {
        $(this).parent().find('.uploaded_image').children("img").attr("src", "");
        $(this).parent().find('.uploaded_image').addClass("hide");

        $(this).parent().find('.btn_upload_image').removeClass("hide");
        $(this).parent().find('.uploaded_image_selecter').val("0");

        $(this).addClass("hide");

        e.preventDefault();
    });

    $(".btn_remove_file").on("click", function(e) {
        $(this).parent().children("a").attr("href", "");
        $(this).parent().children("a").addClass("hide");

        $(this).parent().find('.btn_upload_file').removeClass("hide");
        $(this).parent().find('.uploaded_file_selecter').val("0");

        $(this).addClass("hide");

        e.preventDefault();
    });

    $("body").on("click", ".fm_file_sel", function() {
        var type = $("#image_selecter_origin_type").val();

        var upload = JSON.parse($(this).attr("upload"));

        console.log("upload sel: " + upload + " type: " + type);

        //
        var url = mediaPublicUrl + '/' + upload.name;

        var origin = $("#image_selecter_origin").val();

        if ($.isFunction(window[origin])) {
            window[origin](url, type, upload);

        } else if (type == "image") {
            var origin = $("#image_selecter_origin").val();

            var input = $("input[name=" + origin + "]");

            input.val(upload.id);

            input.next("a").addClass("hide");

            input.next("a").next(".uploaded_image").removeClass("hide");
            input.next("a").next(".uploaded_image").children("img").attr("src", mediaServeUrl + '/' + upload.name);

            input.parent().find(".btn_remove_image").removeClass("hide");
        }
        else if (type == "file") {
            var origin = $("#image_selecter_origin").val();

            var input = $("input[name=" + origin + "]");

            input.val(upload.id);

            input.next("a").addClass("hide");

            input.next("a").next(".uploaded_file").removeClass("hide");
            input.next("a").next(".uploaded_file").attr("href", mediaServeUrl + '/' + upload.name);

            input.parent().find(".btn_remove_file").removeClass("hide");
        }

        $("#fm").modal('hide');
    });
});

</script>

