<?php
/*
 * Created on 7 Nov 2012
 */
 

class Class_BashScript
{
	private static 	$_instance = null;
	private			$file_path
	,				$file_handle;

	function __construct () {
    	// set the path to the file we'll be writing
    	$this->file_path = WRITE_DIR . SCRIPT_FILENAME;
    	// open the file
    	$this->file_handle = fopen($this->file_path, 'w');
    	// add script headers so it'll run
		$this->addLine("#!/bin/bash");		
	}
	
	function __destruct  () {
		// close the file
		fclose($this->file_handle);	
		// set permissions
		chmod($this->file_path, 0755);	
	}
	
	/*
	 *	Gets an instance of the class
	 */
	public static function &getInstance(){
		if (is_null(self::$_instance)){
			self::$_instance = new self();
		}
		return self::$_instance;
	}


	/*
	*	Adds a line (or several) to the output script file
	*/
	public function addLine($param_mLines) {
		// if it's an array then call the function for each value in it. Otherwise, just add the line to the file
		if (is_array($param_mLines)) {
			foreach($param_mLines as $sLine) {
				$this->addLine($sLine);
			}
		} else {
			fwrite($this->file_handle, $param_mLines . "\n");
		}
	}

	/*
	 *	Returns the file path
	 */
	public function get_file_path()	{ return $this->file_path; }	
}
 
?>
