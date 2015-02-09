<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	include_once( "./web/config.php" ) ;
	include_once( "./API/Util_Format.php" ) ;
	include_once( "./API/Util_IP.php" ) ;
	include_once( "./API/Util_Error.php" ) ;
	include_once( "./API/SQL.php" ) ;
	include_once( "./API/Vars/get.php" ) ;

	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$theme = Util_Format_Sanatize( Util_Format_GetVar( "theme" ), "ln" ) ;
	$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "ln" ) ;
	$auto_pop = Util_Format_Sanatize( Util_Format_GetVar( "auto_pop" ), "ln" ) ;
	$vname = Util_Format_Sanatize( Util_Format_GetVar( "vname" ), "ln" ) ;
	$vemail = Util_Format_Sanatize( Util_Format_GetVar( "vemail" ), "e" ) ;
	$vsubject = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "vsubject" ), "htmltags" ) ) ;
	$question = Util_Format_Sanatize( Util_Format_GetVar( "vquestion" ), "htmltags" ) ;
	$onpage = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "onpage" ), "url" ) ) ;  $onpage = ( $onpage ) ? $onpage : "" ;
	$title = Util_Format_Sanatize( Util_Format_GetVar( "title" ), "title" ) ; $title = ( $title ) ? $title : "" ;
	$resolution = Util_Format_Sanatize( Util_Format_GetVar( "win_dim" ), "ln" ) ;
	$widget = Util_Format_Sanatize( Util_Format_GetVar( "widget" ), "n" ) ;
	$custom = Util_Format_Sanatize( Util_Format_GetVar( "custom" ), "ln" ) ;
	$custom_fields_ = Util_Format_Sanatize( Util_Format_GetVar( "custom_fields_" ), "a" ) ;

	$salt = md5( $CONF["SALT"] ) ;

	$theme = ( $theme ) ? $theme : $CONF["THEME"] ;
	$agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : "&nbsp;" ;
	$ip = Util_IP_GetIP() ;
	LIST( $os, $browser ) = Util_Format_GetOS( $agent ) ;
	$mobile = ( $os == 5 ) ? 1 : 0 ;

	$vemail = ( !$vemail ) ? "null" : $vemail ;
	if ( preg_match( "/$ip/", $VALS["CHAT_SPAM_IPS"] ) )
	{
		database_mysql_close( $dbh ) ;
		HEADER( "location: phplive_m.php?ces=$ces&deptid=$deptid&theme=$theme&vname=$vname&vemail=$vemail&vquestion=&onpage=".urlencode( $onpage ) ) ;
		exit ;
	}

	if ( $deptid && $ces && $vname && $question )
	{
		include_once( "./API/Util_Functions.php" ) ;
		include_once( "./API/Util_Functions_itr.php" ) ;
		include_once( "./API/Util_Email.php" ) ;
		include_once( "./API/Depts/get.php" ) ;
		include_once( "./API/Ops/get.php" ) ;
		include_once( "./API/Chat/get_itr.php" ) ;
		include_once( "./API/Chat/put.php" ) ;
		include_once( "./API/Footprints/get_ext.php" ) ;

		$vname_orig = $vname ;
		$vname = "<v>$vname" ;
		$question = preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", preg_replace( "/\"/", "&quot;", $question ) ) ;
		$question_sms = preg_replace( "/<br>/", " ", $question ) ;
		$question_sms = ( strlen( $question_sms ) > 100 ) ? substr( $question_sms, 0, 100 ) . "..." : $question_sms ;
		$sim_ops = "" ;

		$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
		if ( $deptinfo["lang"] )
			$CONF["lang"] = $deptinfo["lang"] ;
		include_once( "./lang_packs/$CONF[lang].php" ) ;

		if ( isset( $custom_fields_[0] ) )
		{
			$custom_fields = unserialize( $deptinfo["custom"] ) ;
			if ( isset( $custom_fields[0] ) )
				$custom .= $custom_fields[0]."-_-".$custom_fields_[0]."-cus-" ;
		}

		if ( $deptinfo["smtp"] )
		{
			$smtp_array = unserialize( Util_Functions_itr_Decrypt( $CONF["SALT"], $deptinfo["smtp"] ) ) ;

			$CONF["SMTP_HOST"] = $smtp_array["host"] ;
			$CONF["SMTP_LOGIN"] = $smtp_array["login"] ;
			$CONF["SMTP_PASS"] = $smtp_array["pass"] ;
			$CONF["SMTP_PORT"] = $smtp_array["port"] ;
		}

		if ( ( $deptinfo["rtype"] < 3 ) || $widget )
		{
			if ( $widget )
			{
				$requestinfo = Chat_get_itr_RequestIPInfo( $dbh, $ip, 1 ) ;

				if ( isset( $requestinfo["opID"] ) )
					$opinfo_next = Ops_get_OpInfoByID( $dbh, $requestinfo["opID"] ) ;
				else
				{
					// set $widget = 0 because it is automated chat invite
					$widget = 0 ;
					$opinfo_next = Ops_get_NextRequestOp( $dbh, $deptid, $deptinfo["rtype"], "" ) ;
				}
			}
			else
				$opinfo_next = Ops_get_NextRequestOp( $dbh, $deptid, $deptinfo["rtype"], "" ) ;

			if ( !isset( $opinfo_next["opID"] ) )
			{
				$url = "phplive_m.php?ces=$ces&chat=1&deptid=$deptid&theme=$theme&vname=$vname&vemail=$vemail&vquestion=".rawurlencode($question)."&title=".rawurlencode($title)."&onpage=".rawurlencode($onpage) ;
				database_mysql_close( $dbh ) ;
				HEADER( "location: $url" ) ; exit ;
			}
			else
				$opid = $opinfo_next["opID"] ;

			if ( $opinfo_next["sms"] == 1 )
				Util_Email_SendEmail( $opinfo_next["name"], $opinfo_next["email"], $vname_orig, base64_decode( $opinfo_next["smsnum"] ), "Chat Request", $question_sms, "sms" ) ;
		}
		else
		{
			$opid = 1111111111 ; $sim_ops = "" ;
			$opinfo_next = Array( "rate" => 0, "sms" => 0 ) ;

			$operators = Depts_get_DeptOps( $dbh, $deptid, 1 ) ;
			for ( $c = 0; $c < count( $operators ); ++$c )
			{
				$operator = $operators[$c] ;
				$sim_ops .= "$operator[opID]-" ;
				if ( $operator["sms"] == 1 )
					Util_Email_SendEmail( $operator["name"], $operator["email"], $vname_orig, base64_decode( $operator["smsnum"] ), "Chat Request", $question_sms, "sms" ) ;
			}
		}

		$referinfo_raw = Footprints_get_IPRefer( $dbh, $ip, 1 ) ;
		$referinfo = isset( $referinfo_raw[0] ) ? $referinfo_raw[0] : Array() ;
		$marketid = ( isset( $referinfo["marketID"] ) && $referinfo["marketID"] ) ? $referinfo["marketID"] : 0 ;

		$refer = ( isset( $referinfo["refer"] ) ) ? $referinfo["refer"] : "" ;
		if ( $requestid = Chat_put_Request( $dbh, $deptid, $opid, 0, $widget, 0, $os, $browser, $ces, $resolution, $vname, $vemail, $ip, $agent, $onpage, $title, $question, $marketid, $refer, $custom, $auto_pop, $sim_ops ) )
		{
			include_once( "./API/Ops/put_itr.php" ) ;
			include_once( "./API/Chat/get.php" ) ;
			include_once( "./API/Chat/update.php" ) ;
			include_once( "./API/Chat/Util.php" ) ;
			include_once( "./API/Marketing/get.php" ) ;
			include_once( "./API/IPs/put.php" ) ;
			include_once( "./API/Footprints/update.php" ) ;

			Footprints_update_FootprintUniqueValue( $dbh, $ip, "chatting", 1 ) ;
			IPs_put_IP( $dbh, $ip, $deptid, 0, 1, 0, 1, 0, 0, time() ) ;
			Chat_put_ReqLog( $dbh, $requestid ) ;

			if ( $widget )
			{
				if ( is_file( "$CONF[TYPE_IO_DIR]/$ip.txt" ) )
					unlink( "$CONF[TYPE_IO_DIR]/$ip.txt" ) ;

				$text = "<widget><div class='ca'><b>$vname</b> ".$LANG["CHAT_NOTIFY_JOINED"]."</div>" ;

				Ops_put_itr_OpReqStat( $dbh, $deptid, $opid, "initiated_", 1 ) ;
				$opinfo = Ops_get_OpInfoByID( $dbh, $opid ) ;
				$filename = $ces."-".$opinfo["opID"] ;
				UtilChat_AppendToChatfile( "$ces.txt", $text ) ;
				UtilChat_AppendToChatfile( "$filename.text", $text ) ;
			}
			else
			{
				if ( !isset( $CONF["cookie"] ) || ( $CONF["cookie"] == "on" ) )
				{
					setcookie( "phplive_vname", $vname_orig, time()+60*60*24*365 ) ;
					if ( $vemail )
						setcookie( "phplive_vemail", $vemail, time()+60*60*24*365 ) ;
				}

				if ( $deptinfo["rtype"] < 3 )
					Ops_put_itr_OpReqStat( $dbh, $deptid, $opid, "requests", 1 ) ;
				else
				{
					Ops_put_itr_OpReqStat( $dbh, $deptid, 0, "requests", 1 ) ;
					for ( $c = 0; $c < count( $operators ); ++$c )
					{
						$operator = $operators[$c] ;
						Ops_put_itr_OpReqStat( $dbh, 0, $operator["opID"], "requests", 1 ) ;
					}
				}
			}

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

			$stars_five = Util_Functions_Stars( 5 ) ; $stars_four = Util_Functions_Stars( 4 ) ;
			$stars_three = Util_Functions_Stars( 3 ) ; $stars_two = Util_Functions_Stars( 2 ) ;
			$stars_one = Util_Functions_Stars( 1 ) ;

			$email_display = ( $vemail != "null" ) ? $vemail : "" ;
			$div_email = ( $deptinfo["temail"] ) ? "<div class='cl'>$LANG[TXT_EMAIL] : <input type='text' class='input_text' size='40' malength='160' id='vemail' name='vemail' value='$email_display'> <input type='button' id='btn_email' value='$LANG[CHAT_BTN_EMAIL_TRANS]' onClick='send_email()'></div>" : "" ;
			$survey = "$div_email<div class='cl'><div class='ctitle'>".$LANG["CHAT_NOTIFY_RATE"]."</div>
				<table cellspacing=0 cellpadding=0 border=0 style='padding-top: 10px; padding-bottom: 10px;'>
					<tr><td><input type='radio' name='rating' id='rating_5' value=5 onClick='submit_survey(this, survey_texts)'></td><td style='padding-left: 2px;'>$stars_five</td>
					<td style='padding-left: 25px;'><input type='radio' name='rating' id='rating_4' value=4 onClick='submit_survey(this, survey_texts)'></td><td style='padding-left: 5px;'>$stars_four</td>
					<td style='padding-left: 25px;'><input type='radio' name='rating' id='rating_3' value=3 onClick='submit_survey(this, survey_texts)'></td><td style='padding-left: 2px;'>$stars_three</td>
					<td style='padding-left: 25px;'><input type='radio' name='rating' id='rating_2' value=2 onClick='submit_survey(this, survey_texts)'></td><td style='padding-left: 2px;'>$stars_two</td>
					<td style='padding-left: 25px;'><input type='radio' name='rating' id='rating_1' value=1 onClick='submit_survey(this, survey_texts)'></td><td style='padding-left: 2px;'>$stars_one</td></tr>
				</table>
			</div>" ;
			$survey = preg_replace( "/(\r\n)|(\n)|(\r)/", "", $survey ) ;

			$socials = Vars_get_Socials( $dbh, $deptid ) ;
			// if does not exist, check the default
			if ( !count( $socials ) && $deptid )
				$socials = Vars_get_Socials( $dbh, 0 ) ;
			
			$socials_string = "" ;
			foreach ( $socials as $social => $data )
			{
				if ( $data["status"] )
					$socials_string .= "<a href=\"$data[url]\" target=\"_blank\" class=\"help_tooltip\" title=\"- $data[tooltip]\"><img src=\"themes/$theme/social/$social.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\"></a> &nbsp;" ;
			}
		}
		else { ErrorHandler( 603, "Chat session did not create.  $dbh[query]<br>$dbh[error].", $PHPLIVE_FULLURL, 0, Array() ) ; }
	}
	else
	{
		$onpage = rawurlencode( $onpage ) ;
		database_mysql_close( $dbh ) ;
		HEADER( "location: phplive.php?d=$deptid&onpage=$onpage" ) ;
		exit ;
	}

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
<title> <?php echo $LANG["CHAT_WELCOME"] ?> </title>

