#!/usr/bin/env php
<?php

define("DS", DIRECTORY_SEPARATOR);

define("BASEPATH", dirname(dirname(__FILE__)) .DS);

$languages = array(
    'cs',
    'da',
    'de',
    'en',
    'es',
    'fa',
    'fr',
    'hu',
    'it',
    'ja',
    'nl',
    'pl',
    'ro',
    'ru',
);

$workPaths = array(
    'shared',
    'app',
);

//
function starts_with($haystack, $needle) {
    return (($needle === '') || (strpos($haystack, $needle) === 0));
}

//
function phpGrep($q, $path) {
    $ret = array();

    $fp = opendir($path);

    while($f = readdir($fp)) {
        if( preg_match("#^\.+$#", $f) ) continue; // ignore symbolic links

        $file_full_path = $path.DS.$f;

        if(is_dir($file_full_path)) {
            $ret = array_unique(array_merge($ret, phpGrep($q, $file_full_path)));
        }
        else if(stristr(file_get_contents($file_full_path), $q)) {
            $ret[] = $file_full_path;
        }
    }

    return $ret;
}

if(is_dir(BASEPATH .'modules')) {
    $path = str_replace('/', DS, BASEPATH .'modules/*');

    $dirs = glob($path , GLOB_ONLYDIR);

    foreach($dirs as $module) {
        $workPaths[] = 'modules' .DS .basename($module);
    }
}

if(is_dir(BASEPATH .'plugins')) {
    $path = str_replace('/', DS, BASEPATH .'plugins/*');

    $dirs = glob($path , GLOB_ONLYDIR);

    foreach($dirs as $template) {
        $workPaths[] = 'plugins' .DS .basename($template);
    }
}

//
$options = getopt('', array('path::'));

if(! empty($options['path'])) {
    $worksPaths = array_map('trim', explode(',', $options['path']));
}

foreach($workPaths as $workPath) {
    if(! is_dir(BASEPATH .$workPath)) {
        continue;
    }

    $start = ($workPath == 'app') ? "__('" : "__d('";

    $results = phpGrep($start, BASEPATH .$workPath);

    if(empty($results)) {
        /*
        foreach($languages as $language) {
            $langFile = BASEPATH .$workPath. DS .'Language' .DS .ucfirst($language) .DS .'messages.php';

            $output = "<?php

return " .var_export(array(), true).";\n";

            file_put_contents($langFile, $output);

            echo 'Written the Language file: "'.str_replace(BASEPATH, '', $langFile).'"'.PHP_EOL;
        }
        */
        continue;
    }

    if($workPath == 'app') {
        $pattern = '#__\(\'(.*)\'(?:,.*)?\)#smU';
    }
    else {
        $pattern = '#__d\(\'(?:.*)?\',.?\s?\'(.*)\'(?:,.*)?\)#smU';
    }

    echo "Using PATERN: '" .$pattern."'\n";

    $messages = array();

    foreach ($results as $key => $filePath) {
        $content = file_get_contents($filePath);

        if (preg_match_all($pattern, $content, $matches)) {
            foreach ($matches[1] as $message) {
                //$message = trim($message);

                if ($message == '$msg, $args = null') {
                    // This is the function
                    continue;
                }

                $messages[] = str_replace("\\'", "'", $message);
            }
        }
    }

    if (! empty($messages)) {
        echo 'Messages found on path "'.$workPath.'". Processing...'.PHP_EOL;

        $strings = array_flip($messages);

        foreach ($languages as $language) {
            $langFile = BASEPATH .$workPath .DS .'Language' .DS .strtoupper($language) .DS .'messages.php';

            if (is_readable($langFile)) {
                $data = include($langFile);

                $data = is_array($data) ? $data : array();
            } else {
                $data = array();
            }

            //
            $messages = array();

            foreach ($strings as $key => $value) {
                if (array_key_exists($key, $data)) {
                    $value = $data[$key];

                    if (! empty($value) && is_string($value)) {
                        $messages[$key] = $value;

                        continue;
                    }
                }

                $messages[$key] = '';
            }

            ksort($messages);

            $output = "<?php

return " .var_export($messages, true).";\n";

            //$output = preg_replace("/^ {2}(.*)$/m","    $1", $output);

            file_put_contents($langFile, $output);

            echo 'Written the Language file: "'.str_replace(BASEPATH, '', $langFile).'"'.PHP_EOL;
        }
    }

    echo PHP_EOL;
}
