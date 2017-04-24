@layout('Default', 'Bootstrap')

@section('header')
    @partial('Partials/Navbar', 'Bootstrap')
@stop

@section('content')
    @partial('Partials/Logo', 'Bootstrap')

    @parent
@stop

@section('footer')
    @partial('Partials/Footer', 'Bootstrap')
@stop
