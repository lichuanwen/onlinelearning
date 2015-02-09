<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Processor Virtual Proctor Requests</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<style>

.format
{
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 14px;
	color: #000;
	text-shadow: Gray;
	line-height: 17px;
	text-align: center;
	margin: 60px;
}

</style>

</head>

<body>

<?php 

$space = " ";
$first_name = $_POST['firstName'];
$last_name = $_POST['lastName'];
$student_number = $_POST['studentNum'];
$email = $_POST['email'];
$coursecode = $_POST['courseCode'];
$course_section = $_POST['courseSection'];
$course = $coursecode.$space.$course_section;
$proctoring_option = $_POST['proctoring_option'];
$campus = $_POST['campus'];
$comments = mysql_real_escape_string($_POST['comments']);
$full_address = $street.$space.$city.$space.$province.$space.$postal_code.$space.$country;

$mailContent = "-------- STUDENT INFORMATION --------\n\n" 
            ."First Name: ".$first_name."\n"
			."Last Name: ".$last_name."\n"
			."Student Number: ".$student_number."\n"
			."Email: ".$email."\n"
			."Course Code: ".$coursecode."\n"
			."Section: ".$course_section."\n\n"
			."-------- VIRTUAL PROCTOR --------\n\n"
			."Campus: ".$campus."\n\n"
			."--------COMMENTS--------\n\n"
			."Additional Notes: ".stripcslashes($comments)."\n";

$ack = "Thank you, $first_name $last_name, for submitting the Test Centre Request Form for Online Students.  You will receive an email confirmation from one of the OLC Staff members once we receive the approval for the request.

Please call the OLC at 416-675-5049 or 1-877-215-6117 if you don't receive an email confirmation a week before your final exam date. \n
Regards, \n\nOpen Learning Centre\nHumber College Institute of Technology and Advanced Learning\nPhone: 416-675-5049\nToll Free: 1-877-215-6117";
			
$toLab = "olc@humber.ca";
$Michelle = "michelle.joseph@humber.ca";
$Noreen = "nooreen.hussain@humber.ca";
$Naveed = "naveed.aqeel@humber.ca";
$toChristine = "christine.augustine@humber.ca";
$toSandy = "sandy.shivratan@humber.ca";
$toJey = "jey.sadad@humber.ca";
$toFalisha = "falisha.rowe@humber.ca";
$Keri = "keri.ranjanakumar@humber.ca";
$subject = "Virtual Proctor Request Form: ".$space.$coursecode.$space.$course_section;
$confSubject = "Proctor Request for ".$space.$coursecode.$space.$course_section.$space." Received";


if($coursecode == 'ACCT 108'){
	mail($Naveed, $subject, $mailContent, "From: $email");
	mail($email, $confSubject, $ack, "From: $Naveed");
}



