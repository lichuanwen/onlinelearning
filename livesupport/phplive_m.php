<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	include_once( "./web/config.php" ) ;
	include_once( "./API/Util_Error.php" ) ;
	include_once( "./API/Util_Format.php" ) ;
	include_once( "./API/Util_IP.php" ) ;
	include_once( "./API/Util_Email.php" ) ;
	include_once( "./API/SQL.php" ) ;
	include_once( "./API/Util_Upload.php" ) ;
	include_once( "./API/Depts/get.php" ) ;
	include_once( "./API/Marketing/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "ln" ) ;
	$theme = Util_Format_Sanatize( Util_Format_GetVar( "theme" ), "ln" ) ;
	$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "n" ) ;
	$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "ln" ) ;
	$chat = Util_Format_Sanatize( Util_Format_GetVar( "chat" ), "n" ) ;
	$vname = preg_replace( "/<v>/", "", Util_Format_Sanatize( Util_Format_GetVar( "vname" ), "ln" ) ) ;
	$vemail = Util_Format_Sanatize( Util_Format_GetVar( "vemail" ), "e" ) ;
	$vsubject = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "vsubject" ), "htmltags" ) ) ;
	$vquestion = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "vquestion" ), "htmltags" ) ) ;
	$onpage = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "onpage" ), "url" ) ) ; $onpage = ( $onpage ) ? $onpage : "" ;
	$custom = Util_Format_Sanatize( Util_Format_GetVar( "custom" ), "ln" ) ;
	$custom_fields_ = Util_Format_Sanatize( Util_Format_GetVar( "custom_fields_" ), "a" ) ;

	$theme = ( $theme ) ? $theme : $CONF["THEME"] ;
	$agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : "&nbsp;" ;
	$ip = Util_IP_GetIP() ;
	LIST( $os, $browser ) = Util_Format_GetOS( $agent ) ;
	$mobile = ( $os == 5 ) ? 1 : 0 ;

	if ( preg_match( "/$ip/", $VALS["CHAT_SPAM_IPS"] ) )
		$spam_exist = 1 ;
	else
		$spam_exist = 0 ;

	$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
	if ( !isset( $deptinfo["deptID"] ) )
	{
		$query = $_SERVER["QUERY_STRING"] ;
		$query = preg_replace( "/^d=(\d+)&/", "d=0&", $query ) ;
		database_mysql_close( $dbh ) ;
		HEADER( "location: phplive.php?$query" ) ; exit ;
	}

	if ( $deptinfo["smtp"] )
	{
		include_once( "./API/Util_Functions_itr.php" ) ;

		$smtp_array = unserialize( Util_Functions_itr_Decrypt( $CONF["SALT"], $deptinfo["smtp"] ) ) ;

		$CONF["SMTP_HOST"] = $smtp_array["host"] ;
		$CONF["SMTP_LOGIN"] = $smtp_array["login"] ;
		$CONF["SMTP_PASS"] = $smtp_array["pass"] ;
		$CONF["SMTP_PORT"] = $smtp_array["port"] ;
	}

	if ( $deptinfo["lang"] )
		$CONF["lang"] = $deptinfo["lang"] ;
	include_once( "./lang_packs/$CONF[lang].php" ) ;

	if ( $action == "send_email" )
	{
		$custom = "" ;
		if ( isset( $custom_fields_[0] ) )
		{
			$custom_fields = unserialize( $deptinfo["custom"] ) ;
			if ( isset( $custom_fields[0] ) )
				$custom .= $custom_fields[0].": ".$custom_fields_[0]."\r\n" ;
		}

		$trans = Util_Format_Sanatize( Util_Format_GetVar( "trans" ), "n" ) ;

		if ( !$vsubject ) { $vsubject = $LANG["CHAT_JS_LEAVE_MSG"] ; }
		if ( $trans )
		{
			include_once( "./API/Ops/get.php" ) ;
			include_once( "./API/Chat/get_ext.php" ) ;

			$opinfo = Ops_get_OpInfoByID( $dbh, $opid ) ;
			$transcript = Chat_ext_get_Transcript( $dbh, $ces ) ;
			$extra = "trans" ;

			$from_name = $vname ;
			$from_email = $vemail ;
			$vname = $opinfo["name"] ;
			$vemail = $opinfo["email"] ;
			// fix the dollar ($) variable
			$message = preg_replace( "/%%transcript%%/", preg_replace( "/\\$/", "-dollar-", $transcript["formatted"] ), $vquestion ) ;
		}
		else
		{
			include_once( "./API/IPs/get.php" ) ;

			$ipinfo = IPs_get_IPInfo( $dbh, $ip ) ;
			if ( $deptinfo["savem"] )
			{
				include_once( "./API/Messages/put.php" ) ;
				include_once( "./API/Messages/remove.php" ) ;

				Messages_remove_LastMessages( $dbh, $deptinfo["savem"] ) ;
				Messages_put_Message( $dbh, $deptid, $chat, $ipinfo["t_footprints"], $ip, $vname, $vemail, $vsubject, $onpage, "", $vquestion ) ;
			}

			$extra = "" ;
			$from_name = $deptinfo["name"] ;
			$from_email = $deptinfo["email"] ;
			$message = "Message to $from_name:\r\n\r\n$vquestion\r\n\r\n======= Visitor Information =======\r\n\r\n$custom"."Name: $vname\r\nEmail: $vemail\r\n\r\nClicked From:\r\n$onpage\r\n\r\nTotal Footprints: $ipinfo[t_footprints]\r\n\r\nIP: $ip\r\n\r\n======\r\n\r\n----\r\n".$LANG["MSG_EMAIL_FOOTER"] ;
		}

		$error = Util_Email_SendEmail( $vname, $vemail, $from_name, $from_email, $vsubject, $message, $extra ) ;

		if ( !$error )
			$json_data = "json_data = { \"status\": 1 };" ;
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"$error\" };" ;

		print "$json_data" ;
		exit ;
	}
	else if ( $action == "send_email_trans" )
	{
		include_once( "./API/Chat/update.php" ) ;
		include_once( "./API/Chat/put_itr.php" ) ;

		Chat_update_TranscriptValue( $dbh, $ces, "vemail", $vemail ) ;
		Chat_update_RequestLogValue( $dbh, $ces, "vemail", $vemail ) ;
		Chat_put_itr_Transcript( $dbh, $ces, 1, "null", "null", $deptid, $opid, "null", "null", 0, "null", $vname, $vemail, "null", "null", "null", "null" ) ;
		$json_data = "json_data = { \"status\": 1 };" ;

		print "$json_data" ;
		exit ;
	}

	if ( is_file( "$CONF[TYPE_IO_DIR]/$ces.txt" ) ) { unlink( "$CONF[TYPE_IO_DIR]/$ces.txt" ) ; }
	$upload_dir = "$CONF[DOCUMENT_ROOT]/web" ;

	$marquees = Marketing_get_DeptMarquees( $dbh, $deptid ) ;
	$marquee_string = "" ;
	for ( $c = 0; $c < count( $marquees ); ++$c )
	{
		$marquee = $marquees[$c] ;
		$snapshot = preg_replace( "/'/", "&#39;", preg_replace( "/\"/", "&quot;", $marquee["snapshot"] ) ) ;
		$message = preg_replace( "/'/", "&#39;", preg_replace( "/\"/", "", $marquee["message"] ) ) ;

		$marquee_string .= "marquees[$c] = '$snapshot' ; marquees_messages[$c] = '$message' ; " ;
	}
	if ( !count( $marquees ) )
		$marquee_string = "marquees[0] = '' ; marquees_messages[0] = '' ; " ;

	include_once( "./inc_cache.php" ) ;
