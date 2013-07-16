<?php
	include_once('../header.php');

	// change to the script directory, then run the script
	chdir(WRITE_DIR);
	$output = shell_exec('./autoscalescript');
	// now use Geshi to get some syntax highlighting!
	$oGeshi = new GeSHi($output, 'bash');

	echo $oGeshi->parse_code();
?>
