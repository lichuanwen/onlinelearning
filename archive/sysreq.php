<?php
/* Required files */
require_once 'SupportFiles/opendb.php';
require_once 'SupportFiles/Messages.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <title>System Requirements</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
         <!-- Humber's CNB required code [start] -->
        <link href="/navbar/navbar.css" type="text/css" rel="stylesheet">
        <script src="/navbar/navbar.js" type="text/javascript"></script>
        <!-- Humber's required CNB code {ends} -->
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js"></script>
        <script type="text/javascript" src="api/main_actions.js"></script>
        <script type="text/javascript" src="api/scrollbar/jquery.scroll.js"></script>
        <link rel="stylesheet" type="text/css" href="api/css/main.css" />
        <link rel="stylesheet" type="text/css" href="api/scrollbar/css/scrollbar.css" />
        
        
        <style type="text/css">
        
#gradient-style
{
	margin: 25px;
	width: 900px;
	text-align: left;
	border-collapse: collapse;
}
#gradient-style td
{
	height: 560px;
	background-position: center;
	vertical-align: top;
}

.text
{
	font-family: Verdana, Geneva, sans-serif;
	font-size: 11px;
	color: #CCC;
	line-height: 16px;
	font-weight: normal;
	padding-left: 20px;
}

.heading
{
	font-family: 'Myriad Web Pro', sans-serif;
	font-size: 16px;
	color: #fff;
	font-weight: bold;
	padding-left: 20px;
}

        
        </style>
        
    </head>
    <body class="main" onload="set_P7_autoHide()">
    <div align="center">
      <div style="width:955px;text-align:left;position: relative;">
        <!--#include virtual="/navbar/goldnavbarxhtml.txt"-->
          <!--#include virtual="includes/banner.txt"-->
          <?php
          include_once '../navbar/goldnavbarxhtml.txt';
          include_once 'includes/banner.txt';
          ?>
      </div>
          <div class="top-nav">
              <!--#include virtual="includes/menus.txt"-->
              <?php
                 include_once 'includes/menus.txt';              
              ?>
          </div> <!-- navigation bar -->
          
          <div class="stage">  <!-- Stage {start} -->
          <!-- Page Content STARTS here -->
          
 
  
  <p align="center">        
       <table id="gradient-style">


    <tbody>
    	<tr>
        	<td width="450" style="background-image: url(images/pc_req.png); background-repeat: no-repeat;"><br /><br /><br /><br />
			<span class="heading">Operating System</span>
            <br>
            <p class="text">&#8226; Minimum: Windows XP
            <br> 
                &#8226; Recommended: Windows 7
             </p>
             
             <span class="heading">Processor</span>
            <br>
            <p class="text">&#8226; Minimum: 1 GHz or faster 32-bit or 64-bit
            <br> 
               &#8226;  Recommended: 2 GHz or faster 32-bit or 64-bit
             </p>
             
             
             <span class="heading">Memory (RAM)</span>
            <br>
            <p class="text">&#8226; Minimum: 1 GB RAM
            <br> 
                &#8226; Recommended: 3 GB RAM
             or greater</p>
             
             
              <span class="heading">Internet Browser</span>
            <br>
            <p class="text">&#8226; Minimum: Internet Explorer 7 or Chrome 14
            <br> 
                &#8226; Recommended: Internet Explorer 9 or Chrome 16</p>
                
                
                 <span class="heading">Office Suite</span>
            <br>
            <p class="text">&#8226; Minimum: Office 2007
            <br> 
                &#8226; Recommended: Office 2010</p>
                
                <span class="heading">Software Plug-ins</span>
            <br>
            <p class="text">&#8226; Java Software, Adobe Flash, PDF Reader
           </p>
           
            <span class="heading">Hardware Peripherals</span>
            <br>
            <p class="text">&#8226; Headset, Microphone
           </p>
             
             </td>
            
            
          <td width="450" style="background-image: url(images/mac_req.png); background-repeat: no-repeat;"><br /><br /><br /><br />


<span class="heading">Operating System</span>
            <br>
            <p class="text">&#8226; Minimum: MAC OS 10.3
            <br> 
                &#8226; Recommended: MAC OS X 10.4 or above
             </p>
             
             <span class="heading">Processor</span>
            <br>
            <p class="text">&#8226; Minimum: G3 Processor
            <br> 
               &#8226;  Recommended: G4 or higher
             </p>
             
             
             <span class="heading">Memory (RAM)</span>
            <br>
            <p class="text">&#8226; Minimum: 1 GB RAM
            <br> 
                &#8226; Recommended: 3 GB RAM
             or greater</p>
             
             
              <span class="heading">Internet Browser</span>
            <br>
            <p class="text">&#8226; Minimum: Safari 3.2
            <br> 
                &#8226; Recommended: Safari 4 or above</p>
                
                
                 <span class="heading">Office Suite</span>
            <br>
            <p class="text">&#8226; Minimum: Office for MAC 2008
            <br> 
              &#8226; Recommended: Office for MAC 2011</p>
                
              <span class="heading">Software Plug-ins</span>
            <br>
            <p class="text">&#8226; Java Software, Adobe Flash, PDF Reader
           </p>
           
            <span class="heading">Hardware Peripherals</span>
            <br>
            <p class="text">&#8226; Headset, Microphone
           </p>


</td>
        </tr>
    </tbody>
</table></p>
       
                         
          <!-- Page Content ENDS here -->
      </div>  <!-- Stage {ends} -->  

      <div class="footer">
          <!--#include virtual="/navbar/footer.txt"-->
          <?php include_once '../navbar/footer.txt'; ?>
      </div>
    </div>      
    </body>
</html>