?>
<?php include_once( "./inc_doctype.php" ) ?>
<!--
********************************************************************
* PHP Live! (c) OSI Codes Inc.
* www.phplivesupport.com
********************************************************************
-->
<head>
<title> <?php echo $LANG["MSG_LEAVE_MESSAGE"] ?> </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta name="phplive" content="version: <?php echo $VERSION ?>">
<meta http-equiv="content-type" content="text/html; CHARSET=<?php echo $LANG["CHARSET"] ?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width">

<link rel="Stylesheet" href="./themes/<?php echo $theme ?>/style.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="./js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/global_chat.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/framework_cnt.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var marquees = marquees_messages = new Array() ;
	var marquee_index = 0 ;
	var mobile = <?php echo $mobile ?> ;

	$(document).ready(function()
	{
		$("body").show() ;
		init_divs_pre() ;

		init_marquees( "<?php echo $marquee_string ?>" ) ;
	});
	$(window).resize(function() {
		if ( !mobile ) { init_divs_pre() ; }
	});

	function init_divs_pre()
	{
		var browser_height = $(window).height() ; var browser_width = $(window).width() ;
		var body_height = browser_height - $('#chat_footer').height() - 45 ;
		var body_width = browser_width - 42 ;
		var logo_width = body_width - 10 ;
		var deptid_width = logo_width + 5 ;
		var input_width = Math.floor( logo_width/2 ) - 19 ;
		var subject_width = ( input_width * 2 ) + 35 ;

		$('#chat_logo').css({'width': logo_width}) ;
		$('#chat_body').css({'height': body_height, 'width': body_width}) ;
		$('#vdeptid').css({'width': deptid_width}) ;
		$('#vname').css({'width': input_width }) ;
		$('#vemail').css({'width': input_width }) ;
		$('#vsubject').css({'width': input_width }) ;
		$('#vquestion').css({'width': input_width }) ;
	}

	function do_submit()
	{
		if ( !$('#vname').val() )
		{
			do_alert( 0, "<?php echo $LANG["CHAT_JS_BLANK_NAME"] ?>" ) ;
			return false ;
		}
		if ( !$('#vemail').val() )
		{
			do_alert( 0, "<?php echo $LANG["CHAT_JS_BLANK_EMAIL"] ?>" ) ;
			return false ;
		}
		if ( !check_email( $('#vemail').val() ) )
		{
			do_alert( 0, "<?php echo $LANG["CHAT_JS_INVALID_EMAIL"] ?>" ) ;
			return false ;
		}
		if ( !$('#vsubject').val() )
		{
			do_alert( 0, "<?php echo $LANG["CHAT_JS_BLANK_SUBJECT"] ?>" ) ;
			return false ;
		}
		if ( !$('#vquestion').val() )
		{
			do_alert( 0, "<?php echo $LANG["CHAT_JS_BLANK_QUESTION"] ?>" ) ;
			return false ;
		}
		
		do_it() ;
	}

	function do_it()
	{
		var unique = unixtime() ;
		var vname = $('#vname').val() ;
		var vemail = $('#vemail').val() ;
		var vsubject = encodeURIComponent( $('#vsubject').val() ) ;
		var vquestion =  encodeURIComponent( $('#vquestion').val() ) ;
		var onpage =  encodeURIComponent( "<?php echo $onpage ?>" ) ;

		$('#chat_button_start').attr( "disabled", true ) ;
		$.post("./phplive_m.php", { action: "send_email", deptid: <?php echo $deptid ?>, chat: <?php echo $chat ?>, vname: vname, vemail: vemail, vsubject: vsubject, vquestion: vquestion, onpage: onpage, unique: unique },  function(data){
			eval( data ) ;

			if ( json_data.status )
			{
				do_alert( 1, "<?php echo $LANG["CHAT_JS_EMAIL_SENT"] ?>" ) ;
				$('#chat_button_start').attr( "disabled", true ) ;
				$('#chat_button_start').html( "<img src=\"./themes/<?php echo $theme ?>/alert_good.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\"> <?php echo $LANG["CHAT_JS_EMAIL_SENT"] ?>" ) ;
			}
			else
			{
				do_alert( 0, json_data.error ) ;
				$('#chat_button_start').attr( "disabled", false ) ;
				$('#chat_button_start').html( "<?php echo $LANG["CHAT_BTN_EMAIL"] ?>" ) ;
			}
		});
	}

