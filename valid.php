<?php
/* ----------------------------------------------------------------------
 * Name: email-reminder.php
 * Description: Gets a list of scheduled tests running the  
                next day and sends a reminder to all the attendees.
 * Dependencies:  DBOpeartions.php
 * Date: May 25, 2010
 * by R. Alvez
 * -------------------------------------------------------------------- */
function getCurlData($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16");
    die($url);
    $curlData = curl_exec($curl);
    curl_close($curl);
    return $curlData;
}

$issuccessful=false;

    $recaptcha="03AHJ_Vuu6nXxdRqVCYo-rR1UVNubdTjMU8sdQ5kg7xd2WxD6HPdGrJyF4SfyefZ4qclLXQANYNJfQ6AhfJxb_d7n6mZlIrSiCbm5UW2F0Vua9dJ0eAkEWXroVU2bCdL8L162TaBTlXmwblHqXcZ76XEl-9SQIu0vQnP5zGzfLbJmr3g5oschXsRa_yypCZwLOI8CMcuGH79iuNf2gAZauOjaO1TSxyw0T_ieh8wT3slpNC5YHmQGIAyJWlnaWX473W69oJTSC17piqlH9sj4i68PJdxNYOBHidry0k_XFY_NJheSEDi7NlrLw_wcGZrJDYYBRtYhjb5h5j1IbcE8LLVrLVOtaMssdCg";
  
        $google_url="https://www.google.com/recaptcha/api/siteverify";
        $secret='6LeVIQETAAAAAB9IxTmCS8nBwERZjkOU8y686to4';
        $ip=$_SERVER['REMOTE_ADDR'];
        $url=$google_url."?secret=".$secret."&response=".$recaptcha."&remoteip=".$ip;
        $res=getCurlData($url);
        $res= json_decode($res, true);
        var_dump($res);
        //reCaptcha success check 
        if($res['success'])
        {
        //Include login check code
            die("asdfasfasdf");
            $issuccessful = true;
        }
        else
        {
            die("failed");
        }

  


?>