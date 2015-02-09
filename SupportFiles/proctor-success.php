<?php
/* ----------------------------------------------------------------------
 * Name: email-reminder.php
 * Description: Gets a list of scheduled tests running the  
                next day and sends a reminder to all the attendees.
 * Dependencies:  DBOpeartions.php
 * Date: May 25, 2010
 * by R. Alvez
 * -------------------------------------------------------------------- */
include_once 'opendb.php';


function getCurlData($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16");
    $curlData = curl_exec($curl);
    curl_close($curl);
    return $curlData;
}

$issuccessful=false;
if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $recaptcha=$_POST['g-recaptcha-response'];
    if(!empty($recaptcha))
    {

        $google_url="https://www.google.com/recaptcha/api/siteverify";
        $secret='6LeVIQETAAAAADY9i8-bNbFqichOICQ-z9hE6hZL';
        $ip=$_SERVER['REMOTE_ADDR'];
        $url=$google_url."?secret=".$secret."&response=".$recaptcha."&remoteip=".$ip;
        $res=getCurlData($url);
        $res= json_decode($res, true);
        //reCaptcha success check 
        if($res['success'])
        {
        //Include login check code
            $issuccessful = true;
        }
        else
        {
        }

    }
    else
    {
    }

}

    if ($issuccessful){

        if(isset($_POST['formid'])){

            $firstName = htmlspecialchars($_POST['firstName']);
            $lastName = htmlspecialchars($_POST['lastName']);
            $studentNum = htmlspecialchars($_POST['studentNum']);
            $email = htmlspecialchars($_POST['email']);

            $courseCode = htmlspecialchars($_POST['courseCode']);
            $courseName = substr($courseCode, 0, 4);
        	
        	if(strlen($courseCode) > 10){
        		$courseNum = substr($courseCode, 4, 4);
        	} else {
        		$courseNum = substr($courseCode, 4, 3);
        	}
            
            $courseSection = substr($courseCode, -3);
            // var_dump($courseName." ".$courseNum." ".$courseSection); // test only::rga

            $comments = addslashes(htmlspecialchars($_POST['comments']));

            $fid = $_POST['formid'];


            if ($fid === "OCF"){
                if (isset($_POST['streetNumName'])
                    AND isset($_POST['cityTown'])
                    AND isset($_POST['provinceState'])
                    AND isset($_POST['postalCode'])
                    AND isset($_POST['country'])
                    AND isset($_POST['proctorName'])
                    AND isset($_POST['proctoredSite'])
                    AND isset($_POST['proctorPosition'])
                    AND isset($_POST['proctorEmail'])
                    AND isset($_POST['proctorPhone'])
                    ){

                    $formTitle = "Off-Campus Proctor Request Form";
                    $courseDetails = $courseName." ".$courseNum." ".$courseSection;
                    var_dump($courseDetails);

                    $streetNumName = htmlspecialchars($_POST['streetNumName']);
                    $cityTown = htmlspecialchars($_POST['cityTown']);
                    $provinceState = htmlspecialchars($_POST['provinceState']);
                    $postalCode = htmlspecialchars($_POST['postalCode']);
                    $country = htmlspecialchars($_POST['country']);
                    $proctorName = htmlspecialchars($_POST['proctorName']);
                    $proctoredSite = htmlspecialchars($_POST['proctoredSite']);
                    $proctorPosition = htmlspecialchars($_POST['proctorPosition']);
                    $proctorEmail = htmlspecialchars($_POST['proctorEmail']);
                    $proctorPhone = htmlspecialchars($_POST['proctorPhone']);
                }

                $dbcnx = mysql_connect("localhost", "aqeel", "fall2004");

                    if (!$dbcnx) {
                      echo( "<p>Unable to connect to the " .
                            "database server at this time.</p>" );
                      exit();
                    }

                mysql_select_db("proctors", $dbcnx);

                $query = "INSERT INTO `offcampus` ( 
                        `dateTime`,
                        `firstName`,
                        `lastName`,
                        `studentNum`,
                        `email`,
                        `courseCode`,
                        `proctorName`,
                        `proctoredSite`,
                        `proctorPosition`,
                        `proctorEmail`,
                        `proctorPhone`,
                        `streetNumName`,
                        `cityTown`,
                        `provinceState`,
                        `postalCode`,
                        `country`,
                        `comments`) 
                VALUES (
                        Now(), 
                        '".$firstName."',
                        '".$lastName."',
                        '".$studentNum."',
                        '".$email."',
                        '".$courseCode."',
                        '".$proctorName."',
                        '".$proctoredSite."',
                        '".$proctorPosition."',
                        '".$proctorEmail."',
                        '".$proctorPhone."',
                        '".$streetNumName."',
                        '".$cityTown."',
                        '".$provinceState."',
                        '".$postalCode."',
                        '".$country."',
                        '".$comments."')";
                        
                $result = mysql_query($query) or die("Error in query:".mysql_error());

                mysql_close($dbcnx); 

            } elseif ($fid === "VPF") {
                $formTitle = "Virtual Proctor Request Form";
                $courseDetails = $courseName." ".$courseNum." ".$courseSection;

                $dbcnx = mysql_connect("localhost", "aqeel", "fall2004");

                    if (!$dbcnx) {
                      echo( "<p>Unable to connect to the " .
                            "database server at this time.</p>" );
                      exit();
                    }

                mysql_select_db("proctors", $dbcnx);

                $query = "INSERT INTO `virtual` ( 
                        `dateTime`,
                        `firstName`,
                        `lastName`,
                        `studentNum`,
                        `email`,
                        `courseCode`,
                        `comments`) 
                VALUES (
                        Now(), 
                        '".$firstName."',
                        '".$lastName."',
                        '".$studentNum."',
                        '".$email."',
                        '".$courseCode."',
                        '".$comments."')";
                        
                $result = mysql_query($query) or die("Error in query:".mysql_error());

                mysql_close($dbcnx); 

            } elseif ($fid === "TCF"){
                $formTitle = "Test Centre Proctor Request Form";
                $courseDetails = $courseName." ".$courseNum." ".$courseSection;

                if (isset($_POST['campus'])) {
                    $campus = htmlspecialchars($_POST['campus']);
                }

                $dbcnx = mysql_connect("localhost", "aqeel", "fall2004");

                    if (!$dbcnx) {
                      echo( "<p>Unable to connect to the " .
                            "database server at this time.</p>" );
                      exit();
                    }

                mysql_select_db("proctors", $dbcnx);

                $query = "INSERT INTO `testcentre` ( 
                        `dateTime`,
                        `firstName`,
                        `lastName`,
                        `studentNum`,
                        `email`,
                        `courseCode`,
                        `campus`,
                        `comments`) 
                VALUES (
                        Now(), 
                        '".$firstName."',
                        '".$lastName."',
                        '".$studentNum."',
                        '".$email."',
                        '".$courseCode."',
                        '".$campus."',
                        '".$comments."')";
                        
                $result = mysql_query($query) or die("Error in query:".mysql_error());

                mysql_close($dbcnx); 
                
            } else{
                return 'Error';
            }
        }

        $autoReplySubject = $formTitle.": ".$courseDetails;
        $autoReplyEmail = "Thank you, ".$firstName." "." ".$lastName.", for submitting the ".$formTitle." for Online Students.  You will receive an email confirmation from one of the OLC Staff members once we receive the approval for the request.\n"
        ."Please call the OLC at 416-675-5049 or 1-877-215-6117 if you don't receive an email confirmation a week before your final exam date.\n\n"
        ."Regards,\n"
        ."Open Learning Centre\n"
        ."Humber College Institute of Technology and Advanced Learning\n"
        ."Phone: 416-675-5049\n"
        ."Toll Free: 1-877-215-6117";

        $subject = $formTitle.": ".$courseDetails;
        $to = $email;
        
        $studentInfo = "--------STUDENT INFORMATION--------\n\n" 
                ."First Name: ".$firstName."\n"
                ."Last Name: ".$lastName."\n"
                ."Student Number: ".$studentNum."\n"
                ."Email: ".$email."\n"
                ."Course Code: ".$courseDetails."\n\n";

        $emailCommentsContent = "--------COMMENTS--------\n\n"
                            ."Additional Notes: ".$comments."\n";

        if($fid === "OCF"){
            $ocpMsg = "--------PROCTOR INFORMATION--------\n\n"
                    ."Proctor Name: ".$proctorName."\n"
                    ."Proctor Site: ".$proctoredSite."\n"
                    ."Proctor Position: ".$proctorPosition."\n"
                    ."Proctor Email: ".$proctorEmail."\n"
                    ."Phone Number: ".$proctorPhone."\n"
                    ."Street: ".$streetNumName."\n"
                    ."City: ".$cityTown."\n"
                    ."Province: ".$provinceState."\n"
                    ."Postal Code: ".$postalCode."\n"
                    ."Country: ".$country."\n\n";
                
            $emailContent = $formTitle."\n\n\n".$studentInfo.$ocpMsg.$emailCommentsContent;

        } elseif($fid === "VPF"){
            $emailContent = $formTitle."\n\n\n".$studentInfo.$emailCommentsContent;

        } elseif ($fid === "TCF"){
            $tcfMsg = "-------- TEST CENTRE CAMPUS INFORMATION --------\n\n"
                    ."Test Centre Campus: ".$campus."\n\n";

            $emailContent = $formTitle."\n\n\n".$studentInfo.$tcfMsg.$emailCommentsContent;
        }

        

        

        $sql = "CALL spGetLiaison('$courseName', '$courseNum', '$courseSection')";
        $results = sendQuery($sql);

        if($results){
            while($row = mysqli_fetch_object($results)){
                $liaisonEmail = $row->Email;
             }
        }


        if (!$liaisonEmail){
            header('Location: http://www.humber.ca/onlinelearning/proctor.php?error=section');
        } else{
            mail($liaisonEmail, $subject, $emailContent, "From: ".$email);  
            mail($email, $autoReplySubject, $autoReplyEmail, "From: ".$liaisonEmail);
        }
    }
