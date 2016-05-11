</div>

<?php

// place default js is array varible
$loadJs = [
	'https://code.jquery.com/jquery-1.12.1.min.js',
	'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js',
];

// check if custom / required js has been set by the called controller method
if(isset($requiredJs)) {

	// cycle each required js
	foreach($requiredJs as $rJs) {

		// push to the loadJs array. the array of js scripts we wish to load
		array_push($loadJs, Url::templatePath() . 'js/' . $rJs);
	}
}

Assets::js($loadJs);
echo $js; //place to pass data / plugable hook zone
echo $footer; //place to pass data / plugable hook zone
?>

</body>
</html>
