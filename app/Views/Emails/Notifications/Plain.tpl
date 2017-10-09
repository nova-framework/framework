<?php

if (! empty($greeting)) {
    echo $greeting, "\n\n";
} else {
    echo ($level == 'error') ? __d('notifications', 'Whoops!') : __d('notifications', 'Hello!'), "\n\n";
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

echo __d('notifications', 'Regards,'), "\n";

echo config('app.name'), "\n";
