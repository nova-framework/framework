<section class="content-header">
    <h1><?= __d('files', 'Files Manager'); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('files', 'Dashboard'); ?></a></li>
        <li><?= __d('files', 'Files'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<div class="elfinder"></div>

</section>

<?php

Assets::css(array(
    'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css',
    vendor_url('css/elfinder.min.css', 'studio-42/elfinder'),
    vendor_url('css/theme.css', 'studio-42/elfinder')
));

Assets::js(array(
    'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js',
    vendor_url('js/elfinder.full.js', 'studio-42/elfinder')
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
            customData: {
                _token: "<?= csrf_token(); ?>"
            }
        }).elfinder('instance');
    });
</script>
