# framework
Version 3 of the Framework

This is a temp readme, for beta testing.

#Install
* Download the files
* for a localserver place the files within public in the root of your project (move them up a directory so they are with app and system)
* open system/Core/Config.example.php and edit the DIR path this is relative to the project url for example / for on the root or /foldername/ when in a folder. Save file as Config.php
* open .htaccess and set the rewrite base same path as in the config the path is relative to the project url
* open index.php and set the paths to if running all folders in the root no change will be needed. If app and system are in a higher folder above then edit the path to be ../app and ../system

This has been tested with php 5.6 and php 7 (RC8) please report any bugs, this system has not been tested enough to be considered stable.

#Namespace change

classes in app/Controller app/Model and app/Modules now have a namespace starting with App:

* App\Controllers
* App\Models
* App\Modules

That is only for classes within app this is not needed for classes within system.

#Error Log
The error log is no longer a .html file but rather a log file. On a production server it should be outside the document root, in order to see the any errors there are a few options:

* Open system/logs/error.log
* OR open system/Core/Logger.php set $display to true to print errors to the screen
* set $emailError to true and setup the siteEmail const in system/Core/Config.php this relies on an email server (not provided by the framework)
