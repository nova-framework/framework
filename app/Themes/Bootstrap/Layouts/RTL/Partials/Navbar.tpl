<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">{{ __d('bootstrap', 'Toggle navigation') }}</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?= site_url(); ?>"><strong>{{ Config::get('app.name') }}</strong></a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-left" style="margin-left: 0;">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class='fa fa-language'></i> {{ Language::name() }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-left">
                    @foreach (Config::get('languages') as $code => $info)
                        <li {{ ($code == Language::code()) ? 'class="active"' : ''; }}>
                            <a href='{{ site_url('language/' .$code) }}' title='{{ $info['info'] }}'>{{ $info['name'] }}</a>
                        </li>
                    @endforeach
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
