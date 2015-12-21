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
if (isset($footerJScript)) {
    Assets::js($footerJScript);
}

// Add Controller specific data.
printStringArray($footerArea);
?>

</body>
</html>
