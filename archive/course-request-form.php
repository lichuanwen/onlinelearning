<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Virtual Proctor Request Form</title>
<!--<link rel="stylesheet" type="text/css" href="proctorForms/tooltip-generic.css" />
<script src="proctorForms/jquery.tools.min.js"></script>
 --><script src="proctorForms/courseValidator.js" type="text/javascript"></script>

<style type="text/css">

fieldset
{      
	position: relative;
	float: left;
	clear: both;
	width: 700px;
	margin: 0 0 -1em 0;
	padding: 0 0 1em 0;
	border-style: none;
	border-top: 1px solid #BFBAB0;    
	background-image: url(images/course_request_gradient.png);   
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
	width: 700px;
	padding-bottom: 1em;
  
}     

fieldset.submit 
{
	float: none;
	width: 460px;
	padding-top: 1.5em;
	padding-left: 17em;
	background-color: #fff;
	background-image: none;
} 

label 
{   
	display: block;
	float: left;   
	width: 18em;   
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
	background-image: url(images/course_request_small_gradient.png);   
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
<form id="proctor" name="proctor" method="post" action="process_course_request.php" onsubmit="return validate();">


  <!-- The following is the fieldset for choosing virtual or test centre. -->

  <fieldset>
  <legend><span>Faculty Information</span></legend>
  <ol>
	<li>
    <label for="firstName">First Name</label>
    <input name="firstName" type="text" id="firstName" title="Enter your first name here" size="40" /></li>
    <li>
    <label for="lastName">Last Name</label>
    <input name="lastName" type="text" id="lastName" size="40" title="Enter your last name" /></li>
    
        <li>
    <label for="userName">User Name</label>
    <input name="userName" type="text" id="userName" size="40" title="Enter your user name" /></li>

    <li>
    <label for="email">Humber Email</label>
    <input name="email" type="text" id="email" size="40" title="Enter your email address" /></li>
    
    <li>
    <label for="school">Department/School</label>
    <input name="school" type="text" id="school" size="40" title="Enter your school name" /></li>
    
    <li>
        <label for="status">Choose your status</label>
        <table width="200">
          <tr>
            <td><label>
              <input type="radio" name="status" value="full_time" id="status_0" />
              Full Time</label></td>
          </tr>
          <tr>
            <td><label>
              <input type="radio" name="status" value="part_time" id="status_1" />
              Part Time</label></td>
          </tr>
        </table>
    </li>
   </ol>
  </fieldset>
  
  
  
  
  
  <fieldset>
  <legend><span>Type of Course</span></legend>
  <ol>
	<li>
    <label for="courseCode">Course Code</label>
    <input name="courseCode" type="text" id="courseCode" title="Enter the course code here" size="40" /></li>

 
    <li>
        <label for="status">Choose a Course Type
        <br /><br />
		<span style="font-size: 12px; font-style: italic;">An "Academic Development Course (_DEV)" is meant for developing future course content.<br />
			A "Non-academic Development Course (.DEV)" is meant for committees or school or program based courses.<br />
			A "Sandbox (_SBX)" is blank shell course meant for users to test Blackboard environment.
         </span>
        </label>
    
    	<table width="200">
    	  <tr>
    	    <td><label>
    	      <input type="radio" name="courseType" value="Development Course (academic)" id="courseType_0" />
    	      Development Course (academic)</label></td>
  	    </tr>
    	  <tr>
    	    <td><label>
    	      <input type="radio" name="courseType" value="Development Course (non-academic)" id="courseType_1" />
    	      Development Course (non-academic)</label></td>
  	    </tr>
    	  <tr>
    	    <td><label>
    	      <input type="radio" name="courseType" value="Sandbox" id="courseType_2" />
    	      Sandbox</label></td>
  	    </tr>
  	  </table>
    </li>
   </ol>
  </fieldset>

  
  
  
  
  
  
  <!--<fieldset class="alt">
<legend><span>Comments</span></legend>
<ol>

<li>
    <label for="comments">&nbsp;</label>
    <textarea name="comments" cols="34" rows="8" id="comments" style="overflow: auto;"></textarea>
</li>


</ol>  
</fieldset> -->

 
  
  <fieldset class="submit">

<input name="submit" type="submit" value="Submit" title="Click the Submit button once you have filled in all form fields" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input name="Reset" type="reset" value="Reset" title="Reset the form fields" />
  </fieldset>
</form>

</body>
</html>
