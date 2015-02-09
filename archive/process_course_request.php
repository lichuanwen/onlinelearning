<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Process Course Requests</title>
<style>

.format
{
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 14px;
	color: #000;
	text-shadow: Gray;
	line-height: 17px;
	text-align: left;
	margin: 60px;
}

</style>

</head>

<body>



<?php 

$space = " ";
$first_name = $_POST['firstName'];
$last_name = $_POST['lastName'];
$user_name = $_POST['userName'];
$email = $_POST['email'];
$school = $_POST['school'];
$status = $_POST['status'];
$course = $coursecode.$space.$course_section;
$course_code = $_POST['courseCode'];
$course_type = $_POST['courseType'];
$comments = mysql_real_escape_string($_POST['comments']);
$full_address = $street.$space.$city.$space.$province.$space.$postal_code.$space.$country;


$mailContent = "-- FACULTY INFORMATION --\n\n" 
            ."First Name: ".$first_name."\n"
			."Last Name: ".$last_name."\n"
			."User Name: ".$user_name."\n"
			."Email: ".$email."\n"
			."School/Department: ".$school."\n"
			."Status: ".$status."\n\n"
			."-- TYPE OF COURSE REQUESTED --\n\n"
			."Course Code: ".$course_code."\n\n"
			."Course Type: ".$course_type."\n\n"
			."-- COMMENTS --\n\n"
			."Additional Notes: ".$comments."\n";


$ack = "Thank you, $first_name $last_name, for submitting the Blackboard 9.1 Online Course Request Form for your DEV or SANDBOX site.  Your request will be completed within one business day. \n

Need help uploading the official Humber Bb 9.1 Template into your new DEV or SANDBOX site?  Please go to http://humber.ca/bb91help/instructors_1_1.php for help.  Please download the most recent version of the template.  We are currently using version 6.2 of the template, dated June 19th 2013. \n

Need help on other Bb 9.1 matters?  Check out our instructor Bb 9.1 help page http://humber.ca/bb91help/instructors.php and/or visit the CTL/eLearning Studios Monday to Friday – D225 (North) and D112 (Lake).  Studio hours and contact information can be found here http://www.humber.ca/centreforteachingandlearning/contact.html. 

\n

Regards, \n

Blackboard Administrators
eLearning (D225 – North Campus)
The Centre For Teaching and Learning (CTL)\r
Humber College Institute of Technology and Advanced Learning\n
http://www.humber.ca/centreforteachingandlearning/home.html";
			
$naveed = "naveed.aqeel@humber.ca";
$mark = "mark.ihnat@humber.ca";
$to = "mark.ihnat@humber.ca";
$subject = "Bb 9.1 DEV or Sandbox Request from".$space.$user_name;
$confSubject = "Blackboard 9.1 Course Request Received";
$headers = "From: mark.ihnat@humber.ca" . "\r\n" . "CC: naveed.aqeel@humber.ca";
 


	mail("naveed.aqeel@humber.ca, mark.ihnat@humber.ca", $subject, stripslashes($mailContent), "From: $email");
	mail($email, $confSubject, $ack, "From: $mark");



$dbcnx = mysql_connect("localhost", "aqeel", "fall2004");

	if (!$dbcnx) {
	  echo( "<p>Unable to connect to the " .
			"database server at this time.</p>" );
	  exit();
	}

mysql_select_db("devsites", $dbcnx);

$query = "INSERT INTO `requests` ( 
		`col_date` ,
		`first_name` ,
		`last_name` ,
		`user_name` ,
		`email` ,
		`school` ,
		`status` ,
		`course_code` ,
		`course_type` )
VALUES (
		Now(), 
		'".$first_name."',
		'".$last_name."',
		'".$user_name."',
		'".$email."',
		'".$school."',
		'".$status."',
		'".$course_code."',
		'".$course_type."')";
		
$result = mysql_query($query) or die("Error in query:".mysql_error());

mysql_close($dbcnx); 


echo '<p class="format">Thank you, <b>'.$first_name.' '.$last_name. '</b>, 

for submitting the Blackboard 9.1 Online Course Request Form for your DEV or SANDBOX course.  Your request will be completed within one business day.
<br /><br />
Need help uploading the official Humber Bb 9.1 Template into your new DEV or SANDBOX site?  Please go here <a href="http://humber.ca/bb91help/instructors_1_1.php" target="_blank">http://humber.ca/bb91help/instructors_1_1.php</a> for help.  

<br />Please download the most recent version of the template.  We are currently using version 7.1 of the template, dated July 4th 2013.
<br /><br />
Need help on other Bb 9.1 matters?  Check out our instructor Bb 9.1 help page <a href="http://humber.ca/bb91help/instructors.php" target="_blank">http://humber.ca/bb91help/instructors.php</a> and/or visit the CTL/eLearning Studios Monday to Friday – D225 (North) and D112 (Lake).  Studio hours and contact information can be found here <a href="http://www.humber.ca/centreforteachingandlearning/contact.html" target="_blank">http://www.humber.ca/centreforteachingandlearning/contact.html</a>.  

</p>'


?>





</body>
</html>