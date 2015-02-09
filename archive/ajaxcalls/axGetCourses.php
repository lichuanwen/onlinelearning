<?php

require_once '../SupportFiles/opendb.php';
require_once '../SupportFiles/Messages.php';

if(isset($_POST['school'])){
    $school = $_POST['school'];
} else {
    echo "Missing data. Cannot continue ...";
    exit();
}

connection();

$school = $mysqli->real_escape_string($school);

if ($_POST['school'] == "ALL")
{
	// $sql = "SELECT *, CONCAT(CName,' ', CNum) AS 'CCode' FROM vuCoursesInfo ORDER BY CCode ASC";
	$sql = "SELECT *, CONCAT(CName,' ', CNum) AS 'CCode' FROM vuCoursesInfo WHERE `Short` <> 'Lab' ORDER BY CCode ASC";
	
	$results = sendQuery($sql);

	if ($results)
	{
             	printf('<table class="school-list" width="60%%">');
		printf('<thead><tr><th colspan="2" align="left">Full list</th></tr></thead>');
        	while ($row = mysqli_fetch_object($results))
		{
              	$link="#";
                     printf('<tr><td width="90"><a class="course-info" id="'.$row->course_id.'" 
                     href="'.$link.'">'."$row->CName  $row->CNum $row->CSec".'</a></td><td>'."$row->Title".'</td></tr>'); 
        	}  // while-loop {ends}
	} else {
    $results = "Your query returned no results";
	}// if ... else {ends}
} else
{
	$sql = "SELECT *, CONCAT(CName,' ', CNum) AS 'CCode' FROM vuCoursesInfo WHERE `Short`='$school' ORDER BY CCode ASC";
	$results = sendQuery($sql);
	if ($results){
	$last_school = "none";  // track processed schools
        
        printf('<table class="school-list" width="60%%">');
        while ($row = mysqli_fetch_object($results)) {

                if ($last_school ==  "none"){  // we are just starting ;-)
                        printf('<thead><tr><th colspan="2" align="left">'.$row->School.'</th></tr></thead>');  // print school header
                        $last_school = $row->School;  // update School token
                        // $link ="http://www.onlinelearning.humber.ca/cis/".strtolower($row->CName)."$row->CNum".".html";
                            $link="#";
                        printf('<tr><td width="90"><a class="course-info" id="'.$row->course_id.'" href="'.$link.'" 
                            LMS="'.$row->LMS.'">'."$row->CName  $row->CNum $row->CSec".'</a></td>
                            <td>'."$row->Title".'</td></tr>'); // print Course Name and Number
                	} else if ($last_school == $row->School ){
                         // $link ="http://www.onlinelearning.humber.ca/cis/".strtolower($row->CName)."$row->CNum".".html";
                            $link="#";
                            printf('<tr><td width="90"><a class="course-info" id="'.$row->course_id.'" 
                                href="'.$link.'">'."$row->CName  $row->CNum $row->CSec".'</a></td><td>'."$row->Title".'</td>
                             </tr>'); // continue to print current school data
                        	}else if ($last_school != "none" AND $last_school != $row->School){  // new school data
                        printf('<tr><th colspan="2" align="left">'."$row->School".'</th></tr>');  // print school header
                         // $link ="http://www.onlinelearning.humber.ca/cis/".strtolower($row->CName)."$row->CNum".".html";
                            $link = "#";
                        $current_school = $row->School;  // update School token
                        printf('<tr><td>'."$row->CName  $row->CNum $row->CSec".'</td><td>'."$row->Title".'</td></tr>'); // print Course Name and Number
                }  // if-loop {ends}
        }  // while-loop {ends}
} else {
    $results = "Your query returned no results";
}// if ... else {ends}

}



printf('</table><br /></br />');

printf('<script>  // Bind to divpopup() 
    $(".course-info").bind("click",function(){
        $CID = $(this).attr("id");
        if (($popup == "undefined")||($popup == 0)){
           divpopup($CID);
        } else if ($popup == 1){
            // pass;
        }
    });
    </script>');

?>
