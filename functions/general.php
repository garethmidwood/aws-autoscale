<?php
	
// writes a file with supplied data. Returns false if it fails
function writeFile($param_sFileName, $param_aData, $param_sDirectoryPath = WRITE_DIR, $param_sPermissions = 0755) {
	// try and write a file
	$myFile = $param_sDirectoryPath . $param_sFileName;
	$fh = @fopen($myFile, 'w');
	
	// if we couldn't write the file then we don't have write permissions
	$bHasWritePermissions = $fh ? true : false;
	
	// stick some text in the file, then close it and change permissions
	if ($bHasWritePermissions) {
		foreach ($param_aData as $sData) {
			fwrite($fh, $sData . "\n");
		}
		fclose($fh);
		// set permissions
		chmod($myFile, $param_sPermissions);
	}
	
	return $bHasWritePermissions;
}

// returns true if we can write files, false otherwise
function checkWritePermissions($param_sDirectoryPath = WRITE_DIR) {
	// set file name
	$sFileName = "write-permissions-test.txt";
	
	// delete the file if it already exists. If it doesn't exist, then fail silently
	@unlink($param_sDirectoryPath . $sFileName);
	
	// try to write a file, to check write permissions
	$bHasWritePermissions = writeFile($sFileName, array("this is test text to check that we have write permissions"), $param_sDirectoryPath);
	
	// now delete the file
	$sCommand = "rm -r $param_sDirectoryPath . $sFileName";
	system($sCommand);
	
	return $bHasWritePermissions;
}
	
// deletes all files in the specified directory
function deleteFilesInDirectory($param_sDirectoryPath = WRITE_DIR) {
	$sCommand = "rm -r $param_sDirectoryPath*";
	system($sCommand);
}

// writes the credentials file
function writeCredentialsFile($param_sCredentialsFilename, $param_sAWSKey, $param_sAWSSecret) {

	// write the credentials to a file
	writeFile(	$param_sCredentialsFilename
			,	array("AWSAccessKeyId=" . $param_sAWSKey, "AWSSecretKey=" . $param_sAWSSecret)
			,	WRITE_DIR
			,	0755
			);
			
	// set the credentials key in the system PATH
	setCredentialsPath($param_sCredentialsFilename);
}

// sets the credentials file path in the system PATH
function setCredentialsPath($param_sFileName) {
	$sCommand = 'export AWS_CREDENTIAL_FILE="' . WRITE_DIR . $param_sFileName . '"';
	addToOutputScript($sCommand);
}

// sets the auto scaling URL
function setAWSAutoScalingURL($param_sRegion) {
	// add the autoscaling part to the URL
	$sURL = 'https://autoscaling.' . $param_sRegion . '.amazonaws.com';
	// log the command
	$sCommand = 'export AWS_AUTO_SCALING_URL=' . $sURL;
	addToOutputScript($sCommand);
	addToOutputScript('export EC2_REGION=' . $param_sRegion);
}

// adds environment variables (sorry, can't figure out why this won't load using source /etc/environment ... it works fine when run manually, but when the site tries to do it, it fails.)
function setEnvironmentVariables() {
	addToOutputScript('export AWS_AUTO_SCALING_HOME="' . AWS_AUTO_SCALING_HOME . '"');
	addToOutputScript('export EC2_HOME="' . EC2_HOME . '"');
	addToOutputScript('export JAVA_HOME="' . JAVA_HOME . '"');
	addToOutputScript('export AWS_CLOUDWATCH_HOME="' . AWS_CLOUDWATCH_HOME . '"');
	addToOutputScript('export PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:${AWS_AUTO_SCALING_HOME}/bin:${EC2_HOME}/bin:${AWS_CLOUDWATCH_HOME}/bin"');
}






