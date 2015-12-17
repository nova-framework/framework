#!/bin/sh

CWD=`pwd`

BASEPATH=`dirname $CWD`

# Execute the Composer in a sub-shell
(cd $BASEPATH

    composer update && composer dump-autoload -o
)