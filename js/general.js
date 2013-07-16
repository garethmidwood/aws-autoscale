$(document).ready(function() {

	// when the region changes, change the availability zones you can see
	$('#aws-region').change(function() {
		var value = this.value;
		// hide all zones
		$('#availability-zones label').hide();

		// if we have a value then show the zones in this region
		if (value) {
			$('#availability-zones label[for^=' + value + ']').each(function() {
				$(this).show();
			});
			
			// uncheck all hidden checkboxes
			$('#availability-zones label input[type=checkbox]').not(':visible').prop('checked', false);

			// get the regions load balancers
			getLoadBalancers();

			// get the regional AMI list
			getAMIs();

			// get the regional security group list
			getSecurityGroups();
		}
	});

	// call it straight away to hide incorrect regions
	$('#aws-region').change();

	// hide unnecessary fields, depending on the type of action we're doing create/update/delete
	$('input[name=aws-update-or-create]').change(function() {
		var openclass = $(this).val();

		$('#autoscaling-form li:not(.' + openclass + ')').hide();
		$('#autoscaling-form li.' + openclass).show();
	});

	// call it straight away to hide fields
	$('input[name=aws-update-or-create]:checked').change();

	// run the script when the button is pressed...
	$('#run-script').click(function() {
		runScript();
		return false;
	});

	// if any field changes then we hide the run script button so there's no confusion
	$( ":input" ).change(function() {
		$('#run-script').hide();
	});
});



// adds loading span inside selected element
function loading(selector, text) {
	var loadingtext = (text) ? text : 'loading, please wait...';
	$(selector).append("<span class='load'>" + loadingtext + "</span>");
}

// removes loading span from selected element
function loaded(selector) {
	$('span.load', selector).remove();
}

// retrieves list of load balancers for the selected region
function getLoadBalancers() {
	// get the currently selected region
	var value = $('#aws-region').val();
	
	if (value) {
		// load balancers will be loading in a second..
		loading('#aws-load-balancer-row');

		// build the ajax request URL
		var request_url = '/ajax/get-load-balancers.php?region=' + value;

		// see if we have a pre-selected load balancer to pick...
		if ($('#aws-load-balancer-selected').length > 0) {
			var request_url = request_url + '&selected=' + $('#aws-load-balancer-selected').val();
		}

		// get the load balancers
		$.ajax(request_url)
		.done(function(response) {
			// empty the load balancer selector
			$('#aws-load-balancer').empty();

			var oLoadBalancers = $.parseJSON(response);
			var length = oLoadBalancers.length;
			// cycle through load balancers, add them to the page
			for (var i = 0; i < length; i++) {
				var loadbalancer = oLoadBalancers[i];

				var selectedValue = (loadbalancer['selected'] == "selected") ? " selected='" + loadbalancer['selected'] + "'" : '';

				$('#aws-load-balancer').append("<option value='" + loadbalancer['name'] + "'" + selectedValue + ">" + loadbalancer['name'] + "</option>");
			}

			loaded('#aws-load-balancer-row');
		});
	}
}


// retrieves list of AMIs for the selected region
function getAMIs() {
	// get the currently selected region
	var value = $('#aws-region').val();
	
	if (value) {
		// load balancers will be loading in a second..
		loading('#aws-ami-row');

		// build the ajax request URL
		var request_url = '/ajax/get-amis.php?region=' + value;

		// see if we have a pre-selected ami to pick...
		if ($('#aws-ami-selected').length > 0) {
			var request_url = request_url + '&selected=' + $('#aws-ami-selected').val();
		}

		// get the load balancers
		$.ajax(request_url)
		.done(function(response) {
			// empty the load balancer selector
			$('#aws-ami-id').empty();

			var oAMIs = $.parseJSON(response);
			var length = oAMIs.length;
			// cycle through amis, add them to the page
			for (var i = 0; i < length; i++) {
				var ami = oAMIs[i];

				var selectedValue = (ami['selected'] == "selected") ? " selected='" + ami['selected'] + "'" : '';

				$('#aws-ami-id').append("<option value='" + ami['name'] + "'" + selectedValue + ">" + ami['description'] + " (" + ami['name'] + ")</option>");
			}

			loaded('#aws-ami-row');
		});
	}
}


// retrieves list of security groups for the selected region
function getSecurityGroups() {
	// get the currently selected region
	var value = $('#aws-region').val();
	
	if (value) {
		// load balancers will be loading in a second..
		loading('#aws-security-group-row');

		// build the ajax request URL
		var request_url = '/ajax/get-security-groups.php?region=' + value;

		// see if we have a pre-selected seucrity group to pick...
		if ($('#aws-security-group-selected').length > 0) {
			var request_url = request_url + '&selected=' + $('#aws-security-group-selected').val();
		}

		// get the load balancers
		$.ajax(request_url)
		.done(function(response) {
			// empty the load balancer selector
			$('#aws-security-group').empty();

			var oSecurityGroups = $.parseJSON(response);
			var length = oSecurityGroups.length;
			// cycle through security groups, add them to the page
			for (var i = 0; i < length; i++) {
				var securitygroup = oSecurityGroups[i];

				var selectedValue = (securitygroup['selected'] == "selected") ? " selected='" + securitygroup['selected'] + "'" : '';

				$('#aws-security-group').append("<option value='" + securitygroup['name'] + "'" + selectedValue + ">" + securitygroup['name'] + "</option>");
			}

			loaded('#aws-security-group-row');
		});
	}
}


// run the generated script
function runScript() {

	// load balancers will be loading in a second..
	loading('#code-area', 'Running script, please wait...');

	// run the script via ajax..
	$.ajax('/ajax/run-script.php')
	.done(function(response) {
		
		$('#code-area').html(response);
		$('#code-title').html('Script results:');
	});
}