//-->
</script>
</head>
<body style="">

<div id="chat_canvas" style="min-height: 100%; width: 100%;"></div>
<div style="position: absolute; top: 2px; padding: 10px; z-Index: 2;">
	<div id="chat_body" style="padding: 10px;">

		<div id="chat_logo" style="width: 100%; height: 45px; background: url( <?php echo Util_Upload_GetLogo( ".", $deptid ) ?> ) no-repeat;"></div>
		<div id="chat_text_header" style="margin-top: 10px;"><?php echo $LANG["MSG_LEAVE_MESSAGE"] ?></div>
		<div id="chat_text_header_sub" style="margin-top: 5px;"><?php echo ( $chat && $deptinfo["msg_busy"] ) ? $deptinfo["msg_busy"] : $deptinfo["msg_offline"] ; ?></div>

		<form method="POST" action="phplive_m.php?submit" id="theform" accept-charset="<?php echo $LANG["CHARSET"] ?>">
		<input type="hidden" name="action" value="submit">
		<input type="hidden" name="deptid" id="deptid" value="<?php echo $deptid ?>">
		<input type="hidden" name="ces" value="<?php echo $ces ?>">
		<input type="hidden" name="onpage" value="<?php echo $onpage ?>">
		<div style="margin-top: 10px;">
			<table cellspacing=0 cellpadding=0 border=0>
			<tr>
				<td style="padding-bottom: 10px;">
					<div style="margin-top: 5px;">
					<?php echo $LANG["TXT_NAME"] ?><br>
					<input type="input" class="input_text" id="vname" name="vname" maxlength="40" value="<?php echo ( $vname ) ? $vname : "" ; ?>" onKeyPress="return noquotestags(event)">
					</div>
				</td>
				<td style="padding-bottom: 10px;">
					<div style="margin-top: 5px; margin-left: 23px;">
					<?php echo $LANG["TXT_EMAIL"] ?><br>
					<input type="input" class="input_text" id="vemail" name="vemail" maxlength="160" value="<?php echo ( $vemail && ( $vemail != "null" ) ) ? $vemail : "" ; ?>">
					</div>
				</td>
			</tr>
			<tr>
				<td colspan=2 style="padding-bottom: 10px;">
					<div style="margin-top: 5px;">
					<?php echo $LANG["TXT_SUBJECT"] ?><br>
					<input type="input" class="input_text" id="vsubject" name="vsubject" maxlength="155" value="<?php echo ( $vsubject ) ? $vsubject : "" ; ?>">
					</div>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<div style="margin-top: 5px;">
					<?php echo $LANG["TXT_MESSAGE"] ?><br>
					<textarea class="input_text" id="vquestion" name="vquestion" rows="4" wrap="virtual" style="resize: none;"><?php echo ( $vquestion ) ? preg_replace( "/&lt;br&gt;/i", "\r\n", $vquestion ) : "" ?></textarea>
					</div>
				</td>
				<td valign="top">
					<div style="margin-top: 5px; margin-left: 23px;">
						&nbsp;<br>
						<div id="chat_btn" style="margin-top: 5px;"><button id="chat_button_start" type="button" class="input_button" style="<?php echo ( $mobile ) ? "" : "width: 150px; height: 45px; font-size: 14px; font-weight: bold;" ?> padding: 6px;" onClick="do_submit()"><?php echo $LANG["CHAT_BTN_EMAIL"] ?></button></div>
					</div>
				</td>
			</tr>
			</table>
		</div>
		</form>

	</div>
</div>

<?php if ( !$mobile ): ?>
<div id="chat_footer" style="position: relative; width: 100%; margin-top: -28px; height: 28px; padding-top: 7px; padding-left: 15px; z-Index: 10;"></div>
<?php endif ; ?>

</body>
</html>
<?php
	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;
?>