# framework
Version 3 of the Framework

This is a temp readme, for beta testing.

#Install
To be updated - 3.0 is currently been refractored once finalished these instructions will be updated.

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

#Video introduction and setup
this is an introduction to 3.0 whilst in beta, how to install and get up and running with the major changes.

Not for production use yet.

https://www.youtube.com/watch?v=28l8lJz-oRM
