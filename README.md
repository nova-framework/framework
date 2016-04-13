![Nova Framework](http://novaframework.com/app/templates/publicthemes/nova/images/nova.png)

# Note **fileinfo is required to be enabled** (edit php.ini and uncomment php_fileinfo.dll or use php selector within cpanel if available.)

[![Software License](http://img.shields.io/badge/License-BSD--3-brightgreen.svg)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/simple-mvc-framework/v2.svg)](https://packagist.org/packages/simple-mvc-framework/v2)
[![Dependency Status](https://www.versioneye.com/user/projects/554367f738331321e2000005/badge.svg)](https://www.versioneye.com/user/projects/554367f738331321e2000005)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/simple-mvc-framework/v2/master/license.txt)
[![GitHub stars](https://img.shields.io/github/stars/simple-mvc-framework/framework.svg)](https://github.com/simple-mvc-framework/framework/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/simple-mvc-framework/framework.svg)](https://github.com/simple-mvc-framework/framework/network)

[![Join the chat at https://gitter.im/simple-mvc-framework/framework](https://img.shields.io/gitter/room/nwjs/nw.js.svg)](https://gitter.im/simple-mvc-framework/framework?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

## What is Nova Framework? (formerly known as Simple MVC Framework)

Nova Framework is a PHP 5.5 MVC system. It's designed to be lightweight and modular, allowing developers to build better and easy to maintain code with PHP.

The base framework comes with a range of [helper classes](https://github.com/nova-framework/framework/tree/master/system/Helpers).

## Documentation

Full docs & tutorials are available at [novaframework.com](http://novaframework.com).

Offline docs are available in PDF, EPUB and MOBI formats on [Leanpub](https://leanpub.com/novaframeworkmanual22)

## Requirements

The framework requirements are limited.

- Apache Web Server or equivalent with mod rewrite support.
- IIS with URL Rewrite module installed - [http://www.iis.net/downloads/microsoft/url-rewrite](http://www.iis.net/downloads/microsoft/url-rewrite)
- PHP 5.5 or greater is required
- fileinfo enabled (edit php.ini and uncomment php_fileinfo.dll or use php selector within cpanel if available.)


Although a database is not required, if a database is to be used the system is designed to work with a MySQL database using PDO. The framework can be changed to work with another database type such as Medoo.

# Recommended way to install

The framework is on packagist [https://packagist.org/packages/nova-framework/framework](https://packagist.org/packages/nova-framework/framework).

Install from terminal now by using:

```
composer create-project nova-framework/framework foldername -s dev
```

The foldername is the desired folder to be created.

# Install Manually

Option 1 - files above document root:

* place the contents of public into your public folder (.htaccess and index.php)
* navigate to your project in terminal and type composer install to initiate the composer install.
* edit public/.htaccess set the rewritebase if running on a sub folder otherwise a single / will do.
* edit app/Config.example.php change the SITEURL and DIR constants. the DIR path this is relative to the project url for example / for on the root or /foldername/ when in a folder. Also change other options as desired. Rename file as Config.php

Option 2 - everything inside your public folder

* place all files inside your public folder
* navigate to the public folder in terminal and type composer install to initiate the composer install.
* open index.php and change the paths from using DIR to FILE:

````
define('APPDIR', realpath(__DIR__.'/app/').'/');
define('SYSTEMDIR', realpath(__DIR__.'/system/').'/');
define('PUBLICDIR', realpath(__DIR__).'/');
define('ROOTDIR', realpath(__DIR__).'/');
````

* edit .htaccess set the rewritebase if running on a sub folder otherwise a single / will do.
* edit system/Core/Config.example.php change the SITEURL and DIR constants. the DIR path this is relative to the project url for example / for on the root or /foldername/ when in a folder. Also change other options as desired. Rename file as Config.php

---

##Nginx configuration

No special configuration, you only need to configure Nginx and PHP-FPM.

````
server {
  listen 80;
  server_name yourdomain.tld;

  access_log /var/www/access.log;
  error_log  /var/www/error.log;

  root   /var/www;
  index  index.php index.html;

  location = /robots.txt {access_log off; log_not_found off;}
  location ~ /\\. {deny all; access_log off; log_not_found off;}
  location / {
    try_files $uri $uri/ /index.php?$args;
  }

  location ~ \.php$ {
    fastcgi_pass unix:/var/run/php5-fpm.sock;
    fastcgi_split_path_info ^(.+\.php)(/.+)$;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
  }
}
````

---

##IIS with URL Rewrite module installed - [http://www.iis.net/downloads/microsoft/url-rewrite](http://www.iis.net/downloads/microsoft/url-rewrite)

For IIS the htaccess needs to be converted to web.config:

````
<configuration>
    <system.webserver>
        <directorybrowse enabled="true"/>
        <rewrite>
            <rules>
                <rule name="rule 1p" stopprocessing="true">
                    <match url="^(.+)/$"/>
                    <action type="Rewrite" url="/{R:1}"/>
                </rule>
                <rule name="rule 2p" stopprocessing="true">
                    <match url="^(.*)$"/
                    <action type="Rewrite" url="/index.php?{R:1}" appendquerystring="true"/>
                </rule>
            </rules>
        </rewrite>
    </system.webserver>
</configuration>
````

---

##Setting up a VirtualHost (Optional but recommended)

Navigate to: 
```` 
<path to your xampp installation>\apache\conf\extra\httpd-vhosts.conf
````

and uncomment:

````
NameVirtualHost *:80
````

Then add your VirtualHost to the same file at the bottom:

````
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot "C:\xampp\htdocs\testproject\public"
    ServerName testproject.dev

    <Directory "C:\xampp\htdocs\testproject\public">
        Options Indexes FollowSymLinks Includes ExecCGI
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>
</VirtualHost>
````

Finally, find your hosts file and add:

````
127.0.0.1       testproject.dev
````

You should then have a virtual host set up, and in your web browser, you can navigate to testproject.dev to see what you are working on.

---

This has been tested with php 5.6 and php 7 please report any bugs.

See complete [Change Log](http://novaframework.com/documentation/v3/overview-change-log)
