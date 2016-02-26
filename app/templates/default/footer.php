<?php
/**
 * Sample layout.
 */
use Helpers\Assets;
use Helpers\Hooks;
use Helpers\Url;

//initialise hooks
$hooks = Hooks::get();
?>

</div>

<!-- JS -->
<?php
Assets::js([
    Url::templatePath().'js/jquery.js',
    '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js',
]);

//hook for plugging in javascript
$hooks->run('js');

//hook for plugging in code into the footer
$hooks->run('footer');
?>

</body>
</html>
