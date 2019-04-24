<div class="checkbox">
    <label>
        <input class="<?= $type; ?>-checkbox" name="<?= $type; ?>[]" value="<?= $taxonomy->id; ?>" type="checkbox" <?= in_array($taxonomy->id, $selected) ? ' checked="checked"' : ''; ?>>
        <?= trim(str_repeat('--', $level) .' ' .$taxonomy->name); ?>
    </label>
</div>
