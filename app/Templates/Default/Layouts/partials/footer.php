<?php
/**
 * Sample layout
 */

use Nova\Helpers\Assets;

?>
</div>

<!-- JS -->
<?php

Assets::js(array(
    '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js'
));

//Add Controller specific JS files.
Assets::js($footerJScripts);
?>

</body>
</html>
