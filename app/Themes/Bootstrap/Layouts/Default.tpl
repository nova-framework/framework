<!DOCTYPE html>
<html lang="{{ Language::code() }}">
<head>
    <meta charset="utf-8">
    <title>{{ $title or __d('bootstrap', 'Page') }} - {{ Config::get('app.name') }}</title>
    {{ $meta or '' }}

@php

Assets::css(array(
    vendor_url('dist/css/bootstrap.min.css', 'twbs/bootstrap'),
    vendor_url('dist/css/bootstrap-theme.min.css', 'twbs/bootstrap'),
    'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
    theme_url('css/style.css', 'Bootstrap'),
));

@endphp

    {{ $css or '' }}
</head>
<body>

@section('header')
@show

{{ $afterBody or '' }}

<div class="container">
    @section('content')
        {{ $content }}
    @show
</div>

@section('footer')
@show

@php

Assets::js(array(
    'https://code.jquery.com/jquery-1.12.4.min.js',
    vendor_url('dist/js/bootstrap.min.js', 'twbs/bootstrap'),
));

@endphp

{{ $js or '' }}

{{ $footer or '' }}

<!-- DO NOT DELETE! - Forensics Profiler -->

</body>
</html>
