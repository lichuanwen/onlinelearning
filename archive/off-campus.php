<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Proctor Request Form</title>
<link rel="stylesheet" type="text/css" href="proctorForms/tooltip-generic.css" />
<script src="proctorForms/jquery.tools.min.js"></script>
<script src="proctorForms/formValidater.js" type="text/javascript"></script>
<style type="text/css">

fieldset 
{      
	position: relative;
	float: left;
	clear: both;
	width: 100%;
	margin: 0 0 -1em 0;
	padding: 0 0 1em 0;
	border-style: none;
	border-top: 1px solid #BFBAB0;    
	background-image: url(proctorForms/form_gradient.png);    
	background-repeat: repeat-x;
	font-family: Calibri, Helvetica, sans-serif;
	font-size: 14px;

}     

legend 
{      
	padding: 0;      
	color: #000000;      
	font-weight: bold;   
	font-family: 'Myriad Pro', Verdana, Helvetica, sans-serif;
	font-size: 18px;
  
} 
    
fieldset ol 
{      
	padding: 3em 1em 0 1em;      
	list-style: none;
}  
   
fieldset li 
{      
	float: left;
	clear: left;
	width: 100%;
	padding-bottom: 1em;
  
}     

fieldset.submit 
{      
	float: none;
	width: auto;
	padding-top: 1.5em;
	padding-left: 17em;    
	background-color: #fff;
	background-image: none;
} 

label 
{   
	display: block;
	float: left;   
	width: 15em;   
	margin-right: 1em;   
} 

legend span
{
	position: absolute;
	left: 0.74em;
	top: 0;
	margin-top: 0.2em;
	font-size: 135%;
}

fieldset.alt
{
	background-image: url(proctorForms/alt_form_gradient.png);
	background-repeat: repeat-x;
}

input
{
	font-family: Calibri, Verdana, Geneva, sans-serif;
	font-size: 13px;
	color: #333;
}

</style>
</head>

<body>
<form id="proctor" name="proctor" method="post" action="process_proctors.php" onsubmit="return validate();">
  <fieldset>
  <legend><span>Student Information</span></legend>
  <ol>
	<li>
    <label for="firstName">First Name</label>
    <input name="firstName" type="text" id="firstName" title="Enter your first name here" size="40" /></li>
    <li>
    <label for="lastName">Last Name</label>
    <input name="lastName" type="text" id="lastName" size="40" title="Enter your last name" /></li>
    <li>
    <label for="studentNumber">Student Number</label>
    <input name="studentNum" type="text" id="studentNum" size="40" title="Enter your 9 digits Humber student number" /></li>
    <li>
    <label for="email">Email</label>
    <input name="email" type="text" id="email" size="40" title="Enter your email address" /></li>
    
<!--    <li>
    
    <label for="courseCode">Choose your online course</label>
    <select name="courseCode">
    
    <option></option>
    
    </select>
    
    </li>
-->
    
    <li>
    
    
     <label for="courseCode">Select Your Online Course</label>
    
	<?php
/* Required files */
require_once 'SupportFiles/opendb.php';
require_once 'SupportFiles/Messages.php';

/* Connect to Database */
connection();	
		
$query = "SELECT CONCAT(CName,' ', CNum) AS 'CCode' FROM `courses` ORDER BY `CCode` ASC";
 $results = sendQuery($query);
	
	if ($results){
            printf('<select name="courseCode" id="dd-courses" title="Select your online course" style="width: 100px; font-family: Calibri; font-size: 13px; ">');
            printf('<option value="default" selected>Select Course</option>');
            while ($row = mysqli_fetch_object($results)) {
                printf('<option value="'.$row->CCode.'">'.$row->CCode.'</option>'); 
            }  // while-loop {ends}
            printf('</select>');
        } // if .. results  {ends}
?>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Select Section&nbsp;

