<?php
//turn on output buffering
ob_start();

function autoloader($class) {

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
 
}

//run autoloader
spl_autoload_register('autoloader');
//start sessions
Session::init();

require('app/core/config.php');
