<div style="margin-bottom: 40px;">
    <h4><strong><?= __d('content', 'Categories'); ?></strong></h4>
    <hr style="margin-bottom: 0;">
    <?php foreach ($categories as $category) { ?>
    <div style="padding: 10px 0 10px 0; border-bottom: 1px solid #eee;">
        <a class="pull-left" href="<?= site_url('content/category/' .$category->slug); ?>"><?= $category->name; ?></a> <span class="pull-right"><?= $category->count; ?></span>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
<?php } ?>
</div>
