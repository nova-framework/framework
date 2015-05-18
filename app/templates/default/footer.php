<?php

use Helpers\Assets;
use Helpers\Url;

?>

</div>

<!-- JS -->
<?php
Assets::js([
	Url::templatePath() . 'js/jquery.js',
	'//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js'
]);
?>

</body>
</html>
