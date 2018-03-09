<div class="form-group">
    <label class="col-sm-4 control-label" for="first-name"><?= __d('users', 'First Name'); ?></label>
    <div class="col-sm-8">
        <input name="first_name" id="first-name" type="text" class="form-control" value="<?= $request->old('first_name', $fields ? $fields->first_name : null); ?>" placeholder="<?= __d('users', 'First Name'); ?>">
     </div>
</div>
<div class="form-group">
    <label class="col-sm-4 control-label" for="last-name"><?= __d('users', 'Last Name'); ?></label>
    <div class="col-sm-8">
        <input name="last_name" id="last-name" type="text" class="form-control" value="<?= $request->old('last_name', $fields ? $fields->last_name : null); ?>" placeholder="<?= __d('users', 'Last Name'); ?>">
     </div>
</div>
<div class="form-group">
    <label class="col-sm-4 control-label" for="location"><?= __d('users', 'Location'); ?></label>
    <div class="col-sm-8">
        <input name="location" id="location" type="text" class="form-control" value="<?= $request->old('location', $fields ? $fields->location : null); ?>" placeholder="<?= __d('users', 'Location'); ?>">
     </div>
</div>
<div class="form-group">
    <label class="col-sm-4 control-label" for="picture"><?= __d('users', 'Picture'); ?></label>
    <div class="col-sm-8">
        <div class="input-group">
            <input type="text" class="form-control" readonly>
            <label class="input-group-btn">
                <span class="btn btn-primary">
                    <?= __d('contacts', 'Browse ...'); ?> <input type="file" name="picture" style="display: none;">
                </span>
            </label>
        </div>
    </div>
</div>

<script type="text/javascript">

$(function() {

    // We can attach the `fileselect` event to all file inputs on the page
    $(document).on('change', ':file', function() {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');

        if (input.get(0).files) {
            var items = [];

            var files = input.get(0).files;

            for (var i = 0, file; file = files[i]; i++) {
                items.push(file.name);
            }

            label = items.join(', ');
        }

        input.trigger('fileselect', [numFiles, label]);
    });

    // We can watch for our custom `fileselect` event like this
    $(document).ready( function() {
        $(':file').on('fileselect', function(event, numFiles, label) {
            var input = $(this).parents('.input-group').find(':text'),
                log = (numFiles > 1) ? sprintf("<?= __d('contacts', '%d files selected'); ?>", numFiles) : label;

            if (input.length) {
                input.val(label);
            } else {
                if (log) alert(log);
            }
      });
  });

});

</script>

<script>
/*
$('#file-browser-picture').click(function(e) {
    e.preventDefault();

    $('#picture').click();
});

$('#picture').change(function() {
    var value = $(this).val();

    $('#file-path-picture').val(value);
});

$('#file-path-picture').click(function() {
    $('#file-browser-picture').click();
});
*/
</script>
