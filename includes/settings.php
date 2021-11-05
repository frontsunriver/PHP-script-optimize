<?php
	//STARTING THE SESSION
	session_start();
	
	//WHETHER OR NOT TO SHOW ERRORS
	ini_set('display_errors', false);
	ini_set('error_reporting', E_ALL);

	//PATH OF THE SYSTEM RELATIVE TO THE htdocs DIRECTORY
	$base_url = "/_vendors/";
	$root_dir = $_SERVER['DOCUMENT_ROOT'] . $base_url;

	//TIMEZONE SETTINGS
	date_default_timezone_set('Africa/Harare');

	//DATABASE CREDENTIALS

	$database_name = "sample1";
	$database_user = "root";
	$database_user_password = ''; 
	$database_host = "localhost";

	//TWILION DETAILS
	$twilio_sender_number = "";
	$twilio_sid = "";
	$twilio_token = "";

	$website_name = "Raymond";
?>