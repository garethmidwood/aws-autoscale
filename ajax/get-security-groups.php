<?php
	include_once('../header.php');

	// we need to know which region we're working in, or return nothing.
	if (!isset($_GET['region'])) {
		exit;
	}

	// gather variables
	$sRegion = $_GET['region'];
	$sSelected = isset($_GET['selected']) ? $_GET['selected'] : false;

	// set up AWS namespaces
	use Aws\Common\Aws;
	use Aws\Ec2\Command\DescribeSecurityGroups;
	use Aws\Common\Enum\Region;

	// Set up the global AWS factories
	$oAWS = Aws::factory(array('key' => AWS_IAM_EC2_READ_KEY,'secret' => AWS_IAM_EC2_READ_SECRET,'region' => $sRegion));

	// Query AWS for Security Groups
	$aSecurityGroups = $oAWS->get('ec2')->describeSecurityGroups(array("Owners" => array("self")))->toArray();

	$aResponseSecurityGroups = array();

	foreach($aSecurityGroups['SecurityGroups'] as $aSecurityGroup) {
		// check if this is selected
		$bSelected = ($sSelected == $aSecurityGroup['GroupName']) ? "selected" : "";
		// set up the item array
		$aSecurityGroup = array("name" => $aSecurityGroup['GroupName']);
		if ($bSelected) {
			$aSecurityGroup["selected"] = $bSelected;
		}

		// add it to the final array
		array_push($aResponseSecurityGroups, $aSecurityGroup);
	}

	foreach ($aResponseSecurityGroups as $key => $row) {
	    $aName[$key]  = $row['name'];
	    $aSelected[$key] = isset($row['selected']) ? $row['selected'] : null;
	}

	// Sort the data with volume descending, edition ascending
	// Add $data as the last parameter, to sort by the common key
	array_multisort($aName, SORT_ASC, $aSelected, SORT_ASC, $aResponseSecurityGroups);

	echo json_encode($aResponseSecurityGroups);

?>
