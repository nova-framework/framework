# framework
Version 3.0 Release Candidate 1 (R1) of the Framework

#Install

Option 1 - files above document root:

* place the nova folder above your htdocs / public / public_html folder
* place the contents of public_html into your public folder (.htaccess and index.php)
* navigate to nova in terminal and type composer install to initate the composer install.
* edit public_html/.htaccess set the rewritebase if running on a sub folder otherwise a single / will do.
* edit system/Core/Config.example.php change the SITEURL and DIR constants. the DIR path this is relative to the project url for example / for on the root or /foldername/ when in a folder. Also change other options as desired. Rename file as Config.php

Option 2 - everything inside your public folder

* place the contents of nova and public_html folder inside your htdocs / public / public_html folder
* navigate to the public folder in terminal and type composer install to initate the composer install.
* open index.php and change the paths from using DIR to FILE:

````
define('APPDIR', dirname(__FILE__).'/app/');
define('SYSTEMDIR', dirname(__FILE__).'/system/');
define('PUBLICDIR', dirname(__FILE__).'/');
define('ROOTDIR', dirname(__FILE__).'/');
````

* edit .htaccess set the rewritebase if running on a sub folder otherwise a single / will do.
* edit system/Core/Config.example.php change the SITEURL and DIR constants. the DIR path this is relative to the project url for example / for on the root or /foldername/ when in a folder. Also change other options as desired. Rename file as Config.php

This has been tested with php 5.6 and php 7 please report any bugs.

#Routing images / js / css files
From within Templates your css/js and images must be in a Assets folder to be routed correctly.
This applies to Modules as well, to have a css file from a Module the css file would be placed inside nova/app/Modules/ModuleName/Assets/css/file.css.
Additionally there is an Assets folder in the root of nova this is for storing resources outside of templates that can still be routed from above the document root.

#Namespace change

classes in app/Controller app/Model and app/Modules now have a namespace starting with App:

* App\Controllers
* App\Models
* App\Modules

That is only for classes within app, this is not needed for classes within system.

#Error Log
The error log is no longer a .html file but rather a log file. On a production server it should be outside the document root, in order to see the any errors there are a few options:

* Open system/logs/error.log
* OR open system/Core/Logger.php set $display to true to print errors to the screen
* set $emailError to true and setup the siteEmail const in system/Core/Config.php this relies on an email server (not provided by the framework)
