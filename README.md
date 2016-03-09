#DO NOT USE, A SECURITY PROBLEM HAS BEEN FOUND!
This is been worked on.

# framework
Version 3.0 Release Candidate 1 (R1) of the Framework

#Install
* Download the files
* for a localserver place index.php and .htaccess in your public folder and the nova folder above it.
*This path can be configured from public_html/index.php, for security reasons it's a good idea to place sensative files above the document root.
* open system/Core/Config.example.php and edit the DIR path this is relative to the project url for example / for on the root or /foldername/ when in a folder. Save file as Config.php
* open .htaccess and set the rewrite base same path as in the config the path is relative to the project url
* open index.php and set the paths to if running all folders in the root no change will be needed. If app and system are in a higher folder above then edit the path to be ../app and ../system
* in terminal run composer install


This has been tested with php 5.6 and php 7 please report any bugs.

#Routing images / js / css files
When the files are above the document root the browser cannot see them to use them a new route has been created inside the routes file.

To route to these resourses simple start the path with resource/ then the path for instance:

````
<img src='resource/<?=Url::templatePath();?>images/logo.png' alt='logo'>
````

This will then return the image from template/images.


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
