![Simple MVC Framework](http://simplemvcframework.com/app/templates/publicthemes/smvc/images/logo.png)

# Version 2.2

[![Software License](http://img.shields.io/badge/License-BSD--3-brightgreen.svg)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/simple-mvc-framework/v2.svg)](https://packagist.org/packages/simple-mvc-framework/v2)
[![Dependency Status](https://www.versioneye.com/user/projects/554367f738331321e2000005/badge.svg)](https://www.versioneye.com/user/projects/554367f738331321e2000005)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/simple-mvc-framework/v2/master/license.txt)
[![GitHub stars](https://img.shields.io/github/stars/simple-mvc-framework/framework.svg)](https://github.com/simple-mvc-framework/framework/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/simple-mvc-framework/framework.svg)](https://github.com/simple-mvc-framework/framework/network)

[![Join the chat at https://gitter.im/simple-mvc-framework/framework](https://img.shields.io/gitter/room/nwjs/nw.js.svg)](https://gitter.im/simple-mvc-framework/framework?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

## What is Simple MVC Framework?

Simple MVC Framework is a PHP 5.5 MVC system. It's designed to be lightweight and modular, allowing developers to build better and easy to maintain code with PHP.

The base framework comes with a range of [helper classes](https://github.com/simple-mvc-framework/framework/tree/master/app/Helpers).

## Packagist

The framework is now on packagist [https://packagist.org/packages/simple-mvc-framework/v2](https://packagist.org/packages/simple-mvc-framework/v2).

Install from terminal now by using:

```
composer create-project simple-mvc-framework/v2 foldername -s dev
```

The foldername is the desired folder to be created.

## Documentation

Full docs & tutorials are available at [simplemvcframework.com](http://simplemvcframework.com).

## Requirements

The framework requirements are limited:

- [Apache Web Server](https://httpd.apache.org/) or equivalent with [mod_rewrite](http://httpd.apache.org/docs/current/mod/mod_rewrite.html) support
- [PHP 5.5 or greater](http://php.net/downloads.php) is required

Although a database is not required, if a database is to be used the system is designed to work with a [MySQL database](http://www.mysql.com/). The framework can be changed to work with another database type.

## Installation

1. [Download](https://github.com/simple-mvc-framework/framework/archive/master.zip) the framework.
2. Unzip the package.
3. To run [composer](https://getcomposer.org/), navigate to your project on a terminal/command prompt, then run `composer install`. That will update the vendor folder. Or use the vendor folder as is (composer is not required for this step).
Upload the framework files to your server. Normally the `index.php` file will be at your root.
4. Open the `app/Core/routes.php` file with a text editor and setup your routes.
5. Open `app/Core/Config.example.php` and set your base path (if the framework is installed in a folder, the base path should reflect the folder path `/path/to/folder/` otherwise a single `/` will do) and database credentials (if a database is needed). Set the default theme. When you are done, rename the file to `Core/Config.php`.
6. Edit `.htaccess` file and save the base path (if the framework is installed in a folder, the base path should reflect the folder path `/path/to/folder/` otherwise a single `/` will do).

## Other Contributions

Have you found this framework helpful? Why not take a minute to endorse my hard work on [coderwall](https://coderwall.com/daveismynamecom)! Just click the badge below:

[![endorse](https://api.coderwall.com/daveismynamecom/endorsecount.png)](https://coderwall.com/daveismynamecom)
