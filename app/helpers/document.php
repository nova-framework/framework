<?php namespace helpers;
/*
 * Document Helper - collection of methods for working with documents
 *
 * @author David Carr - dave@daveismyname.com - http://www.daveismyname.com
 * @version 1.0
 * @date updated Feb 07, 2015
 */
class Document {

	/**
	 * group types into collections, its purpose is to assign the passed extension to the suitable group
	 * @param  string $extension file extension
	 * @return string            group name
	 */
	static public function getFileType($extension){

	    $images = array('jpg', 'gif', 'png', 'bmp');
	    $docs   = array('txt', 'rtf', 'doc', 'docx', 'pdf');
	    $apps   = array('zip', 'rar', 'exe', 'html');
	    $video  = array('mpg', 'wmv', 'avi', 'mp4');
	    $audio  = array('wav', 'mp3');
	    $db     = array('sql', 'csv', 'xls','xlsx');
	    
	    if(in_array($extension, $images)) return "Image";
	    if(in_array($extension, $docs)) return "Document";
	    if(in_array($extension, $apps)) return "Application";
	    if(in_array($extension, $video)) return "Video";
	    if(in_array($extension, $audio)) return "Audio";
	    if(in_array($extension, $db)) return "Database/Spreadsheet";
	    return "Other";
	}

	/**
	 * create a human friendly measure of the size provided
	 * @param  integer  $bytes     file size
	 * @param  integer $precision precision to be used
	 * @return string             size with measure
	 */
	static public function formatBytes($bytes, $precision = 2) { 

	    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
	   
	    $bytes = max($bytes, 0); 
	    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
	    $pow = min($pow, count($units) - 1); 
	   
	    $bytes /= pow(1024, $pow); 
	   
	    return round($bytes, $precision) . ' ' . $units[$pow]; 
	} 

	/**
	 * Converts a human readable file size value to a number of bytes that it
	 * represents. Supports the following modifiers: K, M, G and T.
	 * Invalid input is returned unchanged.
	 *
	 * Example:
	 * <code>
	 * $config->getBytesSize(10);          // 10
	 * $config->getBytesSize('10b');       // 10
	 * $config->getBytesSize('10k');       // 10240
	 * $config->getBytesSize('10K');       // 10240
	 * $config->getBytesSize('10kb');      // 10240
	 * $config->getBytesSize('10Kb');      // 10240
	 * // and even
	 * $config->getBytesSize('   10 KB '); // 10240
	 * </code>
	 *
	 * @param number|string $value
	 * @return number
	 */
	public function getBytesSize($value) {
	  return preg_replace_callback('/^\s*(\d+)\s*(?:([kmgt]?)b?)?\s*$/i', function ($m) {
	    switch (strtolower($m[2])) {
	      case 't': $m[1] *= 1024;
	      case 'g': $m[1] *= 1024;
	      case 'm': $m[1] *= 1024;
	      case 'k': $m[1] *= 1024;
	    }
	    return $m[1];
	  }, $value);
	}

	/**
	 * return the bytes file of a folder
	 * @param string $path
	 * @return string
	 */
	public function getFolderSize($path){
	    $io = popen('/usr/bin/du -sb '.$path, 'r');
	    $size = intval(fgets($io,80));
	    pclose($io);
	    return $size;
	}

	/**
	 * return the file type based on the filename provided
	 * @param  string $file 
	 * @return string       
	 */
	public static function getExtension($file){
		return pathinfo($file, PATHINFO_EXTENSION);
	}
	
	/**
	 * remove extension of file
	 * @param  string  $file filename and extension
	 * @return file name missing extension
	 */
	public static function remove_extension($file){
		
		if(strpos($file, '.')){
			$file = pathinfo($file, PATHINFO_FILENAME);
		}
		return $file;
	}
}
