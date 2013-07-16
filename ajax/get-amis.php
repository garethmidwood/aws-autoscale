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
	use Aws\Ec2\Command\DescribeImages;
	use Aws\Common\Enum\Region;

	// Set up the global AWS factories
	$oAWS = Aws::factory(array('key' => AWS_IAM_EC2_READ_KEY,'secret' => AWS_IAM_EC2_READ_SECRET,'region' => $sRegion));

	// Query AWS for AMIs
	$aAMIs = $oAWS->get('ec2')->describeImages(array("Owners" => array("self")))->toArray();

	$aResponseAMIs = array();

	foreach($aAMIs['Images'] as $aAMI) {
		// check if this is selected
		$bSelected = ($sSelected == $aAMI['ImageId']) ? "selected" : "";
		// set up the item array
		$aAMI = array("name" => $aAMI['ImageId'], "description" => $aAMI['Name']);
		if ($bSelected) {
			$aAMI["selected"] = $bSelected;
		}

		// add it to the final array
		array_push($aResponseAMIs, $aAMI);
	}

	foreach ($aResponseAMIs as $key => $row) {
	    $aName[$key]  = $row['name'];
	    $aDescription[$key] = $row['description'];
	    $aSelected[$key] = isset($row['selected']) ? $row['selected'] : null;
	}

	// Sort the data with volume descending, edition ascending
	// Add $data as the last parameter, to sort by the common key
	array_multisort($aDescription, SORT_ASC, $aName, SORT_ASC, $aSelected, SORT_ASC, $aResponseAMIs);

	echo json_encode($aResponseAMIs);

?>
