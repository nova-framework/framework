<?php

$line = str_pad('', 60, '-');

if (! empty($greeting)) {
    echo $greeting, "\n\n";
} else {
    echo ($level == 'error') ? __('Whoops!') : __('Hello!'), "\n\n";
}

if (! empty($introLines)) {
    $lines = array_map(function ($value) use ($line)
    {
        return strip_tags(str_replace(array('<hr>', '<br />'), array($line, "\n"), $value));

    }, $introLines);

    echo implode("\n", $lines), "\n\n";
}

if (isset($actionText)) {
    echo "{$actionText}: {$actionUrl}", "\n\n";
}

if (! empty($outroLines)) {
    $lines = array_map(function ($value) use ($line)
    {
        return strip_tags(str_replace(array('<hr>', '<br />'), array($line, "\n"), $value));

    }, $outroLines);

    echo implode("\n", $lines), "\n\n";
}

echo __('Regards,'), "\n";

echo config('app.name'), "\n";
