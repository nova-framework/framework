<div class="form-group">
    <label class="col-sm-4 control-label" for="first-name"><?= __d('users', 'First Name'); ?></label>
    <div class="col-sm-8">
        <input name="first_name" id="first-name" type="text" class="form-control" value="<?= $request->input('first_name', $meta ? $meta->first_name : null); ?>" placeholder="<?= __d('users', 'First Name'); ?>">
     </div>
</div>
<div class="form-group">
    <label class="col-sm-4 control-label" for="last-name"><?= __d('users', 'Last Name'); ?></label>
    <div class="col-sm-8">
        <input name="last_name" id="last-name" type="text" class="form-control" value="<?= $request->input('last_name', $meta ? $meta->last_name : null); ?>" placeholder="<?= __d('users', 'Last Name'); ?>">
     </div>
</div>
<div class="form-group">
    <label class="col-sm-4 control-label" for="location"><?= __d('users', 'Location'); ?></label>
    <div class="col-sm-8">
        <input name="location" id="location" type="text" class="form-control" value="<?= $request->input('location', $meta ? $meta->location : null); ?>" placeholder="<?= __d('users', 'Location'); ?>">
     </div>
</div>
<div class="form-group">
    <label class="col-sm-4 control-label" for="picture"><?= __d('users', 'Picture'); ?></label>
    <div class="col-sm-8">
        <div class="input-group">
            <input type="text" id="file-path-picture" class="form-control" placeholder="<?= __d('users', 'Browse...'); ?>" autocomplete="off">
            <span class="input-group-btn">
                <button class="btn btn-primary" type="button" id="file-browser-picture"><i class="fa fa-search"></i> <?= __d('users', 'Browse'); ?></button>
            </span>
            <input type="file" class="hidden" id="picture" name="picture">
        </div>
    </div>
</div>

<script>

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

</script>
