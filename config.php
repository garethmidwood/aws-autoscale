<?php

// define the keys for reading from EC2
define('AWS_IAM_EC2_READ_KEY',			'-ive got the key-');
define('AWS_IAM_EC2_READ_SECRET',		'-youve got the secret-');

// your AWS account Unique ID
define('AWS_ACCOUNT_ID',				123);

// The name of the group to be notified when auto scaling is triggered
define('AWS_NOTIFICATION_GROUP_NAME',	'notification-group-name');

// paths to home directories
define('AWS_AUTO_SCALING_HOME',			'/var/aws/AutoScaling-1.0.61.1');
define('EC2_HOME',						'/var/aws/ec2-api-tools-1.6.5.1');
define('AWS_CLOUDWATCH_HOME',			'/var/aws/CloudWatch-1.0.13.4');
define('JAVA_HOME',						'/usr/lib/jvm/java-7-oracle');

?>