// check all required form fields have been filled in
function validateFormSubmission(&$param_aSubmittedValues) {

	$aErrors = array();
	$aRequiredTextFields = array(
		"aws-key"
	,	"aws-secret"
	,	"aws-ami-id"
	,	"aws-instance-type"
	,	"aws-security-group"
	,	"aws-region"
	,	"aws-load-balancer"
	,	"aws-update-or-create"
	,	"code-revision"
	);
	
	$aRequiredNumberFields = array(
		"aws-min-size"
	,	"aws-max-size"
	);
	
	$aRequiredArrays = array(
		"aws-availability-zones"
	);
	
	// validate text fields
	foreach ($aRequiredTextFields as $sFieldName) {
		if (!validateTextField($sFieldName, $param_aSubmittedValues)) {
			array_push($aErrors, "Error: Field $sFieldName is required");
		}
	}	
	
	// validate number fields
	foreach ($aRequiredNumberFields as $sFieldName) {
		if (!validateNumberField($sFieldName, $param_aSubmittedValues)) {
			array_push($aErrors, "Error: Field $sFieldName is required and must be numeric");
		}
	}
	
	// validate arrays
	foreach ($aRequiredArrays as $sFieldName) {
		if (!validateCheckboxField($sFieldName, $param_aSubmittedValues)) {
			array_push($aErrors, "Error: Field $sFieldName is required");
		}
	}

	// validate tags
	for ($i = 0; $i < 10; $i++) {
		$bNameFieldValid = (isset($param_aSubmittedValues['aws-tag-name'][$i]) && strlen(trim($param_aSubmittedValues['aws-tag-name'][$i])) > 0);
		$bValueFieldValid = (isset($param_aSubmittedValues['aws-tag'][$i]) && strlen(trim($param_aSubmittedValues['aws-tag'][$i])) > 0);

		// if only one of the name/value was filled in then we throw an error
		if (	(!$bNameFieldValid && $bValueFieldValid)
		||	($bNameFieldValid && !$bValueFieldValid)) {
			$j = $i+1; 
			array_push($aErrors, "Error: Fill in both name and value for tag field #$j");
		}

		// if neither is filled in then we unset it
		if (!$bNameFieldValid && !$bValueFieldValid) {
			unset($param_aSubmittedValues['aws-tag-name'][$i]);
			unset($param_aSubmittedValues['aws-tag'][$i]);
		}
	}
	
	// return true if valid, the errors otherwise
	return (count($aErrors) > 0) ? $aErrors : true;
}

// checks a text field has been filled in
function validateTextField($param_sFieldName, $param_aSubmittedValues) {
	return (isset($param_aSubmittedValues[$param_sFieldName]) && strlen(trim($param_aSubmittedValues[$param_sFieldName])) > 0);
}

// checks a number field is filled in and numeric
function validateNumberField($param_sFieldName, $param_aSubmittedValues) {
	return (isset($param_aSubmittedValues[$param_sFieldName]) && is_numeric($param_aSubmittedValues[$param_sFieldName]));
}

// checks at least one of the checkboxes has been ticked
function validateCheckboxField($param_sFieldName, $param_aSubmittedValues) {
	return (isset($param_aSubmittedValues[$param_sFieldName]) && count($param_aSubmittedValues[$param_sFieldName]) > 0);
}





function addToOutputScript($param_sContent) {
	$oBashScript = Class_BashScript::GetInstance();
	$oBashScript->addLine($param_sContent);
}

function readOutputScript() {
	$file_path = WRITE_DIR . SCRIPT_FILENAME;
    	// open the file for reading
    	$file_handle = @fopen($file_path, 'r');

	// no file, no content
	if (!$file_handle) {
		return 'Script could not be found.';
	}
	
	// read in the existing contents of the file
	$file_contents = fread($file_handle, filesize($file_path));
	// close the read file
	fclose($file_handle);

	// now use Geshi to get some syntax highlighting!
	$oGeshi = new GeSHi($file_contents, 'bash');

	return $oGeshi->parse_code();
}





// deletes existing launch config and creates a new one with the same name
function createLaunchConfig($param_sConfigName, $param_sAmiId, $param_sInstanceType, $param_sSecurityGroupName) {
	// delete the existing launch config and create a new one
	addToOutputScript(array(
		'as-delete-launch-config ' . $param_sConfigName . ' -f' // delete the launch config if it already exists (-f forces delete without prompt)
	,	'as-create-launch-config ' . $param_sConfigName
	.	' --image-id ' . $param_sAmiId
	.	' --instance-type ' . $param_sInstanceType
	.	' --group "' . $param_sSecurityGroupName . '"'
	));
}


