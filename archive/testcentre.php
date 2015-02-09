<?php
/* Required files */
require_once 'SupportFiles/opendb.php';
require_once 'SupportFiles/Messages.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Proctor</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="/navbar/navbar.css" type="text/css" rel="stylesheet" />
		<link rel="stylesheet" type="text/css" href="api/css/main.css" />
        
   <script src="/navbar/navbar.js" type="text/javascript"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script type="text/javascript" src="api/main_actions.js"></script>
</head>
    <body class="main" onload="set_P7_autoHide()"><?php     	include_once "../navbar/goldnavbarxhtml.txt";
			include_once "../navbar/banners/humber-xhtml.txt";
?>

    <div align="center">
    
    
      
     <div class="top-nav">
         
<?php                   include_once "includes/menus.txt"
?>                  
                  
     </div> <!-- navigation bar -->
    <div style="min-height: 620px; max-height: float;" class="stage" id="exception">  <!-- Stage {start} -->
          <!-- Page Content STARTS here -->
       
   <br>
   <img name="testcentre_banner" src="images/testcentre_banner.jpg" width="940" height="70" border="0" alt="Test Centre Form for Online Students"><br>
       
 <iframe src="test-centre.php" width="955" height="1000" scrolling="no" frameborder="0"></iframe>



          
          <!-- Page Content ENDS here -->
      </div>  <!-- Stage {ends} -->            
	<?php      
    include_once "../navbar/footer.txt";
    ?>    


</div>
    </body>
</html>