<meta name="description" content="PHP Live! <?php echo $VERSION ?>">
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
<script type="text/javascript" src="./js/tooltip.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/jquery.tools.min.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/autolink.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var base_url = "." ; var base_url_full = "<?php echo $CONF["BASE_URL"] ?>" ;
	var isop = 0 ; var isop_ = 11111111111 ; var isop__ = 0 ;
	var cname = "<?php echo $vname ?>" ; var cemail = "<?php echo $vemail ?>" ;
	var ces = "<?php echo $ces ?>" ;
	var st_typing ;
	var si_timer, si_title, si_typing ;
	var deptid = <?php echo $deptinfo["deptID"] ?> ;
	var temail = <?php echo $deptinfo["temail"] ?> ;
	var rtype = <?php echo $deptinfo["rtype"] ?> ;
	var rtime = <?php echo $deptinfo["rtime"] ?> ;
	var rloop = <?php echo ( $deptinfo["rloop"] ) ? $deptinfo["rloop"] : 1 ; ?> ;
	var cid = "cid_"+unixtime() ;
	var chat_sound = 1 ;
	var title_orig = document.title ;
	var si_counter = 0 ;
	var focused = 1 ;
	var widget = 0 ;
	var wp = 0 ;
	var mobile = <?php echo $mobile ?> ;
	var lang_close = "<?php echo $LANG["CHAT_CLOSE"] ?>" ;
	var sound_new_text = "default" ;
	var salt = "<?php echo $salt ?>" ;
	var theme = "<?php echo $theme ?>" ;

	var marquees = new Array(), marquees_messages = new Array() ;
	var marquee_index = 0 ;

	var loaded = 0 ;
	var newwin_print ;
	var survey_texts = new Array("<?php echo $LANG["CHAT_SURVEY_THANK"] ?>", "<?php echo $LANG["CHAT_CLOSE"] ?>") ;
	var survey = "<?php echo $survey ?>" ;

	var chats = new Object ;
	chats[ces] = new Object ;
	chats[ces]["requestid"] = <?php echo $requestid ?> ;
	chats[ces]["cid"] = cid ;
	chats[ces]["vname"] = cname ;
	<?php if ( isset( $requestinfo ) && $requestinfo["requestID"] ): ?>
	chats[ces]["trans"] = "<xo><div class=\"ca\"><div style=\"margin-top: 10px;\"><img src=\"themes/<?php echo $theme ?>/loading_bar.gif\" border=\"0\" alt=\"\"></div></div></xo>".vars() ;
	<?php else: ?>
	chats[ces]["trans"] = "<xo><div class=\"ca\"><div class=\"info_box\"><i><?php echo $question ?></i></div><div style=\"margin-top: 10px;\"><?php echo $deptinfo["msg_greet"] ?><div style=\"margin-top: 10px;\"><img src=\"themes/<?php echo $theme ?>/loading_bar.gif\" border=\"0\" alt=\"\"></div></div></div></xo>".vars() ;
	<?php endif ; ?>
	chats[ces]["status"] = 0 ;
	chats[ces]["disconnected"] = 0 ;
	chats[ces]["tooslow"] = 0 ;
	chats[ces]["op2op"] = 0 ;
	chats[ces]["deptid"] = 0 ;
	chats[ces]["opid"] = 0 ;
	chats[ces]["opid_orig"] = 0 ;
	chats[ces]["oname"] = "" ;
	chats[ces]["ip"] = "<?php echo $ip ?>" ;
	chats[ces]["chatting"] = 0 ;
	chats[ces]["vsurvey"] = <?php echo $opinfo_next["rate"] ?> ;
	chats[ces]["survey"] = 0 ;
	chats[ces]["timer"] = unixtime() ;
	chats[ces]["istyping"] = 0 ;

	$(document).ready(function()
	{
		$("body").show() ;
		loaded = 1 ;
		init_divs(0) ;
		init_disconnect() ;
		$('#chat_body').empty().html( chats[ces]["trans"] ) ;
		init_scrolling() ;
		init_marquees( "<?php echo $marquee_string ?>" ) ;
		init_typing() ;

		init_tooltips() ;
	});
	$(window).resize(function() {
		if ( !mobile ) { init_divs(1) ; }
	});
	window.onbeforeunload = function() { disconnect() ; }
	$(window).focus(function() {
		input_focus() ;
	});
	$(window).blur(function() {
		focused = 0 ;
	});

	function init_connect( thejson_data )
	{
		init_connect_doit( thejson_data ) ;
	}

	function init_connect_doit( thejson_data )
	{
		isop_ = thejson_data.opid ;
		chats[ces]["status"] = thejson_data.status_request ;
		chats[ces]["oname"] = thejson_data.name ;
		chats[ces]["opid"] = thejson_data.opid ;
		chats[ces]["deptid"] = thejson_data.deptid ;
		chats[ces]["opid_orig"] = thejson_data.opid ;
		chats[ces]["vsurvey"] = thejson_data.rate ;
		chats[ces]["timer"] = unixtime() ;

		// strip out the connecting div
		chats[ces]["trans"] = chats[ces]["trans"].replace( /<xo>(.*)<\/xo>/, "" ) ;

		$('#chat_body').empty().html( chats[ces]["trans"] ) ;
		$('#chat_vname').empty().html( chats[ces]["oname"] ) ;
		$('textarea#input_text').val( "" ) ;
		init_scrolling() ;
		init_textarea() ;

		$('#options_print').show() ;
		init_timer() ;
	}

	function init_chats()
	{
	}

	function cleanup_disconnect( theces )
	{
		if ( !chats[theces]["disconnected"] && chats[theces]["status"] )
		{
			chats[theces]["disconnected"] = unixtime() ;
			var text = "<div class='cl'><?php echo $LANG["CHAT_NOTIFY_VDISCONNECT"] ?></div>" ;
			if ( !chats[theces]["status"] )
			{
				// clear it out so the loading image is not shown
				$('#chat_body').empty().html( "" ) ;
				chats[theces]["trans"] = "" ;
			}

			add_text( text ) ;
			init_textarea() ;
			$('#iframe_chat_engine').attr( "contentWindow" ).stopit(0) ;

			window.onbeforeunload = null ;
			if ( chats[theces]["status"] || ( chats[theces]["status"] == 2 ) )
			{
				chat_survey() ;
				$('#info_disconnect').hide() ;
			}
			else
				window.close() ;
		}
		else if ( !chats[theces]["status"] )
			leave_a_mesg() ;
	}

	function leave_a_mesg()
	{
		<?php if ( $vsubject ): ?>var vsubject = encodeURIComponent( "<?php echo $vsubject ?>" ) ;<?php else: ?>var vsubject = "" ;<?php endif ; ?>

		window.onbeforeunload = null ;
		location.href = base_url_full+"/phplive_m.php?ces=<?php echo $ces ?>&chat=1&deptid=<?php echo $deptid ?>&theme=<?php echo $theme ?>&vname=<?php echo preg_replace( "/<v>/", "", $vname ) ; ?>&vemail=<?php echo $vemail?>&vsubject="+vsubject+"&vquestion=<?php echo rawurlencode($question) ?>&onpage=<?php echo rawurlencode($onpage) ?>" ;
	}

	function send_email()
	{
		if ( !$('#vemail').val() )
			do_alert( 0, "<?php echo $LANG["CHAT_JS_BLANK_EMAIL"] ?>" ) ;
		else if ( !check_email( $('#vemail').val() ) )
			do_alert( 0, "<?php echo $LANG["CHAT_JS_INVALID_EMAIL"] ?>" ) ;
		else
		{
			$('#btn_email').attr( "disabled", true ) ;
			$('#vemail').attr( "disabled", true ) ;

			var unique = unixtime() ;
			var vname = "<?php echo $vname ?>".stripv() ;
			var vemail = $('#vemail').val() ;

			$.get("./phplive_m.php", { action: "send_email_trans", ces: '<?php echo $ces ?>', opid: chats[ces]["opid"], deptid: chats[ces]["deptid"], vname: vname, vemail: vemail, unique: unique },  function(data){
				eval( data ) ;

				if ( json_data.status )
					do_alert( 1, "<?php echo $LANG["CHAT_JS_EMAIL_SENT"] ?>" ) ;
				else
				{
					do_alert( 0, json_data.error ) ;
					$('#btn_email').attr( "disabled", false ) ;
				}
			});
		}
	}

	function init_tooltips()
	{
		var help_tooltips = $( '#chat_options' ).find( '.help_tooltip' ) ;
		help_tooltips.tooltip({
			event: "mouseover",
			track: true,
			delay: 0,
			showURL: false,
			showBody: "- ",
			fade: 0,
			positionLeft: false
		});
	}
