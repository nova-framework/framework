<div class="checkbox" style="padding-left: <?= ($level > 0) ? ($level * 25) .'px' : '0'; ?>;">
    <label>
        <input class="<?= $type; ?>-checkbox" name="items[]" value="<?= $post->id; ?>" type="checkbox">
        &nbsp;&nbsp;<?= $post->title; ?>
    </label>
</div>

