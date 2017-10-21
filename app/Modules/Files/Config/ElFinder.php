<?php
/**
 * ElFinder - the Module's specific Configuration.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

/**
 * Simple function to demonstrate how to control file access using "accessControl" callback.
 * This method will disable accessing files/folders starting from '.' (dot)
 *
 * @param  string  $attr  attribute name (read|write|locked|hidden)
 * @param  string  $path  file path relative to volume root directory started with directory separator
 * @return bool|null
 **/

$callback = function ($attr, $path, $data, $volume)
{
    return (strpos(basename($path), '.') === 0)       // if file/folder begins with '.' (dot)
            ? ! ($attr == 'read' || $attr == 'write') // set read+write to false, other (locked+hidden) set to true
            :  null;                                  // else elFinder decide it itself
};


return array(
    'locale' => 'en_US.UTF-8',
    'debug'  => false,

    'roots' => array(
        array(
            'alias'         => 'assets',
            'driver'        => 'LocalFileSystem',
            'path'          => ROOTDIR .'assets/',
            'URL'           => site_url('assets/'),
            'mimeDetect'    => 'internal',
            'tmbPath'       => STORAGE_PATH .'files/thumbnails',
            'quarantine'    => STORAGE_PATH .'files/quarantine',
            'tmbURL'        => site_url('admin/files/thumbnails/'),
            'utf8fix'       => true,
            'tmbCrop'       => false,
            'tmbSize'       => 48,
            'acceptedName'  => '/^[^\.].*$/',
            'accessControl' => $callback,
            'dateFormat'    => 'j M Y H:i',
            'defaults'      => array('read' => true, 'write' => true),
            'icon'          => site_url('modules/files/assets/img/volume_icon_local.png'),
        ),
        array(
            'alias'         => 'rootdir',
            'driver'        => 'LocalFileSystem',
            'path'          => ROOTDIR,
            'URL'           => site_url('admin/files/preview/'),
            'mimeDetect'    => 'internal',
            'tmbPath'       => STORAGE_PATH .'files/thumbnails',
            'quarantine'    => STORAGE_PATH .'files/quarantine',
            'tmbURL'        => site_url('admin/files/thumbnails/'),
            'utf8fix'       => true,
            'tmbCrop'       => false,
            'tmbSize'       => 48,
            'acceptedName'  => '/^[^\.].*$/',
            'accessControl' => $callback,
            'dateFormat'    => 'j M Y H:i',
            'defaults'      => array('read' => true, 'write' => false),
            'icon'          => site_url('modules/files/assets/img/volume_icon_local.png'),
        )
    )
);