//-->
</script>
</head>
<body style="display: none;">

<div id="chat_canvas" style="min-height: 100%; width: 100%;"></div>
<div style="position: absolute; top: 2px; padding: 10px; z-Index: 2;">
	<div id="chat_body" style="overflow: auto;"></div>
	<div id="chat_options" style="padding-top: 10px;">
		<div style="height: 16px;">
			<div id="options_socials" style="float: left; <?php echo ( count( $socials ) ) ? "padding-right: 20px;" : "" ?>"><?php echo $socials_string ?></div>
			<div id="options_print" style="display: none; float: left;">
				<span><img src="./themes/<?php echo $theme ?>/sound_on.png" width="16" height="16" border="0" alt="" onClick="toggle_chat_sound('<?php echo $theme ?>')" id="chat_sound" class="help_tooltip" title="- <?php echo $LANG["CHAT_SOUND"] ?>"></span>
				<span style="padding-left: 10px;"><img src="./themes/<?php echo $theme ?>/printer.png" width="16" height="16" border="0" alt="" onClick="do_print(ces, <?php echo $deptinfo["deptID"] ?>, 0, <?php echo $VARS_CHAT_WIDTH ?>, <?php echo $VARS_CHAT_HEIGHT ?>)" class="help_tooltip" title="- <?php echo $LANG["CHAT_PRINT"] ?>"></span>
				<span id="chat_vtimer" style="position: relative; top: -2px; padding-left: 15px;"></span>
				<span id="chat_processing" style="padding-left: 15px;"><img src="./pics/space.gif" width="16" height="16" border="0" alt=""></span>
				<span id="chat_vname" style="position: relative; top: -2px; padding-left: 15px;"></span>
				<span id="chat_vistyping" style="position: relative; top: -2px;"></span>
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
	<div id="chat_input" style="margin-top: 8px;">
		<textarea id="input_text" rows="3" style="padding: 2px; height: 75px; resize: none;" wrap="virtual" onKeyup="input_text_listen(event);" onKeydown="input_text_typing(event);" disabled><?php echo $LANG["TXT_CONNECTING"] ?></textarea>
	</div>
