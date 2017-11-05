@extends('Layouts/Default')

@section('content')
<div class="row">
    <a style="outline: none;" href="<?= site_url(); ?>"><img src="<?= resource_url('images/nova.png') ?>" alt="<?= Config::get('app.name') ?>"></a>
    @if ($title)
    <h1><strong>{{ ($title !== 'Home') ? $title : ''; }}</strong></h1>
    @endif
    <hr style="margin-top: 0;">
</div>

{{ $content; }}

@stop

@section('footer')
<footer class="footer">
    <div class="container-fluid">
        <div class="row" style="margin: 15px 0 0;">
            <div class="col-lg-5">
                <p class="text-muted">Copyright &copy; {{ date('Y') }} <a href="http://www.novaframework.com/" target="_blank"><b>Nova Framework {{ $version; }} / Kernel {{ VERSION }}</b></a></p>
            </div>
            <div class="col-lg-7">
                <p class="text-muted pull-right">
                    <small><!-- DO NOT DELETE! - Statistics --></small>
                </p>
            </div>
        </div>
    </div>
</footer>
@stop

@assets('js', array(
    'https://code.jquery.com/jquery-1.12.4.min.js',
    'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'
))

</body>
</html>
