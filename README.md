![Simple MVC Framework](http://simplemvcframework.com/app/templates/smvcf/img/logo.png)

## Packagist

[![Software License](http://img.shields.io/badge/License-BSD--3-brightgreen.svg?style=flat-square)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/simple-mvc-framework/v2.svg?style=flat-square)](https://packagist.org/packages/simple-mvc-framework/v2)

The framework is now on packagist [https://packagist.org/packages/simple-mvc-framework/v2](https://packagist.org/packages/simple-mvc-framework/v2)

Install from terminal now by using:

````
composer create-project simple-mvc-framework/v2 foldername -s dev
````

The foldername is the desired folder to be created.

If you use Sublime you can also use the fetch package to download the framework from within Sublime Text 
[http://code.tutsplus.com/articles/introducing-nettuts-fetch--net-23490](http://code.tutsplus.com/articles/introducing-nettuts-fetch--net-23490)

#V2.1
#What is Simple MVC Framework?

Simple MVC Framework is a PHP 5.3 MVC system. It's designed to be lightweight and modular, allowing developers to build better and easy to maintain code with PHP.

The base framework comes with a range of helper classes.

## Documentation

Full docs & tutorials are available at [simplemvcframework.com](http://simplemvcframework.com)

##Requirements

 The framework requirements are limited

 - Apache Web Server or equivalent with mod rewrite support.
 - PHP 5.3 or greater is required

 Although a database is not required, if a database is to be used the system is designed to work with a MySQL database. The framework can be changed to work with another database type.

## Installation

1. Download the framework
2. Unzip the package.
3. To run composer, navigate to your project on a terminal/command prompt then run 'composer install' that will update the vendor folder. Or use the vendor folder as is (composer is not required for this step)
Upload the framework files to your server. Normally the index.php file will be at your root.
4. Open the index.php file with a text editor, setup your routes.
5. Open core/config.example.php and set your base URL and database credentials (if a database is needed). Set the default theme. When you are done, rename the file to core/config.php
6. Edit .htaccess file and save the base path. (if the framework is installed in a folder the base path should reflect the folder path /path/to/folder/ otherwise a single / will do.

##Sublime Text Snippets

For Sublime Text users, their is a new plugin for keyboard shortcuts see [https://github.com/simple-mvc-framework/SMVC-Snippets](https://github.com/simple-mvc-framework/SMVC-Snippets) for more details and install instructions.

### Other Contributions
Have you found this library helpful? Why not take a minute to endorse my hard work on [coderwall](https://coderwall.com/daveismynamecom)! Just click the badge below:

[![endorse](https://api.coderwall.com/daveismynamecom/endorsecount.png)](https://coderwall.com/daveismynamecom)
