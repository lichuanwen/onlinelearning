<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	if ( !is_file( "./web/VERSION.php" ) ) { touch( "./web/VERSION.php" ) ; }  // patch 4.2.105 adjustment
	include_once( "./web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_IP.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
	/* AUTO PATCH */
	if ( !is_file( "$CONF[DOCUMENT_ROOT]/web/patches/$patch_v" ) )
	{
		$query = ( isset( $_SERVER["QUERY_STRING"] ) ) ? $_SERVER["QUERY_STRING"] : "" ;
		HEADER( "location: patch.php?from=chat&".$query ) ;
		exit ;
	}
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get_itr.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_itr.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Marketing/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/remove.php" ) ;

	$ces = Util_Security_GenSetupSes() ;
	$onpage = Util_Format_Sanatize( Util_Format_GetVar( "onpage" ), "url" ) ; $onpage = ( $onpage ) ? $onpage : "" ;
	$title = Util_Format_Sanatize( Util_Format_GetVar( "title" ), "title" ) ; $title = ( $title ) ? $title : "" ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "d" ), "n" ) ;
	$theme = Util_Format_Sanatize( Util_Format_GetVar( "theme" ), "ln" ) ;
	$widget = Util_Format_Sanatize( Util_Format_GetVar( "widget" ), "n" ) ;
	$embed = Util_Format_Sanatize( Util_Format_GetVar( "embed" ), "n" ) ;
	$js_name = Util_Format_Sanatize( Util_Format_GetVar( "js_name" ), "ln" ) ;
	$js_email = Util_Format_Sanatize( Util_Format_GetVar( "js_email" ), "e" ) ;
	$custom = Util_Format_Sanatize( Util_Format_GetVar( "custom" ), "ln" ) ;

	$marquee_test = Util_Format_ConvertQuotes( Util_Format_Sanatize( Util_Format_GetVar( "marquee_test" ), "notags" ) ) ;

	$agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : "&nbsp;" ;
	$ip = Util_IP_GetIP() ;
	LIST( $os, $browser ) = Util_Format_GetOS( $agent ) ;
	$mobile = ( $os == 5 ) ? 1 : 0 ;

	$cookie = ( !isset( $CONF["cookie"] ) || ( $CONF["cookie"] == "on" ) ) ? 1 : 0 ;
	$theme = ( $theme ) ? $theme : $CONF["THEME"] ;

	$temp_vname = ( !$js_name && ( isset( $_COOKIE["phplive_vname"] ) && $cookie ) ) ? $_COOKIE["phplive_vname"] : $js_name ;
	$temp_vemail = ( !$js_email && ( isset( $_COOKIE["phplive_vemail"] ) && $cookie ) ) ? $_COOKIE["phplive_vemail"] : $js_email ;
	$vname = ( $temp_vname ) ? $temp_vname : "" ;
	$vemail = ( $temp_vemail ) ? $temp_vemail : "" ;
	$dept_offline = $dept_settings = $dept_customs = "" ;

	// automatic clean
	Footprints_remove_Expired_U( $dbh ) ;

	if ( preg_match( "/$ip/", $VALS["CHAT_SPAM_IPS"] ) )
		$spam_exist = 1 ;
	else
		$spam_exist = 0 ;

	if ( is_file( "$CONF[TYPE_IO_DIR]/$ip.txt" ) )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/update.php" ) ;

		$initiate_array = ( isset( $CONF["auto_initiate"] ) && $CONF["auto_initiate"] ) ? unserialize( html_entity_decode( $CONF["auto_initiate"] ) ) : Array() ;

		$auto_initiate_reset = 0 ;
		if ( isset( $initiate_array["reset"] ) )
			$auto_initiate_reset = $initiate_array["reset"] ;

		$reset = 60*60*24*$auto_initiate_reset ;
		IPs_update_IpValue( $dbh, $ip, "i_initiate", time() + $reset ) ;

		unlink( "$CONF[TYPE_IO_DIR]/$ip.txt" ) ;
	}

	if ( $widget )
	{
		$requestinfo = Chat_get_itr_RequestIPInfo( $dbh, $ip, 1 ) ;
		$deptid = ( isset( $requestinfo["deptID"] ) ) ? $requestinfo["deptID"] : $deptid ;
		// reset widget if auto invitation was not located
		$widget = ( isset( $requestinfo["deptID"] ) ) ? 1 : 0 ;
	}

	$total_ops = 0 ; $dept_online = Array() ; $departments = Array() ;
	if ( $deptid )
	{
		$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
		$departments[0] = $deptinfo ;
		if ( !isset( $deptinfo["deptID"] ) )
		{
			$query = $_SERVER["QUERY_STRING"] ;
			$query = preg_replace( "/(d=(.*?)(&|$))/", "d=0&", $query ) ;
			database_mysql_close( $dbh ) ;
			HEADER( "location: phplive.php?$query" ) ; exit ;
		}

		$total = ( $widget ) ? 1 : Ops_get_itr_AnyOpsOnline( $dbh, $deptinfo["deptID"] ) ;
		$total_ops += $total ;
		$dept_online[$deptinfo["deptID"]] = $total ;
		$dept_offline .= "dept_offline[$deptinfo[deptID]] = '".preg_replace( "/'/", "", preg_replace( "/\"/", "\"", $deptinfo["msg_offline"] ) )."' ; " ;
		$dept_settings .= " dept_settings[$deptinfo[deptID]] = Array( $deptinfo[remail], $deptinfo[temail] ) ; " ;
		$custom_fields = ( $deptinfo["custom"] ) ? unserialize( $deptinfo["custom"] ) : Array() ;
		if ( isset( $custom_fields[0] ) )
			$dept_customs .= " dept_customs[$deptinfo[deptID]] = Array( '$custom_fields[0]', $custom_fields[1] ) ;" ;
		
		if ( $deptinfo["lang"] ) { $CONF["lang"] = $deptinfo["lang"] ; }
	}
	else
	{
		$departments_pre = Depts_get_AllDepts( $dbh ) ;
		for ( $c = 0; $c < count( $departments_pre ); ++$c )
		{
			$department = $departments_pre[$c] ;
			if ( $department["visible"] ) { $departments[] = $department ; }
		}

		for ( $c = 0; $c < count( $departments ); ++$c )
		{
			$department = $departments[$c] ;
			if ( $spam_exist )
				$total = 0 ;
			else
				$total = Ops_get_itr_AnyOpsOnline( $dbh, $department["deptID"] ) ;
			$total_ops += $total ;

			$dept_online[$department["deptID"]] = $total ;
			$dept_offline .= "dept_offline[$department[deptID]] = '".preg_replace( "/'/", "", preg_replace( "/\"/", "\"", $department["msg_offline"] ) )."' ; " ;
			$dept_settings .= " dept_settings[$department[deptID]] = Array( $department[remail], $department[temail] ) ; " ;
			$custom_fields = ( $department["custom"] ) ? unserialize( $department["custom"] ) : Array() ;
			if ( isset( $custom_fields[0] ) )
				$dept_customs .= " dept_customs[$department[deptID]] = Array( '$custom_fields[0]', $custom_fields[1] ) ;" ;
		}

		if ( count( $departments ) == 1 )
			$deptid = $departments[0]["deptID"] ;
	}

	include_once( "./setup/KEY.php" ) ;
	$upload_dir = "$CONF[DOCUMENT_ROOT]/web" ;

	if ( $marquee_test )
		$marquees = Array( Array( "snapshot" => "", "message" => "$marquee_test" ) ) ;
	else
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
		$$marquee_string = "marquees[0] = '' ; marquees_messages[0] = '' ; " ;

	include_once( "./lang_packs/$CONF[lang].php" ) ;
	/////////////////////////////////////////////
	if ( defined( "LANG_CHAT_WELCOME" ) || !isset( $LANG["CHAT_JS_CUSTOM_BLANK"] ) )
		ErrorHandler( 611, "Update to your custom language file is required ($CONF[lang]).  Copy an existing language file and create a new custom language file.", $PHPLIVE_FULLURL, 0, Array() ) ;

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
<script type="text/javascript" src="./js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/framework_cnt.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/jquery.tools.min.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var marquees = marquees_messages = new Array() ;
	var marquee_index = 0 ;
	var widget = <?php echo $widget ?> ;
	var mobile = <?php echo $mobile ?> ;

	var win_width = screen.width ;
	var win_height = screen.height ;
	var input_width ;

	var dept_offline = new Object ;
	var dept_settings = new Object ;
	var dept_customs = new Object ;

	var onoff = 0 ;

	var custom_required = 0 ;

	var js_email = "<?php echo $js_email ?>" ;

	$(document).ready(function()
	{
		$('#win_dim').val( win_width + " x " + win_height ) ;

		<?php echo $dept_offline ?>

		<?php echo $dept_settings ?>

		<?php echo $dept_customs ?>

		<?php echo $marquee_string ?>

		check_logo_dim() ;
		init_divs_pre() ;
		init_marquees() ;

		$('#chat_button_start').html( "<?php echo $LANG["CHAT_BTN_START_CHAT"] ?>" ).unbind('click').bind('click', function() {
			start_chat() ;
		}) ;

		<?php
			if ( isset( $requestinfo["ces"] ) ) { print "start_chat() ; " ; }
			else { print "select_dept( $deptid ) ; $('body').show() ; " ; }
		
			if ( !$deptid )
				print "$('#div_vdeptids').show() ; " ;
		?>

		var key_count = 0 ;
		for (var o in dept_offline) {
			key_count++ ;
		}
		if ( !key_count )
		{
			$('#pre_chat_form').hide() ;
			$('#pre_chat_no_depts').show() ;
		}

		flashembed( "flash_result", {
			src: "./media/expressInstall.swf",
			version: [8, 0],
			expressInstall: "./media/expressInstall.swf",
			onFail: function() {
				<?php if ( !$mobile ): ?>$('#flash_detect').show() ;<?php endif ; ?>
			}
		});
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
		var deptid_width = body_width ;
		input_width = Math.floor( body_width/2 ) - 24 ;

		$('#chat_body').css({'height': body_height, 'width': body_width}) ;
		$('#vdeptid').css({'width': deptid_width}) ;
		$('#vsubject').css({'width': input_width }) ;
		$('#vname').css({'width': input_width }) ;
		$('#vemail').css({'width': input_width }) ;
		$('#vemail_null').css({'width': input_width }) ;
		$('#custom_field_input').css({'width': input_width }) ;

		if ( mobile )
			$('#vquestion').css({'height': "45px" }) ;
		else
			$('#vquestion').css({'width': input_width }) ;
	}

	function check_logo_dim()
	{
		var img = new Image() ;
		img.onload = get_img_dim ;
		img.src = '<?php print Util_Upload_GetLogo( ".", $deptid ) ?>' ;
	}

	function get_img_dim()
	{
		var img_width = this.width ;
		var img_height = this.height ;

		$('#chat_logo').css({'width': img_width, 'height': img_height}) ;
	}

	function select_dept( thevalue )
	{
		$('#deptid').val( thevalue ) ;
		$('#custom_field_input').val('') ;

		if ( thevalue && ( typeof( dept_customs[thevalue] ) != "undefined" ) )
		{
			$('#custom_field_title').html( dept_customs[thevalue][0] ) ;
			$('#div_customs').show() ;
			custom_required = dept_customs[thevalue][1] ;
		}
		else
		{
			$('#div_customs').hide() ;
			custom_required = 0 ;
		}

		if ( ( $('#vdeptid option:selected').attr( "class" ) == "offline" ) )
		{
			onoff = 0 ;
			$('#chat_text_header_sub').html( dept_offline[thevalue] ) ;
			if ( parseInt( js_email ) ) { $('#vemail').val( js_email ).attr( "disabled", true ) ; }
			$('#div_subject').show() ;
			$('#chat_button_start').html( "<?php echo $LANG["CHAT_BTN_EMAIL"] ?>" ).unbind('click').bind('click', function() {
				send_email() ;
			});
		}
		else
		{
			onoff = 1 ;
			$('#chat_text_header_sub').html( "<?php echo preg_replace( "/\"/", "&quot;", $LANG["CHAT_WELCOME_SUBTEXT"] ) ?>" ) ;
			if ( thevalue && ( typeof( dept_settings[thevalue] ) != "undefined" ) )
			{
				if ( dept_settings[thevalue][0] )
				{
					if ( parseInt( js_email ) ) { $('#vemail').val( js_email ).attr( "disabled", true ) ; }
				}
			}

			$('#div_subject').hide() ;
			$('#chat_button_start').html( "<?php echo $LANG["CHAT_BTN_START_CHAT"] ?>" ).unbind('click').bind('click', function() {
				start_chat() ;
			});
		}
	}

	function check_form( theflag )
	{
		var error = 0 ;

		if ( widget )
			return true ;

		var deptid = parseInt( $('#deptid').val() ) ;

		if ( !deptid ){
			do_alert( 0, "<?php echo $LANG["CHAT_JS_BLANK_DEPT"] ?>" ) ;
			return false ;
		}
		if ( !$('#vsubject').val() ){
			if ( theflag )
			{
				do_alert( 0, "<?php echo $LANG["CHAT_JS_BLANK_SUBJECT"] ?>" ) ;
				return false ;
			}
		}
		if ( !$('#vname').val() ){
			do_alert( 0, "<?php echo $LANG["CHAT_JS_BLANK_NAME"] ?>" ) ;
			return false ;
		}
		if ( !$('#vemail').val() ){
			if ( dept_settings[deptid][0] || theflag )
			{
				do_alert( 0, "<?php echo $LANG["CHAT_JS_BLANK_EMAIL"] ?>" ) ;
				return false ;
			}
		}
		if ( !$('#vquestion').val() ){
			do_alert( 0, "<?php echo $LANG["CHAT_JS_BLANK_QUESTION"] ?>" ) ;
			return false ;
		}
		if ( !check_email( $('#vemail').val() ) ){
			if ( dept_settings[deptid][0] || theflag )
			{
				do_alert( 0, "<?php echo $LANG["CHAT_JS_INVALID_EMAIL"] ?>" ) ;
				return false ;
			}
		}
		if ( custom_required && !$('#custom_field_input').val() ){
			do_alert( 0, "<?php echo $LANG["CHAT_JS_CUSTOM_BLANK"] ?>"+" "+$('#custom_field_title').html() ) ;
			return false ;
		}

		return true ;
	}

	function start_chat()
	{
		if ( check_form(0) )
		{
			<?php if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/inc_lang.php" ) ): ?>
			$('#pre_chat_form').hide() ;
			$('#pre_chat_lang').show() ;
			<?php else: ?>
			$('#theform').submit() ;
			<?php endif ; ?>
		}
	}

	function send_email()
	{
		if( check_form(1) )
		{
			var json_data = new Object ;
			var unique = unixtime() ;
			var deptid = $('#deptid').val() ;
			var vname = $('#vname').val() ;
			var vemail = $('#vemail').val() ;
			var vsubject = encodeURIComponent( $('#vsubject').val() ) ;
			var vquestion = encodeURIComponent( $('#vquestion').val() ) ;
			var onpage = encodeURIComponent( "<?php echo $onpage ?>" ).replace( /http/g, "hphp" ) ;
			var custom_fields = $('#custom_field_input').val() ;

			$('#chat_button_start').attr( "disabled", true ) ;
			$.post("./phplive_m.php", { action: "send_email", deptid: deptid, vname: vname, vemail: vemail, "custom_fields_[]": custom_fields, vsubject: vsubject, vquestion: vquestion, onpage: onpage, unique: unique },  function(data){
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
	}

//-->
</script>
</head>
<body style="display: none; overflow: hidden;">
<div id="chat_canvas" style="min-height: 100%; width: 100%;"></div>
<div style="position: absolute; top: 2px; padding: 10px; z-Index: 2;">
	<div id="chat_body" style="padding: 10px;">

		<div id="chat_logo" style="background: url( <?php echo Util_Upload_GetLogo( ".", $deptid ) ?> ) no-repeat;"></div>
		<div id="chat_text_header" style="margin-top: 15px;"><?php echo $LANG["CHAT_WELCOME"] ?></div>
		<div id="chat_text_header_sub" style="margin-top: 5px;"><?php echo $LANG["CHAT_WELCOME_SUBTEXT"] ?></div>

		<form method="POST" action="phplive_.php?submit&<?php echo time() ?>" id="theform" accept-charset="<?php echo $LANG["CHARSET"] ?>">
		<input type="hidden" name="deptid" id="deptid" value="<?php echo ( isset( $requestinfo["deptID"] ) ) ? $requestinfo["deptID"] : $deptid ; ?>">
		<input type="hidden" name="ces" value="<?php echo ( isset( $requestinfo["ces"] ) ) ? $requestinfo["ces"] : $ces ; ?>">
		<input type="hidden" name="onpage" value="<?php echo ( isset( $requestinfo["ces"] ) ) ? Util_Format_URL( $requestinfo["onpage"] ) : Util_Format_URL( $onpage ) ; ?>">
		<input type="hidden" name="title" value="<?php echo ( isset( $requestinfo["ces"] ) ) ? $requestinfo["title"] :$title ; ?>">
		<input type="hidden" name="win_dim" id="win_dim" value="">
		<input type="hidden" name="widget" value="<?php echo $widget ?>">
		<input type="hidden" name="theme" value="<?php echo $theme ?>">
		<input type="hidden" name="custom" value="<?php echo $custom ?>">
		<?php if ( $js_name || $js_email ): ?><input type="hidden" id="auto_pop" name="auto_pop" value="1"><?php endif ; ?>
		<?php if ( $js_name ): ?><input type="hidden" name="vname" value="<?php echo $vname ?>"><?php endif ; ?>
		<?php if ( $js_email ): ?><input type="hidden" name="vemail" value="<?php echo $vemail ?>"><?php endif ; ?>
		<div id="pre_chat_form" style="margin-top: 15px;">
			<table cellspacing=0 cellpadding=0 border=0>
			<tr>
				<td style="" colspan=2>
					<div id="div_vdeptids" style="display: none; padding-bottom: 10px;">
						<?php echo $LANG["TXT_DEPARTMENT"] ?><br>
						<select id="vdeptid" onChange="select_dept(this.value)"><option value=0><?php echo $LANG["CHAT_SELECT_DEPT"] ?></option>
						<?php
							$selected = "" ;
							for ( $c = 0; $c < count( $departments ); ++$c )
							{
								$department = $departments[$c] ;
								$class = "offline" ; $text = $LANG["TXT_OFFLINE"] ;
								if ( $dept_online[$department["deptID"]] ) { $class = "online" ; $text = $LANG["TXT_ONLINE"] ; }
								if ( count( $departments ) == 1 ) { $selected = "selected" ; }
								print "<option class=\"$class\" value=\"$department[deptID]\" $selected>$department[name] - $text</option>" ;
							}
						?>
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td style="padding-bottom: 10px;">
					<div style="margin-top: 5px;">
						<?php echo $LANG["TXT_NAME"] ?><br>
						<input type="input" class="input_text" id="vname" name="vname" maxlength="30" value="<?php echo isset( $requestinfo["vname"] ) ? preg_replace( "/<v>/", "", $requestinfo["vname"] ) : $vname ; ?>" onKeyPress="return noquotestags(event)" <?php echo ( $js_name ) ? "disabled" : "" ?>>
					</div>
				</td>
				<td style="padding-bottom: 10px;">
					<div style="margin-top: 5px; margin-left: 23px;">
						<?php echo $LANG["TXT_EMAIL"] ?><br>
						<input type="input" class="input_text" id="vemail" name="vemail" maxlength="160" value="<?php echo isset( $requestinfo["vemail"] ) ? $requestinfo["vemail"] : $vemail ; ?>" onKeyPress="return justemails(event)" <?php echo ( $js_email ) ? "disabled" : "" ?>>
					</div>
				</td>
			</tr>
			<tr>
				<td valign="top" style="padding-bottom: 10px;">
					<div id="div_customs" style="display: none;">
						<span id="custom_field_title"></span><br>
						<input type="input" class="input_text" id="custom_field_input" name="custom_fields_[]" maxlength="30" onKeyPress="return noquotestags(event)">
					</div>

					<div id="div_subject" style="display: none; margin-top: 10px;">
						<?php echo $LANG["TXT_SUBJECT"] ?><br>
						<input type="input" class="input_text" id="vsubject" name="vsubject" maxlength="30" onKeyPress="return noquotestags(event)">
					</div>

					<div style="margin-top: 10px;">
						<?php echo $LANG["TXT_QUESTION"] ?><br>
						<textarea class="input_text" id="vquestion" name="vquestion" rows="4" wrap="virtual" style="resize: vertical; resize: none;"><?php echo isset( $requestinfo["question"] ) ? $requestinfo["question"] : "" ; ?></textarea>
					</div>
				</td>
				<td style="padding-bottom: 10px;" valign="bottom">
					<div id="chat_btn" style="margin-top: 5px; margin-left: 23px;">
						<div style="">
							<button id="chat_button_start" class="input_button" type="button" style="<?php echo ( $mobile ) ? "" : "width: 150px; height: 45px; font-size: 14px; font-weight: bold;" ?> padding: 6px;"><?php echo $LANG["CHAT_BTN_START_CHAT"] ?></button>
						</div>
						<div id="chat_text_powered" style="margin-top: 15px; font-size: 10px;"><?php if ( isset( $CONF["KEY"] ) && ( $CONF["KEY"] == md5($KEY."-c615") ) ): ?><?php else: ?>&nbsp;<br><a href="http://www.phplivesupport.com/?plk=pi-5-ykq-m" target="_blank">PHP Live!</a> powered<?php endif ; ?></div>
					</div>
				</td>
			</tr>
		</table>
		</div>

		<div id="pre_chat_no_depts" style="display: none; margin-top: 10px;">
			<img src="./themes/<?php echo $theme ?>/alert.png" width="12" height="12" border="0" alt=""> There are no visible live chat departments available at this time.  Please try back later as this may be temporary.  If you are the live chat setup admin, enable at least one department to be visible for selection.
		</div>
		</form>

	</div>
</div>

<?php if ( $embed ): ?>
<div id="info_disconnect" class="info_disconnect" style="position: absolute; top: 0px; right: 0px; z-Index: 101;" onClick="disconnect()"><img src="./themes/<?php echo $theme ?>/close_extra.png" width="14" height="14" border="0" alt=""> <?php echo $LANG["CHAT_CLOSE"] ?></div>
<?php endif ; ?>

<?php if ( !$mobile ): ?>
<div id="chat_footer" style="position: relative; width: 100%; margin-top: -28px; height: 28px; padding-top: 7px; padding-left: 15px; z-Index: 10;"></div>
<?php endif ; ?>

<?php include_once( "./inc_flash.php" ) ; ?>

</body>
</html>
<?php
	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;
?>
