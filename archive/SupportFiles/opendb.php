<?php

/* Opens a connectio to a databse. If connection is made returns a connection
|  object,  otherwise exit() whith an error message.
|  Error messages are customized for `live` and `development` to simplify troubleshooting
 */

include_once('.dbconn/connect.php');
include_once('Messages.php');
include_once('error_level.php');

/* ---------- [ connection object ]*/
function connection(){
	global $credentials;
	global $mysqli;
	$mysqli = new mysqli($credentials['host'], $credentials['username'], $credentials['password'], $credentials['database']);
	return $mysqli;
}

function sendQuery($myquery){
    global $credentials;
    global $error_mode;
    global $mysqli;

    $mysqli = connection();  //new mysqli($credentials['host'], $credentials['username'], $credentials['password'], $credentials['database']);

    if (mysqli_connect_errno()) {
        $message = new NewMessage("Failed to connect","DB","DIV","","420");
        $DBError = mysqli_connect_error();

        if ($error_mode == "live") {
            $message->setMessage("Important","<p>[ OpenConnection ] There was an error talking to the database.</p>","420"); // live
        } else {
            $message->setMessage("<p>[ OpenConnection ] Error # ".mysqli_connect_errno()." talking to the database.</p><p>Error: ". $DBError, 'DB', 'DIV', '','420');
        }
        printf($message->showMessage());
        exit();
    } else {
        $result = $mysqli->query($myquery);
        // Check for errors
        if (mysqli_error($mysqli)){
            $message = new Message();
            $DBError = mysqli_error($mysqli);

        if ($error_mode == "live") {
            $message->setMessage("Error","<p>[ sendQuery ] There was an error talking to the database.</p> ","DB",FALSE,"420"); // live
        } else {
            $message->setMessage("Error # ". mysqli_errno($mysqli),"<p>[ sendQuery ]" .$DBError."</p>",'DIV','HTML',FALSE,"540"); // development
        }

        printf($message->showMessage());
        exit();
        }

        if($result){
            return $result;
        } else {
            $message = new Message();
            $DBError = mysqli_error($mysqli);

        if ($error_mode == "live") {
            $message->setMessage("Error","<p>[ sendQuery ] There was an error talking to the database.</p> ","420"); // live
        } else {
            $message->setMessage("Error # ". mysqli_errno($mysqli),"<p>[ sendQuery ] $DBError </p>",'DIV',"540"); // development
        }

        $error = $message->showMessage();
            printf($error);
        }
    }
 
}  // sendQuery {ends}

?>
