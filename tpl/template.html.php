<!DOCTYPE html>
<html>
	<head>
		<title>Auto scale configuration</title>
		<link rel="stylesheet" type="text/css" href="/css/layout.css">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<script src="/js/general.js"></script>
	</head>
	<body>
		<header>
			<h1>Auto scaling configuration</h1>
		</header>
		<div id="body">
			<p>Fill in the form below to set up the auto scaling</p>

			<p>
				Files will be written to this sites /bin directory.
				<?php if (!$bHasWritePermissions) { ?>
					<span class="error">Error: Test file could not be written to /bin directory. Please check the permissions and try again</span>
				<?php } else { ?>
					<span class="positive">Passed: Test file written to /bin directory</span>
				<?php } ?>
			</p>
			
			<p>
				<strong>NOTE:</strong>
				After updating the auto scaling group, you MUST re-set the Cloudwatch alarms
			</p>
			
			<?php
				if (isset($bFormValid) && is_array($bFormValid)) {
					?>
						<h2>Error</h2>
						<p>
					<?php
					
					foreach($bFormValid as $sErrorMsg) {
						?>
							<span class="error">
								<?php echo $sErrorMsg; ?>
							</span>
						<?php
					}
					
					?>
						</p>
					<?php
				}
			?>
						
			<form method="POST" action="">
				<fieldset>
					
					<ul id="autoscaling-form">
						<li class="update create delete list">
							<h1>AWS Access</h1>
						</li>
						<li class="update create delete list">
							<label for="aws-key">AWS Key</label>
							<input type="text" name="aws-key" id="aws-key" value="<?php if (isset($aFormValues["aws-key"])) { echo $aFormValues["aws-key"]; } ?>" />
						</li>
						<li class="update create delete list">
							<label for="aws-secret">AWS Secret</label>
							<input type="password" name="aws-secret" id="aws-secret" value="<?php if (isset($aFormValues["aws-secret"])) { echo $aFormValues["aws-secret"]; } ?>" />
						</li>

						<li class="update create delete list">
							<hr/>
							<h1>Code Revision</h1>
						</li>
						
						<li class="update create delete list">
							<label for="code-revision">Code Revision Number<span>The auto scale policies will have this revision number tagged on the end</span></label>
							<input type="text" name="code-revision" id="code-revision" value="<?php if (isset($aFormValues['code-revision'])) { echo $aFormValues['code-revision']; } ?>" />
						</li>






						<li class="update create delete list">
							<hr/>
							<h1>Autoscaling</h1>
						</li>
						
						<li class="update create delete list">
							<label for="aws-update-or-create">Update or Create new Autoscaling Group</label>
							<div id="aws-update-or-create" class="checkboxes">
								<label for="asg-create"><input type="radio" name="aws-update-or-create" id="asg-create" value="create" <?php if (isset($aFormValues["aws-update-or-create"]) && $aFormValues["aws-update-or-create"] == "create") { echo 'checked="checked"'; } ?>/> Create</label>
								<label for="asg-update"><input type="radio" name="aws-update-or-create" id="asg-update" value="update" <?php if (!isset($aFormValues["aws-update-or-create"]) || $aFormValues["aws-update-or-create"] == "update") { echo 'checked="checked"'; } ?>/> Update</label>
								<label for="asg-delete"><input type="radio" name="aws-update-or-create" id="asg-delete" value="delete" <?php if (isset($aFormValues["aws-update-or-create"]) && $aFormValues["aws-update-or-create"] == "delete") { echo 'checked="checked"'; } ?>/> Delete</label>
								<label for="asg-list"><input type="radio" name="aws-update-or-create" id="asg-list" value="list" <?php if (isset($aFormValues["aws-update-or-create"]) && $aFormValues["aws-update-or-create"] == "list") { echo 'checked="checked"'; } ?>/> List Autoscaling Details</label>							</div>
						</li>
						
						<li class="update create delete list">
							<label for="aws-region">Region</label>
							<select name="aws-region" id="aws-region">
								<option value=""> -- Choose a Region -- </option>
								<option value="eu-west-1"<?php if (isset($aFormValues["aws-region"]) && $aFormValues["aws-region"] == 'eu-west-1') { echo 'selected="selected"'; } ?>>EU West 1 (Dublin)</option>
								<option value="us-east-1"<?php if (isset($aFormValues["aws-region"]) && $aFormValues["aws-region"] == 'us-east-1') { echo 'selected="selected"'; } ?>>US East 1 (N. Virginia)</option>
								<option value="ap-southeast-1"<?php if (isset($aFormValues["aws-region"]) && $aFormValues["aws-region"] == 'ap-southeast-1') { echo 'selected="selected"'; } ?>>AP Southeast 1 (Singapore)</option>
							</select>							
						</li>
						
						<li class="update create">
							<label for="availability-zones">Availability Zones</label>
							<div id="availability-zones" class="checkboxes">
								<label for="eu-west-1a"><input type="checkbox" name="aws-availability-zones[]" id="eu-west-1a" value="eu-west-1a" <?php if (isset($aFormValues["aws-availability-zones"]) && (in_array("eu-west-1a", $aFormValues["aws-availability-zones"]) != false)) { echo 'checked="checked"'; } ?>/> eu-west-1a</label>
								<label for="eu-west-1b"><input type="checkbox" name="aws-availability-zones[]" id="eu-west-1b" value="eu-west-1b" <?php if (isset($aFormValues["aws-availability-zones"]) && (in_array("eu-west-1b", $aFormValues["aws-availability-zones"]) != false)) { echo 'checked="checked"'; } ?>/> eu-west-1b</label>
								<label for="eu-west-1c"><input type="checkbox" name="aws-availability-zones[]" id="eu-west-1c" value="eu-west-1c" <?php if (isset($aFormValues["aws-availability-zones"]) && (in_array("eu-west-1c", $aFormValues["aws-availability-zones"]) != false)) { echo 'checked="checked"'; } ?>/> eu-west-1c</label>

								<label for="us-east-1a"><input type="checkbox" name="aws-availability-zones[]" id="us-east-1a" value="us-east-1a" <?php if (isset($aFormValues["aws-availability-zones"]) && (in_array("us-east-1a", $aFormValues["aws-availability-zones"]) != false)) { echo 'checked="checked"'; } ?>/> us-east-1a</label>
								<label for="us-east-1b"><input type="checkbox" name="aws-availability-zones[]" id="us-east-1b" value="us-east-1b" <?php if (isset($aFormValues["aws-availability-zones"]) && (in_array("us-east-1b", $aFormValues["aws-availability-zones"]) != false)) { echo 'checked="checked"'; } ?>/> us-east-1b</label>
								<label for="us-east-1c"><input type="checkbox" name="aws-availability-zones[]" id="us-east-1c" value="us-east-1c" <?php if (isset($aFormValues["aws-availability-zones"]) && (in_array("us-east-1c", $aFormValues["aws-availability-zones"]) != false)) { echo 'checked="checked"'; } ?>/> us-east-1c</label>
								<label for="us-east-1d"><input type="checkbox" name="aws-availability-zones[]" id="us-east-1d" value="us-east-1d" <?php if (isset($aFormValues["aws-availability-zones"]) && (in_array("us-east-1d", $aFormValues["aws-availability-zones"]) != false)) { echo 'checked="checked"'; } ?>/> us-east-1d</label>

								<label for="ap-southeast-1a"><input type="checkbox" name="aws-availability-zones[]" id="ap-southeast-1a" value="ap-southeast-1a" <?php if (isset($aFormValues["aws-availability-zones"]) && (in_array("ap-southeast-1a", $aFormValues["aws-availability-zones"]) != false)) { echo 'checked="checked"'; } ?>/> ap-southeast-1a</label>
								<label for="ap-southeast-1b"><input type="checkbox" name="aws-availability-zones[]" id="ap-southeast-1b" value="ap-southeast-1b" <?php if (isset($aFormValues["aws-availability-zones"]) && (in_array("ap-southeast-1b", $aFormValues["aws-availability-zones"]) != false)) { echo 'checked="checked"'; } ?>/> ap-southeast-1b</label>
							</div>
						</li>
						<li class="update create">
							<label for="aws-min-size">Min Servers <span>There will always be at least this many servers active</span></label>
							<input type="number" min="1" max="10" name="aws-min-size" id="aws-min-size" value="<?php if (isset($aFormValues["aws-min-size"])) { echo $aFormValues["aws-min-size"]; } ?>" />
						</li>
						<li class="update create">
							<label for="aws-max-size">Max Servers <span>There will never be more than this many servers active</span></label>
							<input type="number" min="2" max="30" name="aws-max-size" id="aws-max-size" value="<?php if (isset($aFormValues["aws-max-size"])) { echo $aFormValues["aws-max-size"]; } ?>" />
						</li>
						<li id="aws-load-balancer-row" class="update create">
							<label for="aws-load-balancer">Load Balancer</label>
							<input type="hidden" id="aws-load-balancer-selected" value="<?php if (isset($aFormValues['aws-load-balancer'])) { echo $aFormValues['aws-load-balancer']; } ?>" />
							<select name="aws-load-balancer" id="aws-load-balancer">
							</select>							
						</li>







						
						<li class="update create">
							<hr/>
							<h1>Launch Configuration</h1>
						</li>
						
						<li id="aws-ami-row" class="update create">
							<label for="aws-ami-id">AMI Id</label>
							<input type="hidden" id="aws-ami-selected" value="<?php if (isset($aFormValues['aws-ami-id'])) { echo $aFormValues['aws-ami-id']; } ?>" />
							<select name="aws-ami-id" id="aws-ami-id">
							</select>	
						</li>
						
						<li class="update create">
							<label for="aws-instance-type">Instance Types <span>This is not the full list of instance types. <a href="http://aws.amazon.com/ec2/instance-types/" target="_blank">Click here for a full list</a></span></label>
							<select name="aws-instance-type" id="aws-instance-type">
								<optgroup label="Micro Instances">
									<option value="t1.micro" <?php if (isset($aFormValues["aws-instance-type"]) && $aFormValues["aws-instance-type"] == "t1.micro") { echo 'selected="selected"'; } ?>>Micro Instance</option>
								</optgroup>
								<optgroup label="First Generation">
									<option value="m1.small" <?php if (isset($aFormValues["aws-instance-type"]) && $aFormValues["aws-instance-type"] == "m1.small") { echo 'selected="selected"'; } ?>>M1 Small Instance</option>
									<option value="m1.medium" <?php if (isset($aFormValues["aws-instance-type"]) && $aFormValues["aws-instance-type"] == "m1.medium") { echo 'selected="selected"'; } ?>>M1 Medium Instance</option>
									<option value="m1.large" <?php if (isset($aFormValues["aws-instance-type"]) && $aFormValues["aws-instance-type"] == "m1.large") { echo 'selected="selected"'; } ?>>M1 Large Instance</option>
									<option value="m1.xlarge" <?php if (isset($aFormValues["aws-instance-type"]) && $aFormValues["aws-instance-type"] == "m1.xlarge") { echo 'selected="selected"'; } ?>>M1 Extra Large Instance</option>
								</optgroup>
								<optgroup label="Second Generation">
									<option value="m3.xlarge" <?php if (isset($aFormValues["aws-instance-type"]) && $aFormValues["aws-instance-type"] == "m3.xlarge") { echo 'selected="selected"'; } ?>>M3 Extra Large Instance</option>
									<option value="m3.2xlarge" <?php if (isset($aFormValues["aws-instance-type"]) && $aFormValues["aws-instance-type"] == "m3.2xlarge") { echo 'selected="selected"'; } ?>>M3 Double Extra Large Instance</option>
								</optgroup>
								<optgroup label="High-CPU Instances">
									<option value="c1.medium" <?php if (isset($aFormValues["aws-instance-type"]) && $aFormValues["aws-instance-type"] == "c1.medium") { echo 'selected="selected"'; } ?>>High-CPU Medium Instance</option>
									<option value="c1.xlarge" <?php if (isset($aFormValues["aws-instance-type"]) && $aFormValues["aws-instance-type"] == "c1.xlarge") { echo 'selected="selected"'; } ?>>High-CPU Extra Large Instance</option>
								</optgroup>
							</select>
						</li>
						
						<li id="aws-security-group-row" class="update create">
							<label for="aws-security-group">Security Group</label>
							<input type="hidden" id="aws-security-group-selected" value="<?php if (isset($aFormValues['aws-security-group'])) { echo $aFormValues['aws-security-group']; } ?>" />
							<select name="aws-security-group" id="aws-security-group">
							</select>
						</li>
						
						
						
						<li class="create">
							<hr/>
							<h1>Instance Tags</h1>
						</li>

						<li class="create">
							<label for="aws-tag-1-name">Tag #1 - Name<span>Note: Do not leave spaces in this! (spaces will be replaced with dashes)</span></label>
							<input type="text" name="aws-tag-name[]" id="aws-tag-1-name" readonly="readonly" value="Name" /> =
							<input name="aws-tag[]" id="aws-tag-1" value="<?php if (isset($aFormValues['aws-tag'][0])) { echo $aFormValues['aws-tag'][0]; } ?>" />							
						</li>
						<li class="create">
							<label for="aws-tag-2-name">Tag #2 - Creator Initials</label>
							<input type="text" name="aws-tag-name[]" id="aws-tag-2-name" readonly="readonly" value="Creator" /> =
							<input name="aws-tag[]" id="aws-tag-2" value="<?php if (isset($aFormValues['aws-tag'][1])) { echo $aFormValues['aws-tag'][1]; } ?>" />							
						</li>
						<li class="create">
							<label for="aws-tag-3-name">Tag #3 - Purpose</label>
							<input type="text" name="aws-tag-name[]" id="aws-tag-3-name" readonly="readonly" value="Purpose" /> =
							<input name="aws-tag[]" id="aws-tag-3" value="<?php if (isset($aFormValues['aws-tag'][2])) { echo $aFormValues['aws-tag'][2]; } ?>" />							
						</li>
						<li class="create">
							<label for="aws-tag-4-name">Tag #4</label>
							<input type="text" name="aws-tag-name[]" id="aws-tag-4-name" value="<?php if (isset($aFormValues['aws-tag-name'][3])) { echo $aFormValues['aws-tag-name'][3]; } ?>" /> =
							<input name="aws-tag[]" id="aws-tag-4" value="<?php if (isset($aFormValues['aws-tag'][3])) { echo $aFormValues['aws-tag'][3]; } ?>" />							
						</li>
						<li class="create">
							<label for="aws-tag-5-name">Tag #5</label>
							<input type="text" name="aws-tag-name[]" id="aws-tag-5-name" value="<?php if (isset($aFormValues['aws-tag-name'][4])) { echo $aFormValues['aws-tag-name'][4]; } ?>" /> =
							<input name="aws-tag[]" id="aws-tag-5" value="<?php if (isset($aFormValues['aws-tag'][4])) { echo $aFormValues['aws-tag'][4]; } ?>" />							
						</li>
						<li class="create">
							<label for="aws-tag-6-name">Tag #6</label>
							<input type="text" name="aws-tag-name[]" id="aws-tag-6-name" value="<?php if (isset($aFormValues['aws-tag-name'][5])) { echo $aFormValues['aws-tag-name'][5]; } ?>" /> =
							<input name="aws-tag[]" id="aws-tag-6" value="<?php if (isset($aFormValues['aws-tag'][5])) { echo $aFormValues['aws-tag'][5]; } ?>" />							
						</li>
						<li class="create">
							<label for="aws-tag-7-name">Tag #7</label>
							<input type="text" name="aws-tag-name[]" id="aws-tag-7-name" value="<?php if (isset($aFormValues['aws-tag-name'][6])) { echo $aFormValues['aws-tag-name'][6]; } ?>" /> =
							<input name="aws-tag[]" id="aws-tag-7" value="<?php if (isset($aFormValues['aws-tag'][6])) { echo $aFormValues['aws-tag'][6]; } ?>" />							
						</li>
						<li class="create">
							<label for="aws-tag-8-name">Tag #8</label>
							<input type="text" name="aws-tag-name[]" id="aws-tag-8-name" value="<?php if (isset($aFormValues['aws-tag-name'][7])) { echo $aFormValues['aws-tag-name'][7]; } ?>" /> =
							<input name="aws-tag[]" id="aws-tag-8" value="<?php if (isset($aFormValues['aws-tag'][7])) { echo $aFormValues['aws-tag'][7]; } ?>" />							
						</li>
						<li class="create">
							<label for="aws-tag-9-name">Tag #9</label>
							<input type="text" name="aws-tag-name[]" id="aws-tag-9-name" value="<?php if (isset($aFormValues['aws-tag-name'][8])) { echo $aFormValues['aws-tag-name'][8]; } ?>" /> =
							<input name="aws-tag[]" id="aws-tag-9" value="<?php if (isset($aFormValues['aws-tag'][8])) { echo $aFormValues['aws-tag'][8]; } ?>" />							
						</li>
						<li class="create">
							<label for="aws-tag-10-name">Tag #10</label>
							<input type="text" name="aws-tag-name[]" id="aws-tag-10-name" value="<?php if (isset($aFormValues['aws-tag-name'][9])) { echo $aFormValues['aws-tag-name'][9]; } ?>" /> =
							<input name="aws-tag[]" id="aws-tag-10" value="<?php if (isset($aFormValues['aws-tag'][9])) { echo $aFormValues['aws-tag'][9]; } ?>" />							
						</li>
				
						<li class="code update create delete list">
							<hr/>
							<h1 id="code-title">The script as it is</h1>
							<div id="code-area">
								<?php
									$sCode = readOutputScript();
									if (strlen(trim($sCode)) > 0) {
										echo $sCode . "<button id='run-script'>Run script!</button>";
									}
								?>
							</div>
						</li>			
						
						<li class="update create delete list">
							<button>Generate Script</button>
						</li>
					</ul>
					
				</fieldset>
			</form>			
		</div>
	</body>
</html>
