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


$msg='';
if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $recaptcha=$_POST['g-recaptcha-response'];
    if(!empty($recaptcha))
    {
        include("getCurlData.php");
        $google_url="https://www.google.com/recaptcha/api/siteverify";
        $secret='Google Secret Key';
        $ip=$_SERVER['REMOTE_ADDR'];
        $url=$google_url."?secret=".$secret."&response=".$recaptcha."&remoteip=".$ip;
        $res=getCurlData($url);
        $res= json_decode($res, true);
        //reCaptcha success check 
        if($res['success'])
        {
        //Include login check code
        }
        else
        {
        $msg="Please re-enter your reCAPTCHA.";
        }

    }
    else
    {
        $msg="Please re-enter your reCAPTCHA.";
    }

}

</html>
