<?php
/* Required files */
require_once '../SupportFiles/opendb.php';
require_once '../SupportFiles/Messages.php';

# inspect incoming data
if (isset($_POST['courseid'])){
    $CID = $_POST['courseid'];
} else {
    echo "[axGetRequirements] Missing data. Cannot continue ...";
}

 /* Connect to Database */
connection();

/* Prepare query */
$sql = "CALL spRequisites($CID)";

/* Send query*/
$results = sendQuery($sql);

/* Get Results */
        
if ($results){
    while ($row = mysqli_fetch_object($results)){
        $requirements = array("coursename"=> $row->CName,"coursenumber"=>$row->CNum,"Title"=>$row->Title,"prereq"=>$row->prereq,
            "availability"=>$row->availability,"textbook"=>$row->textbook,"sysreq"=>$row->sysreq,"credentials"=>$row->credentials,
            "login"=>$row->login);
    }  // while-loop {ends}
    echo json_encode($requirements);
} // if .. results  {ends}

?>
