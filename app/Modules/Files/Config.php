<?php
/**
 * Config - the Module's specific Configuration.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Core\Config;

/**
 * Configuration constants and options.
 */
 
Config::set('elFinder', array(
    'locale' => 'en_US.UTF-8',
    'debug'  => false,

    'roots' => array(
        array(
            'alias'         => 'Site Assets',
            'driver'        => 'LocalFileSystem',
            'path'          => ROOTDIR .'assets/',
            'URL'           => site_url('assets/'),
            'mimeDetect'    => 'internal',
            'tmbPath'       => APPDIR .'Storage/Files/thumbnails',
            'quarantine'    => APPDIR .'Storage/Files/quarantine',
            'tmbURL'        => site_url('admin/files/thumbnails/'),
            'utf8fix'       => true,
            'tmbCrop'       => false,
            'tmbSize'       => 48,
            'acceptedName'  => '/^[^\.].*$/',
            'accessControl' => 'access',
            'dateFormat'    => 'j M Y H:i',
            'defaults'      => array('read' => true, 'write' => true),
            'icon'          => site_url('modules/files/assets/img/volume_icon_local.png'),
        ),
        array(
            'alias'         => 'Site Root',
            'driver'        => 'LocalFileSystem',
            'path'          => ROOTDIR,
            'URL'           => site_url('admin/files/preview/'),
            'mimeDetect'    => 'internal',
            'tmbPath'       => APPDIR .'Storage/Files/thumbnails',
            'quarantine'    => APPDIR .'Storage/Files/quarantine',
            'tmbURL'        => site_url('admin/files/thumbnails/'),
            'utf8fix'       => true,
            'tmbCrop'       => false,
            'tmbSize'       => 48,
            'acceptedName'  => '/^[^\.].*$/',
            'accessControl' => 'access',
            'dateFormat'    => 'j M Y H:i',
            'defaults'      => array('read' => true, 'write' => false),
            'icon'          => site_url('modules/files/assets/img/volume_icon_local.png'),
        )
    )
));
