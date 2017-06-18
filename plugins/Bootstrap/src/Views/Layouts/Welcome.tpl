@layout('Bootstrap::Layouts/Default')

@section('header')
    @partial('Bootstrap::Layouts/Partials/Navbar')
@stop

@section('content')
    @partial('Bootstrap::Layouts/Partials/Logo')

    @parent
@stop

@section('footer')
    @partial('Bootstrap::Layouts/Partials/Footer')
@stop
