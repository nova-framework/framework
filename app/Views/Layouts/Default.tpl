<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title or 'Page' }} - {{ Config::get('app.name'); }}</title>
@php

echo Asset::build('css', array(
    'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
    'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css',
    'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
    asset_url('css/bootstrap-xl-mod.min.css'),
    asset_url('css/style.css'),
));

echo Asset::render('css', 'header');
echo Asset::render('js', 'header');

@endphp

</head>
<body>

<div class="container">

@section('content')
    {{ $content }}
@show

</div>

@section('footer')

@show

@php

echo Asset::build('js', array(
    'https://code.jquery.com/jquery-1.12.4.min.js',
    'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'
));

echo Asset::render('js', 'footer');

@endphp

</body>
</html>
