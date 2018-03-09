<div class="modal modal-default" id="modal-preview-dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 97% !important; margin-left: 1.5%; margin-top: 1.7%;">
        <div class="modal-content">
            <div class="modal-header" style="padding: 10px;">
                <button aria-label="<?= __d('requests', 'Close'); ?>" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-preview-title" style="margin: 0;"><?= __d('requests', 'Preview'); ?></h4>
            </div>
            <div class="modal-body no-padding">
                <iframe class="modal-preview-iframe" frameborder="0" style="width: 100%; height: 100%;" src=""></iframe>
            </div>
            <div class="modal-footer" style="padding: 10px;">
                <button id="model-preview-button" data-dismiss="modal" class="btn btn-primary col-sm-1 pull-right" type="button"><?= __d('requests', 'Close'); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<script type="text/javascript">

$(function () {
    $('#modal-preview-dialog').on('show.bs.modal', function (event) {
        var height = $(window).height() - 155;

        $(this).find('.modal-body').css('height', height);

        // Setup the dialog content.
        var button = $(event.relatedTarget); // Button that triggered the modal

        $('.modal-preview-iframe').attr('src', button.attr('data-url'));

        // Setup the dialog title.
        var filename = button.attr('data-name');

        $('.modal-preview-title').html(filename);

    });

    $('#modal-preview-dialog').on('hidden.bs.modal', function () {
        $('.modal-preview-iframe').attr('src', '');
    });
});

</script>
