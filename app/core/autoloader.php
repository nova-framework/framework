<?php
//turn on output buffering
ob_start();

//run autoloader
spl_autoload_register(function ($class) {

   $filename = "app/controllers/".strtolower($class).".php";
   if(file_exists($filename)){
      require $filename;
   } 

   $filename = "app/core/".strtolower($class).".php";
   if(file_exists($filename)){
      require $filename;
   }

   $filename = "app/helpers/".strtolower($class).".php";
   if(file_exists($filename)){
      require $filename;
   } 
 
});
//start sessions
Session::init();

require('app/core/config.php');