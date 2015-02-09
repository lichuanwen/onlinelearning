<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	/****************************************/
	// STANDARD header for Setup
	if ( !is_file( "../web/config.php" ) ){ HEADER("location: ../setup/install.php") ; exit ; }
	include_once( "../web/config.php" ) ;
	include_once( "../API/Util_Format.php" ) ;
	include_once( "../API/Util_Error.php" ) ;
	include_once( "../API/SQL.php" ) ;
	include_once( "../API/Util_Security.php" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	if ( !$opinfo = Util_Security_AuthOp( $dbh, $ses ) ){ ErrorHandler( 602, "Invalid operator session or session has expired.", $PHPLIVE_FULLURL, 0, Array() ) ; }
	// STANDARD header end
	/****************************************/

	include_once( "../API/Ops/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$console = Util_Format_Sanatize( Util_Format_GetVar( "console" ), "ln" ) ; $body_width = ( $console ) ? 800 : 900 ;
	$menu = Util_Format_Sanatize( Util_Format_GetVar( "menu" ), "ln" ) ;
	$menu = ( $menu ) ? $menu : "go" ;
	$error = "" ;

	$op_depts = Ops_get_OpDepts( $dbh, $opinfo["opID"] ) ;

	if ( $action == "update_password" )
	{
		$cpass = Util_Format_Sanatize( Util_Format_GetVar( "cpass" ), "ln" ) ;
		$npass = Util_Format_Sanatize( Util_Format_GetVar( "npass" ), "ln" ) ;
		$vnpass = Util_Format_Sanatize( Util_Format_GetVar( "vnpass" ), "ln" ) ;

		if ( md5( $cpass ) != $opinfo["password"] )
			$error = "Current password is invalid." ;
		else if ( $npass != $vnpass )
			$error = "Verify password does not match." ;
		else
		{
			include_once( "../API/Ops/update.php" ) ;

			Ops_update_OpValue( $dbh, $opinfo["opID"], "password", md5( $npass ) ) ;
		}

		$menu = "password" ;
	}
	else if ( $action == "update_theme" )
	{
		include_once( "../API/Ops/update.php" ) ;
		$theme = Util_Format_Sanatize( Util_Format_GetVar( "theme" ), "ln" ) ;

		if ( !Ops_update_OpValue( $dbh, $opinfo["opID"], "theme", $theme ) )
			$error = "Error in updating theme." ;
		else
			$opinfo["theme"] = $theme ;
		
		$menu = "themes" ;
	}
	else if ( $action == "update_sound" )
	{
		include_once( "../API/Ops/update.php" ) ;
		$sound1 = Util_Format_Sanatize( Util_Format_GetVar( "sound1" ), "ln" ) ;
		$sound2 = Util_Format_Sanatize( Util_Format_GetVar( "sound2" ), "ln" ) ;

		Ops_update_OpValue( $dbh, $opinfo["opID"], "sound1", $sound1 ) ;
		Ops_update_OpValue( $dbh, $opinfo["opID"], "sound2", $sound2 ) ;

		$opinfo["sound1"] = $sound1 ;
		$opinfo["sound2"] = $sound2 ;
		
		$menu = "sounds" ;
	}
	else if ( $action == "mreset" )
	{
		include_once( "../API/Ops/update.php" ) ;

		$now = time()-60 ;
		Ops_update_OpValue( $dbh, $opinfo["opID"], "sms", $now ) ;
		Ops_update_OpValue( $dbh, $opinfo["opID"], "smsnum", "" ) ;
		$opinfo["sms"] = $now ; $opinfo["smsnum"] = "" ;

		$action = "success" ;
	}
	else if ( $action == "success" )
	{
		// sucess action is an indicator to show the success alert as well
		// as bypass the reloading of the operator console
	}
	else
		$error = "invalid action" ;

	$smsnum = Array() ;
	$tok = strtok( base64_decode( $opinfo["smsnum"] ), '@' ) ;
	while ( $tok !== false ) { $smsnum[] = $tok ; $tok = strtok( '@' ) ; }
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> Operator </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8"> 
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<link rel="Stylesheet" href="../css/setup.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/global_chat.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/jquery.tools.min.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/dn.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var opwin ;
	var menu ;
	var theme = "<?php echo $opinfo["theme"] ?>" ;
	var base_url = ".." ; // needed for function play_sound()
	var focused = 1 ; // needed for function play_sound()
	var dn = dn_check() ; var dn_enabled = <?php echo $opinfo["dn"] ?> ;
	var smsnum ;
	var smsreset = 0 ;
	var st_sound ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		$('#op_launch_btn').live('mouseover mouseout', function(event) {
			$('#op_launch_btn').toggleClass('op_launch_btn_focus') ;
		});

		init_menu_op() ;
		toggle_menu_op( "<?php echo $menu ?>" ) ;

		if ( ( typeof( window.external ) != "undefined" ) && ( typeof( window.external.wp_save_history ) != "undefined" ) ) { $('#menu_dn').hide() ; }
		else { $('#menu_dn').show() ; }

		<?php if ( $action && !$error ): ?>do_alert( 1, "Success" ) ;<?php endif ; ?>
		<?php if ( ( $opinfo["sms"] == 1 ) || ( $opinfo["sms"] == 2 ) ): ?>do_sms_enabled( <?php echo $opinfo["sms"] ?> ) ; smsnum = "<?php echo $smsnum[0] ?>@<?php echo $smsnum[1] ?>" ; $('#sma_reset').show() ;<?php endif ; ?>
		<?php if ( $action && !$error && ( $action != "update_password" ) ): ?>
		
		if ( ( typeof( parent.isop ) != "undefined" ) && ( "<?php echo $action ?>" != "success" ) )
			parent.reload_console() ;
		<?php endif ; ?>

		var dn_browser = dn_check_browser() ;
		if ( dn_browser == "null" )
			$('#dn_unavailable').show() ;
		else
		{
			var dn = dn_check() ;

			if ( ( dn == -1 ) && ( dn_browser == "firefox" ) ) { $('#dn_firefox').show() ; }
			else
			{
				if ( !dn )
				{
					$('#dn_enabled').show() ;

					if ( dn_enabled ) { $('#dn_enabled_on').show() ; }
					else { $('#dn_enabled_off').show() ; }
				}
				else if ( dn == 2 ) { $('#dn_disabled').show() ; }
				else { $('#dn_request').show() ; }
			}
		}
	});

	function launchit()
	{
		var url = "operator.php?ses=<?php echo $ses ?>&auto=1&"+unixtime() ;

		if ( !<?php echo count( $op_depts ) ?> )
			$('#no_dept').show() ;
		else
		{
			if ( typeof( opwin ) == "undefined" )
				opwin = window.open( url, "<?php echo $ses ?>", "scrollbars=yes,menubar=no,resizable=1,location=no,width=940,height=580,status=0" ) ;
			else if ( opwin.closed )
				opwin = window.open( url, "<?php echo $ses ?>", "scrollbars=yes,menubar=no,resizable=1,location=no,width=940,height=580,status=0" ) ;

			if ( opwin )
				opwin.focus() ;
		}

		return true ;
	}

	function confirm_theme( thetheme )
	{
		if ( theme != thetheme )
		{
			if ( confirm( "Select theme "+thetheme+"?" ) )
				update_theme( thetheme ) ;
			else
				$('#theme_<?php echo $opinfo["theme"] ?>').attr('checked', true) ;
		}
	}

	function update_theme( thetheme )
	{
		location.href = 'index.php?console=<?php echo $console ?>&ses=<?php echo $ses ?>&action=update_theme&theme='+thetheme ;
	}

	function update_password()
	{
		if ( $('#cpass').val() == "" )
			do_alert( 0, "Please provide the current password." ) ;
		else if ( $('#npass').val() == "" )
			do_alert( 0, "Please provide the new password." ) ;
		else if ( $('#npass').val() != $('#vnpass').val() )
			do_alert( 0, "New password does not match." ) ;
		else
			$('#theform').submit() ;
	}

	function demo_sound1( theflag )
	{
		var sound = $('#sound1').val() ;
		var div_sound = $('#div_sounds_new_request').html() ;

		if ( theflag )
			play_sound('new_request', 'new_request_'+sound) ;
		else
			clear_sound('new_request') ;
	}

	function demo_sound2()
	{
		var sound = $('#sound2').val() ;
		var div_sound = $('#div_sounds_new_request').html() ;

		play_sound('new_request', 'new_text_'+sound) ;
	}

	function update_sound()
	{
		var sound1 = $('#sound1').val() ;
		var sound2 = $('#sound2').val() ;

		location.href = 'index.php?console=<?php echo $console ?>&ses=<?php echo $ses ?>&action=update_sound&sound1='+sound1+'&sound2='+sound2 ;
	}

	function sms_send()
	{
		var phonenum = $('#phonenum').val().replace( /[^0-9]/g, "" )  ; $('#phonenum').val( phonenum ) ;
		var carrier = encodeURIComponent( $('#carrier').val() ) ;

		$('#btn_sms_send').attr('disabled', true) ;

		if ( !parseInt( phonenum ) || !carrier )
		{
			$('#btn_sms_send').attr('disabled', false) ;
			do_alert( 0, "Blank input is invalid." ) ;
		}
		else if ( !check_email( phonenum+"@"+carrier ) )
		{
			$('#btn_sms_send').attr('disabled', false) ;
			do_alert( 0, "Carrier (gateway) format is invalid." ) ;
		}
		else if ( !smsreset && ( smsnum == phonenum+"@"+carrier ) )
		{
			$('#btn_sms_send').attr('disabled', false) ;
			do_alert( 1, "Your mobile has already been verified." ) ;
		}
		else
		{
			if ( !smsreset && smsnum && ( smsnum != phonenum+"@"+carrier ) )
			{
				if ( !confirm( "You are about to change your SMS information.  Are you sure?" ) )
				{
					$('#btn_sms_send').attr('disabled', false) ;
					$('#phonenum').val( "<?php echo isset( $smsnum[0] ) ? $smsnum[0] : "" ; ?>" ) ;
					$('#carrier').val( "<?php echo isset( $smsnum[1] ) ? $smsnum[1] : "" ; ?>" ) ;
					return false ;
				}
			}

			$.ajax({
				type: "POST",
				url: "../ajax/chat_actions_op_ext.php",
				data: "ses=<?php echo $ses ?>&action=sms_send&phonenum="+phonenum+"&carrier="+carrier+"&"+unixtime(),
				success: function(data){
					eval(data) ;

					setTimeout( function(){ $('#btn_sms_send').attr('disabled', false) ; }, 5000 ) ;
					if ( json_data.status )
					{
						smsreset = 1 ;
						smsnum = phonenum+"@"+carrier ;
						$('#sms_enabled').hide() ;
						do_alert( 1, "Verification code sent to "+smsnum ) ;
					}
					else
						do_alert( 0, json_data.error ) ;
				}
			});
		}
	}

	function sms_verify()
	{
		var code = $('#code').val().replace( /[^0-9]/g, "" )  ; $('#code').val( code ) ;

		$('#btn_sms_verify').attr('disabled', true) ;

		if ( !parseInt( code ) )
		{
			$('#btn_sms_verify').attr('disabled', false) ;
			do_alert( 0, "Blank verification code is invalid." ) ;
		}
		else
		{
			$.ajax({
				type: "POST",
				url: "../ajax/chat_actions_op_ext.php",
				data: "ses=<?php echo $ses ?>&action=sms_verify&code="+code+"&"+unixtime(),
				success: function(data){
					eval(data) ;

					setTimeout( function(){ $('#btn_sms_verify').attr('disabled', false) ; }, 5000 ) ;
					if ( json_data.status )
					{
						if ( typeof( json_data.error ) == "undefined" )
							location.href = "./index.php?action=success&menu=mobile&console=<?php echo $console ?>&ses=<?php echo $ses ?>" ;
						else
						{
							$('#code').val("") ;
							do_alert( 1, json_data.error ) ;
						}
					}
					else
						do_alert( 0, json_data.error ) ;
				}
			});
		}
	}

	function sms_toggle( theflag )
	{
		$.ajax({
			type: "POST",
			url: "../ajax/chat_actions_op_ext.php",
			data: "ses=<?php echo $ses ?>&action=sms_verify&code="+theflag+"&"+unixtime(),
			success: function(data){
				eval(data) ;

				if ( json_data.status )
				{
					if ( theflag == 1 ){ $('#sms_enabled_off').hide() ; $('#sms_enabled_on').show() ; }
					else { $('#sms_enabled_on').hide() ; $('#sms_enabled_off').show() ; }
				}
				else
					do_alert( 0, json_data.error ) ;
			}
		});
	}

	function reset_mobile()
	{
		if ( confirm( "You are about to clear your SMS information.  Are you sure?" ) )
			location.href = "./index.php?action=mreset&menu=mobile&console=<?php echo $console ?>&ses=<?php echo $ses ?>" ;
	}

	function do_sms_enabled( theflag )
	{
		if ( theflag == 1 ){ $('#sms_enabled_off').hide() ; $('#sms_enabled_on').show() ; }
		else { $('#sms_enabled_on').hide() ; $('#sms_enabled_off').show() ; }
		$('#code').val("") ;
		$('#sms_enabled').show() ;
		$('#sms_send').hide() ;
		$('#sma_reset').show() ;
	}

	function do_sms_edit()
	{
		if ( $('#sms_send').is(':visible') )
			$('#sms_send').hide() ;
		else
			$('#sms_send').show() ;
	}

	function dn_toggle( theflag )
	{
		$.ajax({
			type: "POST",
			url: "../ajax/chat_actions_op_ext.php",
			data: "ses=<?php echo $ses ?>&action=dn_toggle&dn="+theflag+"&"+unixtime(),
			success: function(data){
				eval(data) ;

				if ( json_data.status )
				{
					if ( theflag == 1 ){ $('#dn_enabled_off').hide() ; $('#dn_enabled_on').show() ; }
					else { $('#dn_enabled_on').hide() ; $('#dn_enabled_off').show() ; }

					if ( typeof( parent.dn_enabled ) != "undefined" )
						parent.dn_enabled = theflag ;
				}
				else
					do_alert( 0, "Error: Could not update DN value." ) ;
			}
		});
	}

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ); ?>

		<div id="op_go" style="margin: 0 auto;">
			<div id="no_dept" class="info_error" style="display: none;"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> Contact the setup admin to assign this account to a department.  Once assigned, refresh this page and try again.</div>

			<div id="op_launch_btn" style="margin-top: 25px; border: 1px solid #049BD8; padding: 10px; font-size: 18px; font-weight: bold; color: #FFFFFF; text-shadow: 1px 1px #049BD8; text-align: center; cursor: pointer;" onClick="launchit()" class="op_launch_btn round"><img src="../pics/icons/pointer.png" width="16" height="16" border="0" alt=""> Launch operator console to accept visitor chat requests.</div>
		</div>

		<div id="op_themes" style="display: none; margin: 0 auto;">
			<img src="../pics/icons/themes.png" width="16" height="16" border="0" alt=""> Click the theme name to preview the theme.  If the operator console is open, refresh the console window for the new theme to take affect.

			<form>
			<table cellspacing=0 cellpadding=2 border=0 width="100%" style="margin-top: 25px;">
			<tr>
				<td>
					<?php
						$deptid = ( count( $op_depts ) ) ? $op_depts[0]["deptID"] : 0 ;
						$dir_themes = opendir( "$CONF[DOCUMENT_ROOT]/themes/" ) ;

						$themes = Array() ;
						while ( $theme = readdir( $dir_themes ) )
							$themes[] = $theme ;
						closedir( $dir_themes ) ;

						for ( $c = 0; $c < count( $themes ); ++$c )
						{
							$theme = $themes[$c] ;

							$checked = "" ;
							if ( $opinfo["theme"] == $theme )
								$checked = "checked" ;

							if ( preg_match( "/[a-z]/i", $theme ) && ( $theme != "initiate" ) )
								print "<div class=\"li_op round\"><input type=\"radio\" name=\"theme\" id=\"theme_$theme\" value=\"$theme\" $checked onClick=\"confirm_theme('$theme')\"> <span style=\"cursor: pointer;\" onClick=\"preview_theme('$theme', $VARS_CHAT_WIDTH, $VARS_CHAT_HEIGHT, $deptid)\">$theme</span></div>" ;
						}
					?>
					<div style="clear: both;"></div>
				</td>
			</tr>
			</table>
			</form>
		</div>

		<div id="op_sounds" style="display: none; margin: 0 auto;">
			<img src="../pics/icons/bell_start.png" width="16" height="16" border="0" alt="play sound" title="play sound"> Customize the operator console sound alerts.  If the operator console is open, refresh the console window for the new theme to take affect.

			<form>
			<table cellspacing=0 cellpadding=2 border=0 style="margin-top: 25px;">
			<tr>
				<td class="td_dept_td">New chat request: </td>
				<td class="td_dept_td"><div style="margin-left: 15px;"><select name="sound1" id="sound1" style="width: 130px;" onChange="demo_sound1(1)">
					<?php
						$dir_sounds = opendir( "$CONF[DOCUMENT_ROOT]/media/" ) ;

						$sounds = Array() ;
						while ( $sound = readdir( $dir_sounds ) )
							$sounds[] = $sound ;
						closedir( $dir_sounds ) ;
						sort( $sounds ) ;

						for ( $c = 0; $c < count( $sounds ); ++$c )
						{
							$sound = $sounds[$c] ;

							if ( preg_match( "/[a-z]/i", $sound ) && preg_match( "/^new_request_/i", $sound ) )
							{
								$sound_temp = preg_replace( "/(new_request_)|(.swf)/", "", $sound ) ;
								$sound_display = ucwords( preg_replace( "/_/", " ", $sound_temp ) ) ;
								$selected = "" ;
								if ( $opinfo["sound1"] == $sound_temp )
									$selected = "selected" ;

								print "<option value=\"$sound_temp\" $selected>$sound_display</option>" ;
							}
						}
					?>
					</select></div>
				</td>
				<td class="td_dept_td"><div style="margin-left: 15px; cursor: pointer;" onClick="demo_sound1(1)"><img src="../pics/icons/bell_start.png" width="16" height="16" border="0" alt="play sound" title="play sound" id="img_sound1"></div></td>
				<td class="td_dept_td"><div style="margin-left: 15px; cursor: pointer;" onClick="demo_sound1(0)"><img src="../pics/icons/bell_stop.png" width="16" height="16" border="0" alt="stop sound" title="stop sound" id="img_sound1"></div></td>
			</tr>
			<tr>
				<td class="td_dept_td"><div style="padding-top: 5px;">New chat response: </div></td>
				<td class="td_dept_td"><div style="padding-top: 5px; margin-left: 15px; "><select name="sound2" id="sound2" style="width: 130px;" onChange="demo_sound2()">
					<?php
						$dir_sounds = opendir( "$CONF[DOCUMENT_ROOT]/media/" ) ;

						$sounds = Array() ;
						while ( $sound = readdir( $dir_sounds ) )
							$sounds[] = $sound ;
						closedir( $dir_sounds ) ;
						sort( $sounds ) ;

						for ( $c = 0; $c < count( $sounds ); ++$c )
						{
							$sound = $sounds[$c] ;

							if ( preg_match( "/[a-z]/i", $sound ) && preg_match( "/^new_text_/i", $sound ) )
							{
								$sound_temp = preg_replace( "/(new_text_)|(.swf)/", "", $sound ) ;
								$sound_display = ucwords( preg_replace( "/_/", " ", $sound_temp ) ) ;
								$selected = "" ;
								if ( $opinfo["sound2"] == $sound_temp )
									$selected = "selected" ;

								print "<option value=\"$sound_temp\" $selected>$sound_display</option>" ;
							}
						}
					?>
					</select></div>
				</td>
				<td class="td_dept_td"><div style="margin-left: 15px; cursor: pointer;" onClick="demo_sound2()"><img src="../pics/icons/bell_start.png" width="16" height="16" border="0" alt="play sound" title="play sound" id="img_sound2"></div></td>
			</tr>
			<tr>
				<td></td>
				<td colspan=2 class="td_dept_td"><div style="margin-left: 15px; padding-top: 15px;"><input type="button" value="Update Sound Alerts" onClick="update_sound()" class="btn"></div></td>
			</tr>
			</table>
			</form>
		</div>

		<div id="op_mobile" style="display: none; margin: 0 auto;">
			<img src="../pics/icons/mobile.png" width="16" height="16" border="0" alt=""> Receive new chat request SMS alerts to your mobile. This is a notification only. The operator console should remain opened on the computer.

			<form>
			<table cellspacing=0 cellpadding=2 border=0 width="100%" style="margin-top: 25px;">
			<tr>
				<td>
					<div id="sms_enabled" style="display: none; margin-bottom: 25px;">
						<div class="info_good edit_title" id="sms_enabled_on" style="display: none; text-align: center;">SMS notification alert is on.  <button type="button" onClick="sms_toggle(2)" class="btn">Switch Off</button></div>
						<div class="info_error edit_title" id="sms_enabled_off" style="display: none; text-align: center;">SMS notification alert is off.  <button type="button" onClick="sms_toggle(1)" class="btn">Switch On</button></div>
						<div style="margin-top: 15px;"><img src="../pics/icons/arrow_grey.png" width="16" height="16" border="0" alt=""> <a href="JavaScript:void(0)" onClick="do_sms_edit()">Your SMS information</a></div>
					</div>
					<div id="sms_send">
						<table cellspacing=0 cellpadding=0 border=0 width="100%">
						<tr>
							<td width="60%" valign="top">
								<div style="font-size: 14px; font-weight: bold;">Step 1.</div>
								<div style="margin-top: 5px;">The format is <span style="font-style: italic;">mobile_number@SMS_gateway</span> (example: 1234567890@txt.att.net)</div>
								<div style="margin-top: 15px;">
									<table cellspacing=0 cellpadding=2 border=0 width="100%">
									<tr>
										<td><span style="font-size: 10px;">mobile # with area code</span><br><input type="text" class="input" size="15" maxlength="25" id="phonenum" name="phonenum" onKeyPress="return numbersonly(event)" value="<?php echo isset( $smsnum[0] ) ? $smsnum[0] : "" ?>"></td>
										<td><span style="font-size: 10px; color: #F0F0F2;">@</span><br>@</td>
										<td nowrap><span style="font-size: 10px;">carrier (SMS gateway)</span><br><input type="text" class="input" size="15" maxlength="40" id="carrier" id="carrier" value="<?php echo isset( $smsnum[1] ) ? $smsnum[1] : "" ?>"> &nbsp; <input type="button" value="Send Verification" onClick="sms_send()" id="btn_sms_send" class="btn"></td>
									</tr>
									<tr>
										<td colspan=3>
											<div style="margin-top: 15px;">Verification of your mobile devide is required.  There will be a 1 minute delay between each verification attempts.  If you are unsure of the carrier format, visit the <a href="http://en.wikipedia.org/wiki/List_of_SMS_gateways" target="newer">list of SMS gateways</a> for more information.</div>
											<div style="margin-top: 15px;">If you do not receive the verification code within 5-10 minutes, double check the mobile number and the carrier values for accuracy . <span id="sma_reset" style="display: none; text-decoration: underline; cursor: pointer;" onClick="reset_mobile()">To clear and reset the SMS information, click here.</span></div>
										</td>
									</tr>
									</table>
								</div>
							</td>
							<td width="5%">&nbsp;</td>
							<td width="35%" valign="top">
								<div style="font-size: 14px; font-weight: bold;">Step 2.</div>
								<div style="margin-top: 5px;">Provide the verification code to activate.</div>
								<div style="margin-top: 15px;">
									<table cellspacing=0 cellpadding=2 border=0>
									<tr>
										<td><span style="font-size: 10px;">verification code</span><br><input type="text" class="input" size="11" maxlength="15" id="code" id="code" onKeyPress="return logins(event)"> <input type="button" value="Verify" onClick="sms_verify()" id="btn_sms_verify" class="btn"></td>
									</tr>
									</table>
								</div>
							</td>
						</tr>
						</table>
					</div>
					<div id="sms_verify" style="display: none;">
					</div>
				</td>
			</tr>
			</table>
			</form>
		</div>

		<div id="op_dn" style="display: none; margin: 0 auto;">
			<img src="../pics/icons/comp.png" width="16" height="16" border="0" alt=""> Display a new chat request notification alert box on the desktop.  Desktop notification is currently available for <a href="https://www.google.com/chrome" target="new">Google Chrome</a> and <a href="http://www.firefox.com" target="new">Firefox</a>.

			<form>
			<table cellspacing=0 cellpadding=2 border=0 width="100%" style="margin-top: 15px;">
			<tr>
				<td>
					<div id="dn_unavailable" style="display: none;">Desktop Notification is not supported for this browser type.  Consider using Google Chrome of Firefox browser if you'd like to utilize the new chat request Desktop Notification popup alert.</div>
					<div id="dn_request" style="display: none;">
						<div class="info_box"><img src="../pics/icons/check.png" width="16" height="16" border="0" alt=""> Good news!  Desktop notification is support for this browser.</div>
						
						<div style="margin-top: 15px;">To enable the new chat request desktop notification, click on the "Request Notification" button.  When alerted to "Allow" or "Deny" the request, click the "Allow" option to enable.</div>

						<div style="margin-top: 15px;"><input type="button" onClick="dn_pre_request()" value="Request Notification" class="btn"></div>
					</div>
					<div id="dn_firefox" style="display: none;">
						<div class="info_box"><img src="../pics/icons/check.png" width="16" height="16" border="0" alt=""> Good news!  Desktop notification is support for this browser.</div>

						<div style="margin-top: 15px;">However, the system has detected a Firefox browser and you'll want to install the <a href="https://addons.mozilla.org/en-US/firefox/addon/html-notifications/" target="new">Firefox Desktop Notification Addon</a> .  Once the addon has been installed, restart the Firefox browser and access this area again to continue with the process.  If the Firefox addon is already installed, you'll want to enable the addon.</div>
					</div>
					<div id="dn_enabled" style="display: none;">
						<div class="info_good edit_title" id="dn_enabled_on" style="display: none; text-align: center;">Desktop Notification alert is on. <button type="button" onClick="dn_toggle(0)" class="btn">Switch Off</button></div>

						<div class="info_error edit_title" id="dn_enabled_off" style="display: none; text-align: center;">Desktop Notification alert is off.  <button type="button" onClick="dn_toggle(1)" class="btn">Switch On</button></div>

						<div style="margin-top: 25px;">With each new chat request, a popup notification box will appear on your desktop.  <a href="JavaScript:void(0)" onClick="dn_show( 1, '<?php echo time() ?>', 'Demo Visitor', 'This is a demo question.' )">Click for demo notification.</a></div>
					</div>
					<div id="dn_disabled" style="display: none;">
						<span class="info_error">Desktop notification request was denied.</span>

						<div style="margin-top: 25px;">To re-request the desktop notification access, you'll want to refer to the <a href="http://www.phplivesupport.com/help_desk.php?title=Reset_Desktop_Notification_Settings&docid=18" target="new">Reset Desktop Notification Settings</a> documentation.  After the settings have been reset, reload this page to re-request permission.</div>
					</div>
				</td>
			</tr>
			</table>
			</form>
		</div>

		<div id="op_password" style="display: none; margin: 0 auto;">
			<?php if ( $action && $error ): ?>
				<div id="div_error" class="info_error" style="margin-bottom: 10px;"><img src="../pics/icons/alert.png" width="12" height="12" border="0" alt=""> <?php echo $error ?></div>
			<?php endif; ?>

			<form method="POST" action="index.php?submit" id="theform">
			<input type="hidden" name="action" value="update_password">
			<input type="hidden" name="console" value="<?php echo $console ?>">
			<input type="hidden" name="ses" value="<?php echo $ses ?>">
			<div style="">
				<img src="../pics/icons/key.png" width="16" height="16" border="0" alt=""> Update your account password.
				
				<div style="margin-top: 15px;">Current Password</div>
				<input type="password" class="input" name="cpass" id="cpass" size="30" maxlength="15" value="" onKeyPress="return nospecials(event)">
				
				<div style="margin-top: 15px;">New Password</div>
				<input type="password" class="input" name="npass" id="npass" size="30" maxlength="15" value="" onKeyPress="return nospecials(event)"><div style="font-size: 10px;">* letters and numbers</div>
				
				<div style="margin-top: 15px;">Verify New Password</div>
				<input type="password" class="input" name="vnpass" id="vnpass" size="30" maxlength="15" value="" onKeyPress="return nospecials(event)"><div style="font-size: 10px;">* letters and numbers</div>
				
				<div style="margin-top: 10px;"><input type="button" value="Update" onClick="update_password()" class="btn"></div>
			</div>
			</form>

		</div>

		<?php if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/inc_lang.php" ) ): ?>
		<div id="op_language" style="display: none; margin: 0 auto;">
			<iframe src="../addons/inc_lang.php" style="width:100%; height: 120px; border: 0px;" scrolling="no" frameBorder="0"></iframe>
		</div>
		<?php endif ; ?>

<?php include_once( "./inc_footer.php" ); ?>
