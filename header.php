<?php

// define directory separator shorthand
define('DS',				DIRECTORY_SEPARATOR);
// define the directory we'll be writing to
define('WRITE_DIR',			$_SERVER["DOCUMENT_ROOT"] . 'bin/');
// define the name of the credentials file that we'll be writing
define('CREDENTIALS_FILENAME',		'awscredentials');
// define the name of the script file that we'll be writing
define('SCRIPT_FILENAME',		'autoscalescript');


// we require the aws keys, functions file and bash script class
require_once('config.php');
require_once('functions' . DS . 'general.php');
require_once('classes' . DS . 'class_bashscript.php');
require_once('3rdparty' . DS . 'geshi' . DS . 'geshi.php');
require_once('3rdparty' . DS . 'aws2' . DS . 'aws-autoloader.php');


?>