</div>

<div id="chat_btn" style="position: absolute; z-Index: 10;"><button id="input_btn" type="button" class="input_button" style="<?php echo ( $mobile ) ? "" : "width: 104px; height: 45px; font-size: 14px; font-weight: bold;" ?> padding: 6px;" OnClick="add_text_prepare()" disabled><?php echo $LANG["TXT_SUBMIT"] ?></button></div>
<div id="chat_text_powered" style="position: absolute; margin-top: 5px; font-size: 10px; z-Index: 10;">
	<div id="sounds" style="width: 1px; height: 1px; overflow: hidden; opacity:0.0; filter:alpha(opacity=0);">
		<span id="div_sounds_new_text"></span>
	</div>
</div>

<iframe id="iframe_chat_engine" name="iframe_chat_engine" style="position: absolute; width: 100%; border: 0px; bottom: -50px; height: 20px;" src="ops/p_engine.php?ces=<?php echo $ces ?>" scrolling="no" frameBorder="0"></iframe>

<div id="info_disconnect" class="info_disconnect" style="position: absolute; top: 0px; right: 0px; z-Index: 101;" onClick="disconnect()"><img src="./themes/<?php echo $theme ?>/close_extra.png" width="14" height="14" border="0" alt=""> <?php echo $LANG["TXT_DISCONNECT"] ?></div>

<div id="profile_pic" style="position: absolute; display: none;">
	<div style="background: #FFFFFF; border: 2px solid #446996; width: 100px; height: 100px; -moz-border-radius: 10px; border-radius: 10px;"><img src="themes/winterland/profile_cover.png" width="100" height="100" border="0" alt=""></div>
	<div style="margin-top: 5px; width: 100px;">
		<img src="themes/winterland/email.png" width="16" height="16" border="0" alt="">
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
