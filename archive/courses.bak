<?php
/* Required files */
require_once 'SupportFiles/opendb.php';
require_once 'SupportFiles/Messages.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Courses</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="/navbar/navbar.css" type="text/css" rel="stylesheet" />
		<script src="/navbar/navbar.js" type="text/javascript"></script>

        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
        <script type="text/javascript" src="api/main_actions.js"></script>
		<script type="text/javascript" src="api/scrollbar/jquery.scroll.js"></script>
        <link rel="stylesheet" type="text/css" href="api/css/main.css" />
   </head>
    <body class="main" onload="set_P7_autoHide()">
   
<?php     	include_once "../navbar/goldnavbarxhtml.txt";
			include_once "../navbar/banners/humber-xhtml.txt";
?>

    <div align="center">
    
    
      
     <div class="top-nav">
         
<?php                   include_once "includes/menus.txt"
?>                  
                  
     </div> <!-- navigation bar -->
    <div style="min-height: 620px; max-height: float;" class="stage" id="exception">  <!-- Stage {start} -->
          <!-- Page Content STARTS here -->
          
          
          
<br /><br />
  
  	<img src="images/courses_info.png" width="855" height="127"><br /> <br /> <br />
      <?php
        /* Connect to Database */
        connection();

        /* Prepare query */
        $sql = "SELECT * FROM `schools` ORDER BY `school_id`";
        /* Send query*/
        $results = sendQuery($sql);
        /* Get Results */
        
        if ($results){
            printf('<select name="sch" id="dd-schools" style="width: 375px;">');
            printf('<option value="default" selected>Select School</option>');
            while ($row = mysqli_fetch_object($results)) {
                printf('<option value="'.$row->school_id.'" title="'.$row->Short.'">'.$row->School.'</option>'); 
            }  // while-loop {ends}
            printf('</select>');
        } // if .. results  {ends}
        ?>
          
  <div id="insertion-point"></div> <br /><!-- DB query results are placed after this  point -->
          
          <!-- Page Content ENDS here -->
      </div>  <!-- Stage {ends} -->            
	<?php      
    include_once "../navbar/footer.txt";
    ?>    


</div>
    </body>
</html>

