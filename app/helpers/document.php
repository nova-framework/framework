<?php namespace helpers;
/*
 * Document Helper - collection of methods for working with documents
 *
 * @author David Carr - dave@daveismyname.com - http://www.daveismyname.com
 * @version 1.0
 * @date June 27, 2014
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
	 * return the file type based on the filename provided
	 * @param  string $file 
	 * @return string       
	 */
	public static function getExtension($file){
		return substr(strrchr($file,'.'),1);
	}
	
	//IMAGE UPLOAD & GENERATE THUMBNAIL FUNCTION
	public function uploadPhoto($fupload_name)
	{
		//IMAGE UPLOAD DIRECTORY, DEFINE ON core/config.php
		$vdir_upload = IMAGE_UPLOAD_DIR;
		$vfile_upload = $vdir_upload . $fupload_name;
		
		//SAVE IMAGE WITH ORIGINAL SIZE
		move_uploaded_file($_FILES["fupload"]["tmp_name"], $vfile_upload);
		
		//ORIGINAL FILES IDENTITY
		$im_src = imagecreatefromjpeg($vfile_upload);
		$src_width = imageSX($im_src);
		$src_height = imageSY($im_src);
		
		//SET & SAVE THUMBNAIL
		$dst_width = 177;
		$dst_height = ($dst_width/$src_width)*$src_height;
		
		//CONVERT IMAGE SIZE
		$im = imagecreatetruecolor($dst_width,$dst_height);
		imagecopyresampled($im, $im_src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
		
		//SAVE IMAGE
		imagejpeg($im,$vdir_upload . "small_" . $fupload_name);
		
		//DELETE FROM MEMORY
		imagedestroy($im_src);
		imagedestroy($im);
	}

}
