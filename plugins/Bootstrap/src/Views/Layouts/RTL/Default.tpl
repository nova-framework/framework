<!DOCTYPE html>
<html lang="{{ Language::code() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title or __d('bootstrap', 'Page') }} - {{ Config::get('app.name') }}</title>

    <link rel="stylesheet" type="text/css" href="{{ resource_url('css/bootstrap-rtl.min.css', 'Bootstrap') }}">
    <link rel="stylesheet" type="text/css" href="{{ resource_url('css/bootstrap-rtl-theme.min.css', 'Bootstrap') }}">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Local customizations -->
    <link rel="stylesheet" type="text/css" href="{{ resource_url('css/bootstrap-xl-mod.min.css', 'Bootstrap') }}">
    <link rel="stylesheet" type="text/css" href="{{ resource_url('css/style-rtl.css', 'Bootstrap') }}">

    <script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
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

<script type="text/javascript" src="{{ resource_url('js/bootstrap-rtl.min.js', 'Bootstrap') }}"></script>

<!-- DO NOT DELETE! - Forensics Profiler -->

</body>
</html>
