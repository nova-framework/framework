<!DOCTYPE html>
<html lang="{{ Language::code() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title or 'Page' }} - {{ Config::get('app.name') }}</title>

{{ Assets::render('css', array(
    'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
    'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css',
    'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
    asset_url('css/bootstrap-xl-mod.min.css', 'themes/bootstrap'),
    asset_url('css/style.css', 'themes/bootstrap'),
));

}}

{{ Asset::position('header', 'css') }}

{{ Assets::build('js', array(
    asset_url('js/sprintf.min.js'),
    'https://code.jquery.com/jquery-1.12.4.min.js',
));

}}

{{ Asset::position('header', 'js') }}

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<div class="container">
    <div class="row">
        <a style="outline: none;" href="<?= site_url(); ?>"><img src="<?= asset_url('images/nova.png') ?>" alt="<?= Config::get('app.name') ?>"></a>
        <h1><strong>{{ ($title !== 'Home') ? $title : ''; }}</strong></h1>
        <hr style="margin-top: 0;">
    </div>
    {{ $content; }}
</div>

@include('Themes/Bootstrap::Partials/Footer')

{{ Asset::render('js', array(
    'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'
));

}}

{{ Asset::position('footer', 'js') }}

</body>
</html>
