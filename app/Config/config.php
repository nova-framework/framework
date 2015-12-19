<?php
/**
 * Framework configuration - the configuration parameters of the Framework components.
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 15th, 2015
 */

use Nova\Config;

/**
 * Set the Framework's timezone.
 */
Config::set('timezone', 'Europe/Rome');

/**
 * All known Languages
 */
Config::set('languages', array(
    'cz' => array('info' => 'Czech',    'name' => 'čeština',    'locale' => 'cz_CZ'),
    'de' => array('info' => 'German',   'name' => 'Deutsch',    'locale' => 'de_DE'),
    'en' => array('info' => 'English',  'name' => 'English',    'locale' => 'en_US'),
    'es' => array('info' => 'Spanish',  'name' => 'Español',    'locale' => 'es_ES'),
    'fr' => array('info' => 'French',   'name' => 'Français',   'locale' => 'fr_FR'),
    'it' => array('info' => 'Italian',  'name' => 'italiano',   'locale' => 'it_IT'),
    'nl' => array('info' => 'Dutch',    'name' => 'Nederlands', 'locale' => 'nl_NL'),
    'pl' => array('info' => 'Polish',   'name' => 'polski',     'locale' => 'pl_PL'),
    'ro' => array('info' => 'Romanian', 'name' => 'Română',     'locale' => 'ro_RO'),
    'ru' => array('info' => 'Russian',  'name' => 'ру́сский',    'locale' => 'ru_RU')
));


/**
 * Email (PHPMailer) configuration
 */
Config::set('emailer', array(
    'mailer_charset'       => 'iso-8859-1',
    'mailer_from_name'     => 'SMVC Website',
    'mailer_from_email'    => 'smvc@localhost',
    'mailer_mailer'        => 'mail',           // Could be 'mail' => 'sendmail' or 'smtp'

    /** Only when using smtp as mailer: */
    'mailer_smtp_host'     => 'localhost',
    'mailer_smtp_port'     => 25,
    'mailer_smtp_secure'   => '',    // Options: '' => 'ssl' or 'tls'
    'mailer_smtp_auth'     => false, // Use SMTPAuth, (false or true)
    'mailer_smtp_user'     => '',    // Only when using SMTPAuth
    'mailer_smtp_pass'     => '',    // Only when using SMTPAuth
    'mailer_smtp_authtype' => ''     // Options are LOGIN (default), PLAIN, NTLM, CRAM-MD5. Blank when not use SMTPAuth.
));


/**
 * Database configurations
 *
 * By default, the 'default' connection will be used when no connection name is given to the engine factory.
 */
Config::set('database', array(
    'default' => array(
        'engine' => 'mysql',
        'config' => array(
            'host' => 'localhost',
            'database' => 'dbname',
            'user' => 'root',
            'password' => 'password',
            'fetchmethod' => \PDO::FETCH_OBJ // Not required, default is OBJ.
        )
    ),
    /** Extra connections can be added here, some examples: */
    /*
    'sqlite' => array(
        'engine' => 'sqlite',
        'config' => array(
            'file' => APPPATH . 'database.sqlite'
        )
    )
    */
));

/**
 * Active Modules
 */
Config::set('modules', array(
    //'Blog',
    //'Page'
));
