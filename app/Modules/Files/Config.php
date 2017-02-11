<?php
/**
 * Config - the Module's specific Configuration.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


Config::set('elFinder', array(
    'locale' => 'en_US.UTF-8',
    'debug'  => false,

    'roots' => array(
        array(
            'alias'         => 'Site Assets',
            'driver'        => 'LocalFileSystem',
            'path'          => ROOTDIR .'assets' .DS,
            'URL'           => site_url('assets'),
            'mimeDetect'    => 'internal',
            'tmbPath'       => STORAGE_PATH .'files/thumbnails',
            'quarantine'    => STORAGE_PATH .'files/quarantine',
            'tmbURL'        => site_url('admin/files/thumbnails'),
            'utf8fix'       => true,
            'tmbCrop'       => false,
            'tmbSize'       => 48,
            'acceptedName'  => '/^[^\.].*$/',
            'accessControl' => 'access',
            'dateFormat'    => 'j M Y H:i',
            'defaults'      => array('read' => true, 'write' => true),
            'icon'          => resource_url('img/volume_icon_local.png', 'Files'),
        ),
        array(
            'alias'         => 'Site Root',
            'driver'        => 'LocalFileSystem',
            'path'          => ROOTDIR,
            'URL'           => site_url('admin/files/preview'),
            'mimeDetect'    => 'internal',
            'tmbPath'       => STORAGE_PATH .'files/thumbnails',
            'quarantine'    => STORAGE_PATH .'files/quarantine',
            'tmbURL'        => site_url('admin/files/thumbnails'),
            'utf8fix'       => true,
            'tmbCrop'       => false,
            'tmbSize'       => 48,
            'acceptedName'  => '/^[^\.].*$/',
            'accessControl' => 'access',
            'dateFormat'    => 'j M Y H:i',
            'defaults'      => array('read' => true, 'write' => false),
            'icon'          => resource_url('img/volume_icon_local.png', 'Files'),
        )
    )
));
