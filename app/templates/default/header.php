<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo $data['title'].' - '.SITETITLE; //SITETITLE defined in index.php?></title>
	<link href="<?php echo \helpers\url::get_template_path();?>css/style.css" rel="stylesheet">
</head>
<body>
    <form action="setlanguage" method="POST">
        <input type="submit" name="code" value="nl">
        <input type="submit" name="code" value="en">
    </form>
<div id='wrapper'>
