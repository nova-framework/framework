<section class="content-header">
    <h1><?= __d('files', 'Files'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('users', 'Dashboard'); ?></a></li>
        <li><?= __d('files', 'Files'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<div class="elfinder"></div>

</section>

<?php

Assets::css(array(
    'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css',
    site_url('modules/files/assets/css/elfinder.min.css'),
    site_url('modules/files/assets/css/theme.css')
));

Assets::js(array(
    'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js',
    site_url('modules/files/assets/js/elfinder.full.js')
));

?>

<script type="text/javascript" charset="utf-8">
    $(document).ready(function() {
        var beeper = $(document.createElement('audio')).hide().appendTo('body')[0];

        $('div.elfinder').elfinder({
            url : '<?= site_url('admin/files/connector'); ?>',
            dateFormat: 'M d, Y h:i A',
            fancyDateFormat: '$1 H:m:i',
            lang: '<?= Language::code(); ?>',
            height: 550,
            cookie : {
                expires: 30,
                domain: '',
                path: '/',
                secure: false,
            },
        }).elfinder('instance');
    });
</script>
