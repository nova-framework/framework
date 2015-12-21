<?php
/**
 * Sample layout
 */

use Nova\Helpers\Assets;

?>
</div>

<!-- JS -->
<?php

// Add Controller specific data.
foreach($footerArea as $str) {
    echo $str;
}

Assets::js(array(
    '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js'
));

//Add Controller specific JS files.
Assets::js($footerJScript);

?>

</body>
</html>
