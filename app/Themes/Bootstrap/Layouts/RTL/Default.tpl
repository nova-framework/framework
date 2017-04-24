<!DOCTYPE html>
<html lang="{{ Language::code() }}">
<head>
    <meta charset="utf-8">
    <title>{{ $title }} - {{ Config::get('app.name') }}</title>
    {{ $meta or '' }}

@php

Assets::css(array(
    theme_url('css/bootstrap-rtl.min.css', 'Bootstrap'),
    theme_url('css/bootstrap-rtl-theme.min.css', 'Bootstrap'),
    'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
    theme_url('css/style-rtl.css', 'Bootstrap'),
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
    theme_url('js/bootstrap-rtl.min.js', 'Bootstrap'),
));

@endphp

{{ $js or '' }}

{{ $footer or '' }}

<!-- DO NOT DELETE! - Forensics Profiler -->

</body>
</html>
