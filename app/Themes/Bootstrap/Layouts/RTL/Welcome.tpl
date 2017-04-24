@layout('Default', 'Bootstrap')

@php

$siteName = Config::get('app.name');

@endphp

@section('navbar')
    @partial('Partials/Navbar', 'Bootstrap')
@stop

@section('content')
    <p>
        <img src='<?= theme_url('images/nova.png', 'Bootstrap'); ?>' alt='{{ $siteName }}'>
    </p>

    @parent
@stop

@section('footer')
    @partial('Partials/Footer', 'Bootstrap')
@stop
