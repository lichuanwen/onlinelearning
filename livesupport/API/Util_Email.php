<?php
	if ( defined( 'API_Util_Email' ) ) { return ; }	
	define( 'API_Util_Email', true ) ;

	$ERROR_EMAIL = "" ;
	function eMailErrorHandler($errno, $errstr, $errfile, $errline) { global $ERROR_EMAIL ; $ERROR_EMAIL = $errstr ; }
	function Util_Email_SendEmail( $from_name, $from_email, $to_name, $to_email, $subject, $message, $extra )
	{
		global $CONF ;
		global $ERROR_EMAIL ;
		$to_name = preg_replace( "/<v>/", "", $to_name ) ;

		if ( $extra == "trans" )
		{
			$message = preg_replace( "/<>/", "\r\n\r\n", $message ) ;
			$message = preg_replace( "/<disconnected><d(\d)>(.*?)<\/div>/", "\r\n$2\r\n=== ===\r\n", $message ) ;
			$message = preg_replace( "/<div class='ca'><i>(.*?)<\/i><\/div>/", "===\r\n$1\r\n===", $message ) ;
			$message = preg_replace( "/<div class='co'><b>(.*?)<timestamp_(\d+)_co>:<\/b> /", "\r\n$1:\r\n", $message ) ;
			$message = preg_replace( "/<div class='cv'><b><v>(.*?)<timestamp_(\d+)_cv>:<\/b> /", "\r\n$1:\r\n", $message ) ;
			$message = preg_replace( "/<(.*?)>/", "", $message ) ;
			$message = stripslashes( preg_replace( "/-dollar-/", "\$", $message ) ) ;
		}

		if ( $extra == "sms" )
		{
			$headers = "From: $from_name <$from_email>" . "\r\n" ;
			$subject_new = $subject ;
			if ( preg_match( "/(russian)/", $CONF["lang"] ) && !preg_match( "/Verification Code:/", $message ) )
				$message = "New chat request." ;
		}
		else
		{
			$headers = "From: ".'=?UTF-8?B?'.base64_encode( $from_name ).'?='." <$from_email>" . "\n" ;
			$headers .= "MIME-Version: 1.0" . "\n" ;
			$headers .= "Content-type: text/plain; charset=UTF-8" . "\r\n" ;
			$subject_new = '=?UTF-8?B?'.base64_encode( $subject ).'?=' ;
		}

		// SMTP
		//ini_set( SMTP, "localhost" ) ;

		if ( $to_email )
		{
			set_error_handler('eMailErrorHandler') ;
			if ( !isset( $CONF["SMTP_PASS"] ) && is_file( "$CONF[DOCUMENT_ROOT]/addons/smtp/API/Util_Extra.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/addons/smtp/API/Util_Extra.php" ) ; }
			if ( isset( $CONF["SMTP_HOST"] ) && isset( $CONF["SMTP_LOGIN"] ) && isset( $CONF["SMTP_PASS"] ) && isset( $CONF["SMTP_PORT"] ) )
			{
				if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/smtp/API/Util_Email_SMTP.php" ) )
				{
					include_once( "$CONF[DOCUMENT_ROOT]/addons/smtp/API/Util_Email_SMTP.php" ) ;

					$subject_new = $subject ;
					$error = Util_Email_SMTP_SwiftMailer( $to_email, $to_name, $from_email, $from_name, $subject_new, $message ) ;
					if ( defined( 'API_Util_Error' ) ) { set_error_handler( "ErrorHandler" ) ; }

					if ( $error == "NONE" ) { return false ; }
					else { return $error ; }
				}
				else
					return "SMTP addon not found or addon upgrade is needed. [e1]" ;
			}
			else
			{
				if ( mail( $to_email, $subject_new, $message, $headers ) ) { if ( defined( 'API_Util_Error' ) ) { set_error_handler( "ErrorHandler" ) ; } return false ; }
				else
				{
					if ( defined( 'API_Util_Error' ) ) { set_error_handler( "ErrorHandler" ) ; }

					if ( preg_match( "/failed to connect/i", $ERROR_EMAIL ) )
						return "Could not connect to local mail server or mail server is not installed." ;
					else
						return "Email error: $ERROR_EMAIL" ;
				}
			}
		}
		else
			return "Recipient is invalid." ;
	}
?>
