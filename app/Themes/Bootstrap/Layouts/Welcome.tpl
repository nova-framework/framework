@layout('Default', 'Bootstrap')

@section('header')
    @partial('Partials/Navbar', 'Bootstrap')
@stop

@section('content')
    <p><img src='<?= theme_url('images/nova.png', 'Bootstrap'); ?>' alt='{{ Config::get('app.name') }}'></p>

    @parent
@stop

@section('footer')
    @partial('Partials/Footer', 'Bootstrap')
@stop
