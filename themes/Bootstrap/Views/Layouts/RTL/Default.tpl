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
        asset_url('css/bootstrap-rtl.min.css', 'themes/bootstrap'),
        'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
        asset_url('css/bootstrap-xl-mod.min.css', 'themes/bootstrap'),
        asset_url('css/style-rtl.css', 'themes/bootstrap'),
));

echo Asset::position('header', 'css');

echo Asset::position('header', 'js');

@endphp
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
    'https://code.jquery.com/jquery-1.12.4.min.js',
    asset_url('js/bootstrap-rtl.min.js', 'themes/bootstrap'),
));

echo Asset::position('footer', 'js');

@endphp

<!-- DO NOT DELETE! - Profiler -->

</body>
</html>
