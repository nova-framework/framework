@layout('Default', 'Bootstrap')

@section('header')
    @partial('Partials/Navbar', 'Bootstrap')
@show

@section('content')
    @partial('Partials/Logo', 'Bootstrap')

    @parent
@stop

@section('footer')
    @partial('Partials/Footer', 'Bootstrap')
@show
