<div class="checkbox" style="padding-left: <?= ($level > 0) ? ($level * 25) .'px' : '0'; ?>;">
    <label>
        <input class="<?= $type; ?>-checkbox" name="items[]" value="<?= $taxonomy->id; ?>" type="checkbox">
        &nbsp;&nbsp;<?= $taxonomy->name; ?>
    </label>
</div>
