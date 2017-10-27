<?php

if (! empty($greeting)) {
    echo $greeting, "\n\n";
} else {
    echo ($level == 'error') ? __('Whoops!') : __('Hello!'), "\n\n";
}

if (! empty($introLines)) {
    echo implode("\n", $introLines), "\n\n";
}

if (isset($actionText)) {
    echo "{$actionText}: {$actionUrl}", "\n\n";
}

if (! empty($outroLines)) {
    echo implode("\n", $outroLines), "\n\n";
}

echo __('Regards,'), "\n";

echo config('app.name'), "\n";
