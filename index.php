<?php
/*
 *	Auto scaling Tool
 *
 *	Note:
 *		A list of available commands can be found here
 *		http://docs.amazonwebservices.com/AutoScaling/latest/DeveloperGuide/astools.html#verify-install
 *		
 *
 *	@Author:	Gareth Midwood.
 *	@Date:		06/11/12
 */

 // adding a needless comment
include('header.php');


// check we have write permissions
$bHasWritePermissions = checkWritePermissions();

// get the form values if submitted
$aFormValues = isset($_POST) ? $_POST : array();
$bFormSubmitted = (count($aFormValues) > 0);

// if the form hasn't been submitted then we exit here
if (!$bFormSubmitted) {
	// include the tpl
	include('tpl' . DS . 'template.html.php');	
	// exit
	exit();
}

// check that all form fields have been filled in
$bFormValid = validateFormSubmission($aFormValues);

// if we got an array back then they are errors
if (is_array($bFormValid)) {
	// include the tpl
	include('tpl' . DS . 'template.html.php');	
	// exit
	exit();	
}

/*
 *	FORM HAS BEEN SUBMITTED
 */
// write the credentials file and set the path
writeCredentialsFile(CREDENTIALS_FILENAME, $aFormValues["aws-key"], $aFormValues["aws-secret"]);
// set the AWS url to select the region
setAWSAutoScalingURL($aFormValues["aws-region"]);
// set the environment variables
setEnvironmentVariables();
// the name of the configuration we're setting up
define('AWS_LAUNCH_CONFIG_NAME',	'web_launch_config_r' . $aFormValues["code-revision"]);
// the name of the auto scaling group we're setting up
define('AWS_AUTOSCALE_GROUP_NAME',	'web_autoscaling_group_r' . $aFormValues["code-revision"]);


/*
 * 
 *	WRITE THE OUTPUT SCRIPT 
 * 
 */

// if we chose delete then we delete the group
if ($aFormValues["aws-update-or-create"] == "delete") {
	// build the command
	addToOutputScript(array(
			'as-delete-auto-scaling-group ' . AWS_AUTOSCALE_GROUP_NAME . ' -f --force-delete'
		,	'echo ---------------------------------'
		,	'as-delete-launch-config ' . AWS_LAUNCH_CONFIG_NAME . ' -f'
		,	'echo ---------------------------------'
		,	'mon-delete-alarms ' . AWS_AUTOSCALE_GROUP_NAME . '-CHU ' . AWS_AUTOSCALE_GROUP_NAME . '-spike -f'
		,	'echo ---------------------------------'
		,	'as-describe-launch-configs'
		,	'rm ' . WRITE_DIR . CREDENTIALS_FILENAME // allow them to remove their credentials file
	));

	// include the tpl
	include('tpl' . DS . 'template.html.php');
	exit;
} elseif ($aFormValues["aws-update-or-create"] == "list") {
	addToOutputScript(array(
			'as-describe-auto-scaling-groups'
		,	'echo ---------------------------------'
		,	'as-describe-launch-configs'
		,	'rm ' . WRITE_DIR . CREDENTIALS_FILENAME // allow them to remove their credentials file
	));

	// include the tpl
	include('tpl' . DS . 'template.html.php');
	exit;
}


// create the launch config
createLaunchConfig(AWS_LAUNCH_CONFIG_NAME, $aFormValues["aws-ami-id"], $aFormValues["aws-instance-type"], $aFormValues["aws-security-group"]);	

// create the auto scaling group
autoScalingGroupConfig(AWS_AUTOSCALE_GROUP_NAME, AWS_LAUNCH_CONFIG_NAME, implode(',', $aFormValues["aws-availability-zones"])
					, $aFormValues["aws-min-size"], $aFormValues["aws-max-size"], $aFormValues["aws-load-balancer"], $aFormValues['aws-tag-name']
					, $aFormValues["aws-tag"], $aFormValues["aws-update-or-create"]);

// create the auto scaling policy
createAutoScalingPolicy(AWS_AUTOSCALE_GROUP_NAME, $aFormValues["aws-region"]);

// spit out a few details after running
addToOutputScript(array(
	'echo ---------------------------------'
,	'echo ---------------------------------'
,	'echo "Describing launch configs:"'
,	'as-describe-launch-configs'
,	'echo ---------------------------------'
,	'echo "Describing auto scaling groups:"'
,	'as-describe-auto-scaling-groups'
,	'rm ' . WRITE_DIR . CREDENTIALS_FILENAME // allow them to remove their credentials file
));

// include the tpl
include('tpl' . DS . 'template.html.php');

// clear out the write directory
//deleteFilesInDirectory(WRITE_DIR);
?>
