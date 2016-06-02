<?php use Nova\Net\Session; ?>

<section class="content-header">
    <h1><?= __('Show Category'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __('Dashboard'); ?></a></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::message('status'); ?>

</section>
