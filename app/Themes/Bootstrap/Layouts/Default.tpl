<!DOCTYPE html>
<html lang="{{ Language::code() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title or __d('bootstrap', 'Page') }} - {{ Config::get('app.name') }}</title>

    @assets('css', array(
        vendor_url('dist/css/bootstrap.min.css', 'twbs/bootstrap'),
        vendor_url('dist/css/bootstrap-theme.min.css', 'twbs/bootstrap'),
        'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
        theme_url('css/style.css', 'Bootstrap'),
    ))

</head>
<body>

@section('header')
@stop

<div class="container">

@section('content')
    {{ $content }}
@show

</div>

@section('footer')
@stop

@assets('js', array(
    'https://code.jquery.com/jquery-1.12.4.min.js',
    vendor_url('dist/js/bootstrap.min.js', 'twbs/bootstrap'),
))

<!-- DO NOT DELETE! - Forensics Profiler -->

</body>
</html>
