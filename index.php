<?php
	if(file_exists("includes/settings.php")) require_once("includes/settings.php");
	else exit("<center><h2>file missing</h2></center>");
	if(file_exists("includes/functions.php")) require_once("includes/functions.php");
	else exit("<center><h2>file missing</h2></center>");

	//SECTION FOR IMPORTING UPLOADED FILE
	$files = new FilesystemIterator(dirname(__FILE__) . DIRECTORY_SEPARATOR . "API_Files");
				
	foreach($files as $file){
		if(import_data($file)){
			echo("<p style=\"color: green\">{$file} = pass<br>");
			//unlink($file);
		} else {
			echo("<p style=\"color: red\">{$file} = fail<br>");
		}
	}

	echo "<h2>Done</h2>";
?>