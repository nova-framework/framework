![Nova Framework](https://novaframework.com/themes/nova/assets/images/nova.png)

# Nova Framework

[![Total Downloads](https://img.shields.io/packagist/dt/nova-framework/framework.svg)](https://packagist.org/packages/nova-framework/framework)
[![Dependency Status](https://www.versioneye.com/user/projects/554367f738331321e2000005/badge.svg)](https://www.versioneye.com/user/projects/554367f738331321e2000005)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/nova-framework/framework/blob/master/LICENSE.txt)
[![GitHub stars](https://img.shields.io/github/stars/nova-framework/framework.svg)](https://github.com/nova-framework/framework/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/nova-framework/framework.svg)](https://github.com/nova-framework/framework/network)

[![Join the general chat open to all at https://gitter.im/nova-framework/framework](https://img.shields.io/gitter/room/nwjs/nw.js.svg)](https://gitter.im/nova-framework/framework/novausers)

- [What is the Nova Framework?](#what-is-the-nova-framework)
- [Requirements](#requirements)
- [Installation](#installation)
    - [Recommended](#recommended)
    - [Manual](#manual)
- [Documentation](#documentation)
- [Contributing](#contributing)
    - [Issue Tracker](#issue-tracker)
    - [Pull Requests](#pull-requests)
    - [Code Style](#code-style)
- [License](#license)

## What is the Nova Framework?

Nova Framework is a PHP 5.6 MVC system. It's designed to be lightweight and modular, allowing developers to build better and easy to maintain code with PHP.

The base framework comes with a range of [helper classes](https://github.com/nova-framework/framework/tree/master/system/Helpers).

## Requirements

**The framework requirements are limited.**

- PHP 5.6 or greater.
- Apache Web Server or equivalent with mod rewrite support.
- IIS with URL Rewrite module installed - [http://www.iis.net/downloads/microsoft/url-rewrite](http://www.iis.net/downloads/microsoft/url-rewrite)

**The following PHP extensions should be enabled:**

- Fileinfo (edit php.ini and uncomment php_fileinfo.dll or use php selector within cpanel if available.)
- OpenSSL
- INTL
- MBString

> **Note:** Although a database is not required, if a database is to be used, the system is designed to work with a MySQL database using PDO.

## Installation

This framework was designed and is **strongly recommended** to be installed above the document root directory, with it pointing to the `webroot` folder.

Additionally, installing in a sub-directory, on a production server, will introduce severe security issues. If there is no choice still place the framework files above the document root and have only index.php and .htacess from the webroot folder in the sub folder and adjust the paths accordingly.

#### Recommended
The framework is located on [Packagist](https://packagist.org/packages/nova-framework/framework).

You can install the framework from a terminal by using:

```
composer create-project nova-framework/framework foldername 3.* -s dev
```

The foldername is the desired folder to be created.

> **Note:** For additional installation instructions, for example; setting up a Virtualhost (Recommended for Local Development), Nginx or IIS with URL Rewrite, [please visit the install docs](http://novaframework.com/documentation/v3/install).

## Documentation

Full docs & tutorials are available on [novaframework.com](http://novaframework.com/documentation/v3).

Screencasts are available on [https://novaframework.com/screencasts](https://novaframework.com/screencasts).

## Contributing

#### Issue Tracker

You can find outstanding issues on the [GitHub Issue Tracker](https://github.com/nova-framework/framework/issues).

#### Pull Requests

* Each pull request should contain only one new feature or improvement.
* Pull requests should be submitted to the correct version branch ie [3.0/master](https://github.com/nova-framework/framework/tree/master)

#### Code Style

All pull requests must use the PSR-2 code style.

* Code MUST use the PSR-1 code style.
* Code MUST use 4 spaces for indenting, not tabs.
* There MUST NOT be a hard limit on line length; the soft limit MUST be 120 characters; lines SHOULD be 80 characters or less.
* There MUST be one blank line after the namespace declaration, and there MUST be one blank line after the block of use declarations.
* Opening braces for classes MUST go on the next line, and closing braces MUST go on the next line after the body.
* Opening braces for methods MUST go on the next line, and closing braces MUST go on the next line after the body.
* Visibility MUST be declared on all properties and methods; abstract and final MUST be declared before the visibility; static MUST be declared after the visibility.
* Control structure keywords MUST have one space after them; method and function calls MUST NOT.
* Opening braces for control structures MUST go on the same line, and closing braces MUST go on the next line after the body.
* Opening parentheses for control structures MUST NOT have a space after them, and closing parentheses for control structures MUST NOT have a space before.

## License

The Nova Framework is under the MIT License, you can view the license [here](https://github.com/nova-framework/framework/blob/master/LICENSE.txt).
