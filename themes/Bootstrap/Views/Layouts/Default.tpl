<!DOCTYPE html>
<html lang="{{ Language::code() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title or 'Page' }} - {{ Config::get('app.name') }}</title>
@php

echo Asset::render('css', array(
    vendor_url('dist/css/bootstrap.min.css', 'twbs/bootstrap'),
    vendor_url('dist/css/bootstrap-theme.min.css', 'twbs/bootstrap'),
    'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
    asset_url('css/bootstrap-xl-mod.min.css'),
    asset_url('css/style.css'),
));

echo Asset::position('header', 'css');

echo Asset::render('js', array(
    asset_url('js/sprintf.min.js'),
    'https://code.jquery.com/jquery-1.12.4.min.js',
));

echo Asset::position('header', 'js');

@endphp
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

@section('header')

@show

<div class="container">

@section('content')
    {{ $content }}
@show

</div>

@section('footer')

@show

@php

echo Asset::render('js', array(
    vendor_url('dist/js/bootstrap.min.js', 'twbs/bootstrap'),
));

echo Asset::position('footer', 'js');

@endphp

<!-- DO NOT DELETE! - Profiler -->

</body>
</html>