// create the auto scaling group
function autoScalingGroupConfig($param_sAutoscaleGroupName, $param_sLaunchConfigName, $param_sAvailabilityZones, $param_iMinSize, $param_iMaxSize, $param_sLoadBalancer = null, $param_aTagNames = array(), $param_aTagValues = array(), $param_sConfigType = "update") {

	// update/create/delete the auto scaling group
	if ($param_sConfigType == "update") {
		addToOutputScript(array(
			'as-update-auto-scaling-group ' . $param_sAutoscaleGroupName
		.	' --availability-zones ' . $param_sAvailabilityZones
		.	' --launch-configuration ' . $param_sLaunchConfigName
		.	' --min-size ' . $param_iMinSize
		.	' --max-size ' . $param_iMaxSize
		/*.	' --load-balancers ' . $param_sLoadBalancer // Load balancer is not part of an update */
		));
	} elseif ($param_sConfigType == "create") {
		// build up the tag string from the submitted values
		$sTagString = buildTagASConfigString($param_aTagNames, $param_aTagValues);

		addToOutputScript(array(
			'as-create-auto-scaling-group ' . $param_sAutoscaleGroupName
		.	' --availability-zones ' . $param_sAvailabilityZones
		.	' --launch-configuration ' . $param_sLaunchConfigName
		.	' --min-size ' . $param_iMinSize
		.	' --max-size ' . $param_iMaxSize
		.	' --load-balancers ' . $param_sLoadBalancer
		.	$sTagString
		));	
	} else {
		
	}
}

// builds the tag part of the auto scaling config
function buildTagASConfigString($param_aTagNames, $param_aTagValues) {
	// start with an empty string..
	$sString = '';

	// cycle through the tag names
	foreach($param_aTagNames as $i => $sTagName) {
		// if we didn't find a corresponding value then we break
		if (!isset($param_aTagValues[$i])) {
			break;
		}

		$sString .= ' --tag "k=' . str_replace('"', '\'', str_replace(' ', '-', $sTagName)) . ', v=' . str_replace('"', '\'', str_replace(' ', '-', $param_aTagValues[$i])) . ', p=true"';
	}

	return $sString;
}

// create an auto scaling policy - scale up AND scale down
function createAutoScalingPolicy($param_sAutoscaleGroupName, $param_sRegionName) {
	addToOutputScript(array(
		'SCALEUP=$(as-put-scaling-policy ScaleUp'
	.	' --auto-scaling-group ' . $param_sAutoscaleGroupName
	.	' --adjustment 1'
	.	' --type ChangeInCapacity '
	.	' --cooldown 300)'
	
//	,	'echo $SCALEUP'
	
	,	'SCALEDOWN=$(as-put-scaling-policy ScaleDown'
	.	' --auto-scaling-group ' . $param_sAutoscaleGroupName
	.	' --adjustment=-1'
	.	' --type ChangeInCapacity '
	.	' --cooldown 600)'

//	,	'echo $SCALEDOWN'

	// create cloudwatch alarms - spike
	,	'mon-put-metric-alarm --alarm-name ' . $param_sAutoscaleGroupName . '-spike'
	.	' --alarm-description "Scale up when ' . $param_sAutoscaleGroupName . ' CPU is above 65% for 2 minutes"'
	.	' --metric-name CPUUtilization'
	.	' --namespace AWS/EC2'
	.	' --statistic Average'
	.	' --period 60'
	.	' --threshold 65'
	.	' --comparison-operator GreaterThanThreshold'
	.	' --dimensions AutoScalingGroupName=' . $param_sAutoscaleGroupName
	.	' --evaluation-periods 2'
	.	' --unit Percent'
	.	' --alarm-actions $SCALEUP,arn:aws:sns:' . $param_sRegionName . ':' . AWS_ACCOUNT_ID . ':' . AWS_NOTIFICATION_GROUP_NAME
	.	' --ok-actions $SCALEDOWN,arn:aws:sns:' . $param_sRegionName . ':' . AWS_ACCOUNT_ID . ':' . AWS_NOTIFICATION_GROUP_NAME
	.	' --region ' . $param_sRegionName


	// create cloudwatch alarms - constant high usage
	,	'mon-put-metric-alarm --alarm-name ' . $param_sAutoscaleGroupName . '-CHU'
	.	' --alarm-description "Scale up when ' . $param_sAutoscaleGroupName . ' CPU is above 40% for 5 minutes"'
	.	' --metric-name CPUUtilization'
	.	' --namespace AWS/EC2'
	.	' --statistic Average'
	.	' --period 60'
	.	' --threshold 40'
	.	' --comparison-operator GreaterThanThreshold'
	.	' --dimensions AutoScalingGroupName=' . $param_sAutoscaleGroupName
	.	' --evaluation-periods 5'
	.	' --unit Percent'
	.	' --alarm-actions $SCALEUP,arn:aws:sns:' . $param_sRegionName . ':' . AWS_ACCOUNT_ID . ':' . AWS_NOTIFICATION_GROUP_NAME
	.	' --ok-actions $SCALEDOWN,arn:aws:sns:' . $param_sRegionName . ':' . AWS_ACCOUNT_ID . ':' . AWS_NOTIFICATION_GROUP_NAME
	.	' --region ' . $param_sRegionName
	));
}
	
?>
