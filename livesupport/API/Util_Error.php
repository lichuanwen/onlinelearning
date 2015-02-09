<?php
	if ( defined( 'API_Util_Error' ) ) { return ; }	
	define( 'API_Util_Error', true ) ;
	error_reporting(0) ;

	/*****************************************************************/
	function ErrorHandler( $errno, $errmsg, $filename, $linenum, $vars ) 
	{
		global $CONF ; global $KEY ; global $dbh ;
		if ( isset( $dbh ) && isset( $dbh['con'] ) ) { database_mysql_close( $dbh ) ; }

		$ckey = ( isset( $KEY ) ) ? $KEY : "error-no-license" ;
		$query = ( isset( $_SERVER["QUERY_STRING"] ) ) ? $_SERVER["QUERY_STRING"] : "" ;

		// for now set timezone to default... it will be replaced by user's timezone later
		if ( phpversion() >= "5.1.0" ){ date_default_timezone_set( "America/New_York" ) ; }
		$time = date( "D m/d/Y H:i:s" ) ;

		// 600-699 is custom error reserved for PHP Live!
		$errortype = array (
			1		=>  "Error",
			2		=>  "Warning",
			4		=>  "Parsing Error",
			8		=>  "Notice",
			16		=>  "Core Error",
			32		=>  "Core Warning",
			64		=>  "Compile Error",
			128		=>  "Compile Warning",
			256		=>  "User Error",
			512		=>  "User Warning",
			1024	=>  "User Notice",
			600		=>	"PHP Live! DB Connection Failed",
			601		=>	"PHP Live! Configuration Missing",
			602		=>	"PHP Live! Operator Session Expired",
			603		=>	"PHP Live! Chat Request Not Created",
			604		=>	"PHP Live! DB Data Error",
			605		=>	"PHP Live! Error",
			606		=>	"PHP Live! Patch Loop Error",
			607		=>	"PHP Live! version not compatible with WinApp",
			608		=>	"PHP Live! Setup Session Expired",
			609		=>	"PHP Live! directory or file permission denied.",
			610		=>	"PHP Live! -- error placeholder --",
			611		=>	"PHP Live! Update your language pack."
		);
		$user_errors = array( E_ALL ) ;
		if ( $errno == 602 ) { HEADER( "location: $CONF[BASE_URL]/?$query&errno=$errno" ) ; exit ; }
		else if ( $errno == 608 ) { HEADER( "location: $CONF[BASE_URL]/?$query&menu=sa" ) ; exit ; }

		if ( $errno )
		{
			if ( preg_match( "/gethostbyaddr/", $errmsg ) ) { return true ; }
			else
			{
				$errmsg_query = urlencode( $errmsg ) ;

				$admin_email = ( isset( $_SERVER['SERVER_ADMIN'] ) ) ? $_SERVER['SERVER_ADMIN'] : "tech@osicodesinc.com" ;
				$script = ( isset( $_SERVER['SCRIPT_NAME'] ) ) ? $_SERVER['SCRIPT_NAME'] : $filename ;
				$script_encoded = urlencode( $script ) ;
				print "<!DOCTYPE html><html><head><title> Error: $errortype[$errno] </title></head> <body style=\"background: #F6F3F3; margin: 0; padding: 0; overflow: auto; font-family: Arial; font-size: 12px; color: #524F4F;\"> <div style=\"padding: 10px;\"> <div style=\"color: #E1001A; font-size: 16px; font-weight: bold;\">Error: $errortype[$errno]</div> <div style=\"padding-bottom: 5px; padding-top: 10px;\">Live Support system has produced the following error. Make sure the URL you are attempting to access has not been altered.  Please notify the website admin.</div><div style=\"margin-top: 5px; margin-bottom: 5px;\"> <table cellspacing=1 cellpadding=5 border=0> <tr> <td style=\"background: #EBE7E7;\">Time</td><td>: $time</td> </tr> <tr> <td style=\"background: #EBE7E7;\">Error Type</td><td>: [$errno] $errortype[$errno]</td> </tr> <tr> <td style=\"background: #E1001A; color: #FFE9EC;\">Error Message</td><td>: <font color=\"#E1001A\">$errmsg</font> - [ <a href=\"http://www.phplivesupport.com/help_desk.php?errornum=$errno&error=$errmsg_query&script=$script_encoded&line=$linenum&key=$ckey\" style=\"text-decoration: none\" target=\"_blank\"><font color=\"#47B039\">Check for Solutions</font></a> ]</td> </tr> <tr> <td style=\"background: #EBE7E7;\">File Name</td><td>: $script</td> </tr> <tr> <td style=\"background: #EBE7E7;\">File Line #</td><td>: $linenum</td> </tr> <tr> <td style=\"background: #EBE7E7;\">Query</td><td>: $query</td> </tr> </table> </div><div style=\"border: 1px solid #A19E9E; background: #EBE7E7; margin-top: 10px; margin-bottom: 10px;\"><div style=\"padding: 5px;\">Please notify the website admin.</div></div><div style=\"font-size: 10px\">PHP Live! &copy; OSI Codes Inc.</div> </div> </body> </html>" ;

				if ( isset( $admin_email ) && $admin_email )
				{
					//mail( $admin_email, "Subject", "Body", "From: PHP Live! Support System <$admin_email>") ;
				}
				exit ;
			}
		}
	}
	set_error_handler( "ErrorHandler" ) ;
?>