elseif($coursecode == 'ACCT 111'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'ACCT 202'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'ACCT 211'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'ACCT 221'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'ACCT 331'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'ACCT 341'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'ACCT 351'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'ACCT 441'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'ACCT 461'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'ACCT 531'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'ACCT 551'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'ACCT 561'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'ACCT 800'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'ACCT 901'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'ATEC 500'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'BACC 100'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BACC 200'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BACC 201'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'BACC 300'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BCTA 002'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BCTA 102'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'BCTA 200'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'BCTA 302'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BCTA 304'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BCTA 308'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'BCTA 402'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BECN 100'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BECN 200'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BECN 301'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BFIN 420'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BFIN 500'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'BICC 102'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BICC 200'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BICC 201'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BICC 302'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BICC 402'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BICC 404'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BIOS 211'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'BISM 120'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BISM 324'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BISM 327'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BISM 450'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BISM 461'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'BISM 462'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}
elseif($coursecode == 'BISM 463'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'BISM 464'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'BLAW 100'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BMAT 220'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'BMGT 100'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BMGT 201'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BMGT 202'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BMGT 204'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BMGT 206'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BMGT 210'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BMGT 212'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BMGT 300'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BMGT 305'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BMGT 310'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BMGT 328'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'BMGT 400'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'BMGT 405'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BMGT 424'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'BPGM 500'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'BPGM 502'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'BPGM 503'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'BPGM 504'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'BPGM 506'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'BPGM 507'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'BPGM 508'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}
elseif($coursecode == 'BRTV 330'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'BRTV 430'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'BSTA 300'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'CCL. 119'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CCL. 206'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CCL. 207'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CCL. 208'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CCL. 209'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CCL. 213'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CCL. 214'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CCL. 215'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CIVL 801'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'CIVL 802'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'CIVL 803'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'CIVL 804'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'CIVL 805'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'CIVL 806'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'CIVL 807'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'CIVL 808'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'CLIN 213'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CLIN 500'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CLIN 502'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CLIN 509'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CLIN 513'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CMC. 701'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CMC. 702'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CMC. 703'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CNST 701'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CNST 703'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CNST 704'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CNST 709'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CNST 718'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CNST 719'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CNST 729'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CNST 755'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CNST 759'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CNST 801'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'COM. 001'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'COMM 200'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'COMM 213'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'COMM 300'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'COMM 303'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'COMM 313'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'CORN 101'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CORN 201'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CORN 401'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CPAN 110'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CPAN 140'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CPAN 210'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CPAN 220'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CPAN 222'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}
elseif($coursecode == 'CPAN 223'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CPAN 240'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CPAN 260'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CPAN 330'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CPAN 702'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CRWR 222'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CRWR 224'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'CRWR 238'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'DEV. 101'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'DEV. 102'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'DEV. 103'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'DEV. 104'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'DEV. 105'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'DMAS 001'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'DMAS 002'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'DMAS 003'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'DMAS 004'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'DMAS 005'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'DMAS 006'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'DMAS 011'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'DMAS 020'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'DMAS 022'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'DMAS 030'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'DMAS 031'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'DMAS 032'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'DMAS 040'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'DMAS 041'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'DMAS 042'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'DMAS 044'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'DSW. 102'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'DSW. 108'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'DSW. 203'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'DSW. 204'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'DSW. 215'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'DSW. 405'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'DSW. 805'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'DSWA 102'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'DSWA 108'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'DSWA 203'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'ECE. 012'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'ECE. 014'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'ECON 004'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'ECON 006'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'EMGT 501'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'EMGT 504'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'EMGY 101'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'EMGY 203'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'EMGY 401'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'EMGY 501'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'ENGL 078'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'ESL. 300'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}
elseif($coursecode == 'FREN 191'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}
elseif($coursecode == 'FREN 192'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}
elseif($coursecode == 'FREN 194'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}
elseif($coursecode == 'FREN 195'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}
elseif($coursecode == 'FREN 301'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'FSW. 101'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'FSW. 102'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'FSW. 106'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'FSW. 107'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'FSW. 108'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'FSW. 110'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'FSW. 112'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'FSW. 201'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}
elseif($coursecode == 'FSW. 202'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'FSW. 203'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'FSW. 204'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'FSW. 205'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'FSW. 206'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'FUND 529'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'GEOG 012'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'GRND 310'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'GRND 311'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'GRND 312'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'GRND 313'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'GRND 314'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'GRND 315'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'GRND 316'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'HMIN 100'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'HMIN 110'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'HMIN 111'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'HMIN 112'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'HMIN 113'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'HMIN 114'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'HMIN 201'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'HOTL 550'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'HOTL 551'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'HOTL 552'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'HOTL 553'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'HOTL 554'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'HOTL 555'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'HOTL 556'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'HOTL 557'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'HOTL 558'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'HOTL 559'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'HOTL 560'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'HOTL 561'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'HOTL 562'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'HOTL 575'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'HRMS 205'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'HRMS 403'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif(($coursecode == 'HUMA 024') && ($course_section == 90)){	
	mail($toSandy, $subject, $mailContent, "From: $email");
	mail($email, $confSubject, $ack, "From: $toSandy");
}	
	
elseif($coursecode == 'HUMA 024'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'HUMA 028'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}

elseif(($coursecode == 'HUMA 035') && ($course_section == 90)){	
	mail($toSandy, $subject, $mailContent, "From: $email");
	mail($email, $confSubject, $ack, "From: $toSandy");
}	

	
elseif($coursecode == 'HUMA 035'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'HUMA 037'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'HUMA 040'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'IDL. 701'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'IDL. 702'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'IDL. 703'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'IDL. 704'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'IDL. 705'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'IDL. 706'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'INDU 906'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}
elseif($coursecode == 'ITAL 191'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'JRNL 015'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	

elseif($coursecode == 'JRNL 017'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'JRNL 025'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'JRNL 205'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'JRNL 815'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'LAND 109'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'LAND 702'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'LAND 810'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'LAND 901'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'LAWC 100'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'LAWC 101'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'LAWC 200'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'LAWC 201'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'LAWS 280'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'LAWS 405'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'LEAD 100'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'LEAD 101'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'LEAD 102'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'LIBR 100'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'LIBR 101'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'LIBR 102'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'LIBR 200'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'LIBR 201'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'LIBR 300'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'LIBR 301'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'MDEC 001'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'MDEC 002'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'MDEC 003'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'MDEC 004'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'MDEC 005'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'MDEC 006'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'MKTG 111'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'MKTG 211'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'MKTG 300'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'MKTG 310'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'MKTG 311'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'MKTG 322'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'MKTG 400'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'MKTG 406'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'MKTG 415'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'MKTG 417'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'MKTG 445'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'MKTG 455'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'MKTG 470'){mail($toChristine, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toChristine");}	
elseif($coursecode == 'MLCP 101'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'MLCP 102'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'MLCP 103'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'MLCP 104'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'MLCP 106'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'MUSC 010'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'NEPH 101'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'NEPH 201'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'NEPH 301'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'NEUR 123'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'NIHM 001'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'NIHM 106'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'NIHM 107'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'NRPN 100'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'NURS 466'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'OBST 102'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'OBST 103'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'OBST 104'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'OBST 106'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'OBST 112'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'OBST 201'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'OBST 206'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'OHSC 100'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'OHSC 102'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'OHSC 103'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'OHSC 105'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'OHSC 107'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'OHSC 108'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'OHSC 109'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'OLC. 100'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'OLC. 101'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'OLC. 102'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'OLC. 103'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'OLC. 104'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'OLC. 300'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'OLC. 400'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'OLC. 401'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'OLC. 402'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'OLC. 403'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'OLC. 404'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'OLC. 405'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'OPER 501'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'OPER 502'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'OPER 503'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'OPER 504'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PFP. 101'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PFP. 106'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PFP. 107'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PFP. 201'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PFP. 203'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PFP. 205'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PFP. 206'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PFP. 210'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PFP. 301'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PFP. 302'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PFP. 303'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PFP. 304'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PFP. 305'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PFP. 306'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PFP. 307'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PFP. 401'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PFP. 402'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PFP. 403'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PFP. 404'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PFP. 405'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PFP. 406'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PFP. 410'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PHAR 310'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'PHAR 311'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'PHAR 312'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'PHAR 313'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'PHIL 017'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'PHIL 025'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'PHIL 027'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'PHIL 030'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'PHIL 405'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'PNCR 100'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'POLS 019'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'POLS 023'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'POLS 024'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'POLS 025'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'POLS 104'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}
elseif($coursecode == 'POLS 200'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'PRDS 105'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PRDS 118'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PRDS 119'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PRDS 120'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PRDS 121'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PRDS 123'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'PSYC 001'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'PSYC 002'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'PSYC 003'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'PSYC 004'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'PSYC 100'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'PSYC 200'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}
elseif($coursecode == 'PSYC 302'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'QENG 111'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'QENG 112'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'QENG 150'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}
elseif($coursecode == 'QENG 151'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'RESP 120'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'RIMC 100'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'RIMC 101'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'RIMC 102'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'RIMC 103'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'RIMC 104'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'RNCC 101'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'SCIE 021'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'SCIE 072'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'SCIE 200'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'SCIE 202'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'SCIE 203'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'SCWR 001'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'SCWR 002'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'SECN 103'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'SECN 104'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'SECN 105'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'SECN 110'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'SECN 521'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'SECN 523'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'SECN 524'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'SECN 525'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'SECN 526'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'SECN 527'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'SECN 550'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'SMC. 101'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'SMC. 102'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'SMC. 103'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'SMC. 104'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'SOCE 006'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'SOCI 002'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}
elseif($coursecode == 'SOCI 005'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'SOCI 006'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'SOCI 029'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'SOCI 033'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'SOCI 036'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'SOCI 075'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'SOCI 077'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'SOCI 201'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'SSW. 205'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'SSW. 303'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'SSW. 403'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'TCJC 501'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'TCJC 504'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'TCJC 507'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'TCJC 508'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'TCJC 510'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'TCJC 511'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'TECH 702'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'TECH 703'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'TECH 704'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'TECH 705'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	


elseif($coursecode == 'TECH 706'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'TECH 707'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'TECH 708'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'TECH 709'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'TECH 710'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'TECH 711'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'TECH 812'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'TECH 813'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}	
elseif($coursecode == 'TRAV 601'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'TRAV 602'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'TRAV 603'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'TRAV 604'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'TRAV 605'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'TRAV 606'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'TRAV 607'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'TRAV 608'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'TRAV 609'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'TRAV 610'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'TRAV 611'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'TRAV 612'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'TRAV 613'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'TRAV 614'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'TSOP 100'){mail($toSandy, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toSandy");}	
elseif($coursecode == 'TSOL 512'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'TSOL 514'){mail($toFalisha, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $toFalisha");}	
elseif($coursecode == 'VOLM 001'){mail($Noreen, $subject, $mailContent, "From: $email"); mail($email, $confSubject, $ack, "From: $Noreen");}


$dbcnx = mysql_connect("localhost", "aqeel", "fall2004");

	if (!$dbcnx) {
	  echo( "<p>Unable to connect to the " .
			"database server at this time.</p>" );
	  exit();
	}

mysql_select_db("proctors", $dbcnx);

$query = "INSERT INTO `testcentre` ( 
		`dateTime` ,
		`firstName` ,
		`lastName` ,
		`studentNum` ,
		`email`,
		`courseCode` ,
		`courseSection` ,
		`proctoringOption` ,
		`campus` ,
		`comments` ) 
VALUES (
		Now(), 
		'".$first_name."',
		'".$last_name."',
		'".$student_number."',
		'".$email."',
		'".$coursecode."',
		'".$course_section."',
		'".$proctoring_option."',
		'".$campus."',
		'".$comments."')";
		
$result = mysql_query($query) or die("Error in query:".mysql_error());

mysql_close($dbcnx); 

// jey's db connection starts here

$dbcnx = mysql_connect("localhost", "olcdata", "olc2011");

	if (!$dbcnx) {
	  echo( "<p>Unable to connect to the " .
			"database server at this time.</p>" );
	  exit();
	}

mysql_select_db("olcdata", $dbcnx);

$query = "INSERT INTO `testcentrerequest` ( `semester` ,
		`dateTime` ,
		`student` ,
		`studentNumber` ,
		`studentEmail`,
		`course` ,
		`proctoringOption` ,
		`campus` ,
		`comments` ) 
VALUES (
		'Fall 2012',
		Now(), 
		'".$first_name." ".$last_name."',
		'".$student_number."',
		'".$email."',
		'".$coursecode." ".$course_section."',
		'".$proctoring_option."',
		'".$campus." ".$proctoring_option."',
		'".$comments."')";
		
$result = mysql_query($query) or die("Error in query:".mysql_error());

mysql_close($dbcnx); 

// jey's db connection ends here



echo '<p class="format">Thank you, <b>'.$first_name.' '.$last_name. '</b>, for submitting the Virtual Proctor Request Form for Online Students. <br /> <br />You will receive an email confirmation from one of the OLC Staff members once we receive the approval for the request. If you are taking an additional online
course, please <a href=\'javascript:history.go(-1)\'>go back</a> and fill the <a href=\'javascript:history.go(-1)\'>Virtual Proctor Request</a> form again.<br /></p>
		
<p class="format">Please call the Open Learning Centre at 416-675-5049 or 1-877-215-6117 or email at   olc@humber.ca if you don\'t receive an email confirmation a week before your final exam date. <br /><br />Thank you for your cooperation in advance.</p>'

?>

</body>
</html>
