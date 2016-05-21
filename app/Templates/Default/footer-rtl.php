</div>

<?php
Assets::js([
	'https://code.jquery.com/jquery-1.12.4.min.js',
    template_url('js/bootstrap-rtl.min.js', 'Default'),
]);

echo $js; // Place to pass data / plugable hook zone
echo $footer; // Place to pass data / plugable hook zone
?>

</body>
</html>
