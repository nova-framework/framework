@foreach (array('info', 'success', 'warning', 'danger') as $type)
    @if (Session::has($type))
<div class="alert alert-{{ $type }} alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><span aria-hidden="true">&times;</span></button>
    {{ Session::get($type); }}
</div>
    @endif
@endforeach

@if ($errors->any())
<div class="alert alert-danger alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><span aria-hidden="true">&times;</span></button>
    <ul>
        @foreach ($errors->all('<li>:message</li>') as $error)
        {{ $error; }}
        @endforeach
    </ul>
</div>
@endif
