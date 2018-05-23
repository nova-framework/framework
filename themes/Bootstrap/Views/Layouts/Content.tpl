<!DOCTYPE html>
<html lang="{{ $code = Language::code() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title or 'Page' }} - {{ Config::get('app.name') }}</title>
@php

echo Asset::build('css', array(
    vendor_url('dist/css/bootstrap.min.css', 'twbs/bootstrap'),
    vendor_url('dist/css/bootstrap-theme.min.css', 'twbs/bootstrap'),
    'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/icheck-bootstrap@2.0.4/icheck-bootstrap.min.css',
    asset_url('css/bootstrap-xl-mod.min.css'),
    asset_url('css/style.css'),
));

echo Asset::render('css', 'header');

@endphp

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
@php

echo Asset::build('js', array(
    asset_url('js/sprintf.min.js'),
    'https://code.jquery.com/jquery-1.12.4.min.js',
));

echo Asset::render('js', 'header');

@endphp

</head>
<body>

@section('header')
    @include('Themes/Bootstrap::Partials/Navbar')
@show

<div class="container">

@section('content')
    {{ $content }}
@show

{{ Widget::position('content-footer') }}

</div>

@section('footer')
    @include('Themes/Bootstrap::Partials/Footer')
@show

<script>

$(function () {
    //Initialize Select2 Elements
    $(".select2").select2({
        theme: "bootstrap"
    });
});
</script>

@php

echo Asset::build('js', array(
    vendor_url('dist/js/bootstrap.min.js', 'twbs/bootstrap'),
    'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/i18n/' .$code .'.js',
));

echo Asset::render('js', 'footer');

@endphp

<!-- DO NOT DELETE! - Profiler -->

</body>
</html>