?>

<!doctype html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Proctor Request: Open Learning Center</title>
        <link href="/navbar/navbar.css" type="text/css" rel="stylesheet" />
        <script src="/navbar/navbar.js" type="text/javascript"></script>

        <link rel="stylesheet" href="../css/app.css" />
        <script src="../bower_components/modernizr/modernizr.js"></script>
    </head>
    <body>
        <!-- HUMBER NAVIGATION -->
        <?php include_once("/var/www/humber_global/navigation.php"); ?>
        <?php include_once("/var/www/humber_global/header.php"); ?>
        <!-- END OF HUMBER NAVIGATION -->

        <div id="proctor" class="container">
            <!-- OLC Banner -->
            <div class="full-row olc-banner-row">
                <div class="row">
                    <div class="large-12 columns olc-banner">
                        <h1><a href="../index.php" target="_self">Open Learning Centre</a></h1>
                    </div>
                </div>
            </div>

            <!-- OLC Navigation -->
            <div class="contain-to-grid olc-nav">
                <nav class="top-bar" data-topbar>
                    <ul class="title-area">
                        <li class="name"></li>
                        <li class="toggle-topbar menu-icon"><a href="#">Menu</a></li>
                    </ul>

                    <section class="top-bar-section">
                        <ul class="left">
                            <li><a href="../index.php">Home</a></li>
                            <li><a href="../courses.php">Courses</a></li>
                            <li><a href="../bbhelp.php">Blackboard Help</a></li>
                            <li><a href="../faq.php">FAQs</a></li>
                            <li><a href="../proctor.php">Proctor Request</a></li>
                            <li><a href="../services.php">Student Services</a></li>
                            <li><a href="../contact.php">Contact Us</a></li>
                        </ul>
                    </section>
                </nav>
            </div>

            <!-- Proctor Request Page Content -->
            <div class="row">
                <div class="large-12 columns">
                    <h2 class="heading-background">Proctoring Information</h2>
                </div>
            </div>

            <?php
                if ($issuccessful){
                    echo '<div id="successPage" class="row">';
                    echo '    <div class="large-12 columns">';
                    echo '        <div class="panel radius">';
                    echo '            <h4>Your form has been submitted!</h4>';

                    echo '            <p>Thank you, <span class="bold"><?php echo $firstName.\' \'.$lastName ?></span>, for submitting the <span class="bold"><?php echo $formTitle ?></span> for Online Students. You will receive an email shortly confirming that your form has been sent. If you haven\'t received it, please check your spam folder. Once we receive the approval for the request, you will receive an email confirmation from one of the OLC Staff members once.</p>';
                    echo '            <p>Please call the OLC at 416-675-5049 or 1-877-215-6117 if you don\'t receive an email confirmation a week before your final exam date.</p>';
                    echo '            <p>';
                    echo '                Regards,<br />';
                    echo '                <span class="bold">Open Learning Centre</span><br />';
                    echo '                Humber College Institute of Technology and Advanced Learning<br />';
                    echo '                Phone: 416-675-5049<br />';
                    echo '                Toll Free: 1-877-215-6117';
                    echo '            </p>';
                    echo '            <div class="back-buttons">';
                    echo '                <a href="../proctor.php" target="_self" class="button radius"><span class="icon-left-dir"></span>Back to Proctor Request</a>';
                    echo '                <a href="../index.php" target="_self" class="button radius"><span class="icon-left-dir"></span>Go Home</a>';
                    echo '            </div>';
                    echo '        </div>';
                    echo '    </div>';
                    echo '</div>';
                }
                else{
                    echo '<div id="successPage" class="row">';
                    echo '    <div class="large-12 columns">';
                    echo '        <div class="panel radius">';
                    echo '            <h4>ReCAPTCHA Failed!</h4>';

                    echo '            <p>Sorry, <span class="bold"><?php echo $firstName.\' \'.$lastName ?></span>, Your form hasn\'t been submitted </p>';
                    echo '            <p>';
                    echo '               ';
                    echo '                <span class="bold">Open Learning Centre</span><br />';
                    echo '                Humber College Institute of Technology and Advanced Learning<br />';
                    echo '                Phone: 416-675-5049<br />';
                    echo '                Toll Free: 1-877-215-6117';
                    echo '            </p>';
                    echo '            <div class="back-buttons">';
                    echo '                <a href="../proctor.php" target="_self" class="button radius"><span class="icon-left-dir"></span>Back to Proctor Request</a>';
                    echo '                <a href="../index.php" target="_self" class="button radius"><span class="icon-left-dir"></span>Go Home</a>';
                    echo '            </div>';
                    echo '        </div>';
                    echo '    </div>';
                    echo '</div>';
                }
            ?>
        </div><!-- Container Close-->

        <!-- HUMBER FOOTER -->
        <?php include_once("/var/www/humber_global/footer.php"); ?>
        <!-- END OF HUMBER FOOTER -->
    </body>
    <script src="../bower_components/jquery/jquery.js"></script>
    <script src="../bower_components/foundation/js/foundation.min.js"></script>
    <script src="../js/app.js"></script>
    <script src="../js/custom.js"></script>
</html>
