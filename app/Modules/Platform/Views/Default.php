<section class="content-header">
    <h1><?= $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('platform', 'Dashboard'); ?></a></li>
        <li><?= $title; ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('platform', 'Content'); ?></h3>
    </div>
    <div class="box-body">
    	<?= $content; ?>
    </div>
</div>