<select name="courseSection" title="Select your online course's section" style="width: 80px; font-family: Calibri; font-size: 13px; ">
     <option value="Section">Section</option>
    <option>33</option>
    <option>34</option>
    <option>35</option>
    <option>50</option>
    <option>60</option>
    <option>61</option>
    <option>80</option>
    <option>90</option>
    <option>91</option>
    <option>94</option>
    <option>95</option>
    <option>96</option>
    <option>97</option>
    <option>98</option>
    <option>99</option>
    <option>9A</option>
    <option>9B</option>
    <option>9C</option>
    <option>9E</option>
    <option>9G</option>
    <option>9J</option>
    <option>9N</option>
    <option>9U</option>
    <option>9V</option>
    <option>9Z</option>
    <option>AD</option>
    <option>NM</option>
    <option>F1</option>
    <option>G1</option>
    <option>T1</option>
</select>

    
    
    </li>
  </ol>
  </fieldset>
  
  <fieldset class="alt"><legend><span>Proctor Information</span></legend>
<ol>

<li>
<label for="proctorName">Proctor Name</label>  
<input name="proctorName" type="text" size="40" title="Enter your proctor (exam supervisor's) full name" />
</li>

<li>
<label for="proctoredSite">Academic Institution/Proctored Site
</label>
<input name="proctoredSite" type="text" size="40" title="Enter the full name of the educational institution where you will be writing your final exam" />
</li>


<li>
<label for="proctorPosition">Proctor Position/Title</label>
<input name="proctorPosition" type="text" size="40" title="Enter the position or job title of your proctor" />
</li>


<li>
<label for="proctorEmail">Proctor's Email</label>
<input name="proctorEmail" type="text" size="40" title="Enter your proctor's valid business email address" />
</li>

<li>
<label for="proctorPhone">Proctor's Phone Number</label>
<input name="proctorPhone" type="text" size="40" title="Enter your proctor's phone number with area code" />
</li>

</ol>  </fieldset>



  <fieldset>
  <legend><span>Proctor's Mailing Address</span></legend>
  <ol>
	<li>
    <label for="streetNumName">Street No. & Name</label>
    <input name="streetNumName" type="text" id="streetNumName" size="40" title="Enter the street number and name of your proctor's educational institution" /></li>
    <li>
    <label for="cityTown">City/Town</label>
    <input name="cityTown" type="text" id="cityTown" size="40" title="Enter the city/town name where your proctor's educational institution is located" /></li>
    <li>
    <label for="provinceState">Province/State</label>
    <input name="provinceState" type="text" id="provinceState" size="40" title="Enter the province/state name where your proctor's educational institution is located" /></li>
    <li>
    <label for="postalCode">Postal/Zip Code</label>
    <input name="postalCode" type="text" id="postalCode" size="40" title="Enter the postal code of your proctor's educational institution" /></li>
    <li>
    <label for="country">Country</label>
    <input name="country" type="text" id="country" size="40" title="Enter the name of the country where your proctor's educational institution is located" /></li>

  </ol>
  </fieldset>

  
  
  
  
    <fieldset class="alt">
  <legend><span>Comments/Reasons</span></legend>
  <ol>
    <li>
    <label for="comments">Describe in detail why you are submitting the Off-Campus Proctor request form. Be sure to provide any further detail that may help us process your proctor request faster </label>
    <textarea name="comments" cols="34" rows="8" id="comments" style="overflow: auto;" title="Describe in detail why you are submitting the proctor request form. Be sure to provide any further detail that may help us process your proctor request faster. "></textarea>
</li>
  </ol>

  </fieldset>

  
  
  
  <fieldset class="submit">

<input name="submit" type="submit" value="Submit" title="Click the Submit button once you have filled in all form fields" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input name="Reset" type="reset" value="Reset" title="Reset the form fields" />
  </fieldset>
</form>

<!-- javascript coding -->
<script>
// execute your scripts when the DOM is ready. this is a good habit
$(function() {

// select all desired input fields and attach tooltips to them
$("#proctor :input").tooltip({

	// place tooltip on the right edge
	position: "center right",

	// a little tweaking of the position
	offset: [-2, 10],

	// use the built-in fadeIn/fadeOut effect
	effect: "fade",

	// custom opacity setting
	opacity: 0.7

});
});
</script>

</body>
</html>
