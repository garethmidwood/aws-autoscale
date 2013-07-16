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
	use Aws\ElasticLoadBalancing\Command\DescribeLoadBalancers;
	use Aws\Common\Enum\Region;

	// Set up the global AWS factories
	$oAWS = Aws::factory(array('key' => AWS_IAM_EC2_READ_KEY,'secret' => AWS_IAM_EC2_READ_SECRET,'region' => $sRegion));

	// Query AWS for load balancers
	$aLBs = $oAWS->get('ElasticLoadBalancing')->describeLoadBalancers(array("Owners" => array("self")))->toArray();

	$aResponseLBs = array();

	foreach($aLBs['LoadBalancerDescriptions'] as $aLoadBalancer) {
		// check if this is selected
		$bSelected = ($sSelected == $aLoadBalancer['LoadBalancerName']) ? "selected" : "";
		// set up the item array
		$aLoadBalancer = array("name" => $aLoadBalancer['LoadBalancerName']);
		if ($bSelected) {
			$aLoadBalancer["selected"] = $bSelected;
		}

		// add it to the final array
		array_push($aResponseLBs, $aLoadBalancer);
	}

	echo json_encode($aResponseLBs);

?>
