#!/bin/sh

CWD=`pwd`

BASEPATH=`dirname $CWD`

# Execute the Composer in a sub-shell
(cd $BASEPATH

    composer update --no-dev && composer dump-autoload -o
    #composer update && composer dump-autoload -o
)