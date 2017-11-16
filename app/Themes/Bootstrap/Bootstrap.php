<?php

/**
 * Which CSS files should be loaded by the Content Editors?
 */
Event::listen('content.editor.stylesheets.bootstrap', function ()
{
    return array(
        vendor_url('dist/css/bootstrap.min.css', 'twbs/bootstrap'),
        vendor_url('dist/css/bootstrap-theme.min.css', 'twbs/bootstrap'),
        'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
    );
});
