<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	/****************************************/
	// STANDARD header for Setup
	if ( !is_file( "../web/config.php" ) ){ HEADER("location: install.php") ; exit ; }
	include_once( "../web/config.php" ) ;
	include_once( "../API/Util_Format.php" ) ;
	include_once( "../API/Util_IP.php" ) ;
	include_once( "../API/Util_Error.php" ) ;
	include_once( "../API/SQL.php" ) ;
	include_once( "../API/Util_Security.php" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	if ( !$admininfo = Util_Security_AuthSetup( $dbh, $ses ) ){ ErrorHandler( 608, "Invalid setup session or session has expired.", $PHPLIVE_FULLURL, 0, Array() ) ; }
	// STANDARD header end
	/****************************************/

	include_once( "../API/Util_Vals.php" ) ;
	include_once( "../API/Util_Hash.php" ) ;
	include_once( "../API/Util_Upload.php" ) ;
	include_once( "../API/Depts/get.php" ) ;

	$https = "" ;
	if ( isset( $_SERVER["HTTP_CF_VISITOR"] ) && preg_match( "/(https)/i", $_SERVER["HTTP_CF_VISITOR"] ) ) { $https = "s" ; }
	else if ( isset( $_SERVER["HTTP_X_FORWARDED_PROTO"] ) && preg_match( "/(https)/i", $_SERVER["HTTP_X_FORWARDED_PROTO"] ) ) { $https = "s" ; }
	else if ( isset( $_SERVER["HTTPS"] ) && preg_match( "/(on)/i", $_SERVER["HTTPS"] ) ) { $https = "s" ; }

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$jump = ( Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) ) ? Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) : "logo" ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "ln" ) ;

	if ( !isset( $CONF["cookie"] ) )
		$CONF["cookie"] = "on" ;
	if ( !isset( $CONF["screen"] ) )
		$CONF["screen"] = "same" ;
	if ( !isset( $CONF["THEME"] ) )
		$CONF["THEME"] = "default" ;

	$error = "" ;

	$deptinfo = Array() ;
	$upload_dir = "$CONF[DOCUMENT_ROOT]/web" ;

	if ( $action == "update" )
	{
		if ( $jump == "logo" )
			$error = Util_Upload_File( "logo", $deptid ) ;
		else if ( $jump == "themes" )
		{
			$theme = Util_Format_Sanatize( Util_Format_GetVar( "theme" ), "ln" ) ;

			$error = ( Util_Vals_WriteToConfFile( "THEME", $theme ) ) ? "" : "Could not write to config file." ;
			$CONF["THEME"] = $theme ;
		}
		else if ( $jump == "time" )
		{
			include_once( "../API/Chat/remove.php" ) ;

			$timezone = Util_Format_Sanatize( Util_Format_GetVar( "timezone" ), "ln" ) ;

			if ( $timezone != $CONF["TIMEZONE"] )
				Chat_remove_ResetReports( $dbh ) ;

			$error = ( Util_Vals_WriteToConfFile( "TIMEZONE", $timezone ) ) ? "" : "Could not write to config file." ;
			if ( phpversion() >= "5.1.0" ){ date_default_timezone_set( $timezone ) ; }
		}
	}
	else if ( $action == "screen" )
	{
		$screen = Util_Format_Sanatize( Util_Format_GetVar( "screen" ), "ln" ) ;
		$error = ( Util_Vals_WriteToConfFile( "screen", $screen ) ) ? "" : "Could not write to config file." ;
		$CONF["screen"] = $screen ;

		$jump = "screen" ;
	}

	$cookie_off = ( $CONF["cookie"] == "off" ) ? "checked" : "" ;
	$cookie_on = ( $cookie_off == "checked" ) ? "" : "checked" ;

	$screen_same = ( $CONF["screen"] == "same" ) ? "checked" : "" ;
	$screen_separate = ( $screen_same == "checked" ) ? "" : "checked" ;

	// auto write conf file if variables do not exist
	if ( !isset( $CONF["API_KEY"] ) )
	{
		$CONF["API_KEY"] = Util_Format_RandomString( 10 ) ;
		$error = ( Util_Vals_WriteToConfFile( "API_KEY", $CONF["API_KEY"] ) ) ? "" : "Could not write to config file." ;
	}

	$departments = Depts_get_AllDepts( $dbh ) ;
	$timezones = Util_Hash_Timezones() ;
	$vars = Util_Format_Get_Vars( $dbh ) ;
	$charset = ( isset( $vars["char_set"] ) && $vars["char_set"] ) ? unserialize( $vars["char_set"] ) : Array(0=>"UTF-8") ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> PHP Live! Support <?php echo $VERSION ?> </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8"> 

<link rel="Stylesheet" href="../css/setup.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/tooltip.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var theme = "<?php echo $CONF["THEME"] ?>" ;
	var global_cookie = "<?php echo $CONF["cookie"] ?>" ;
	var global_charset = "<?php echo $charset[0] ?>" ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "settings" ) ;

		fetch_eips() ;
		fetch_sips() ;
		show_div( "<?php echo $jump ?>" ) ;

		<?php if ( $action && !$error ): ?>do_alert( 1, "Success" ) ;<?php endif ; ?>
		<?php if ( $action && $error ): ?>do_alert( 0, "<?php echo $error ?>" ) ;<?php endif ; ?>

		$('#urls_<?php echo $CONF["screen"] ?>').show() ;

		check_logo_dim() ;
	});

	function fetch_eips()
	{
		$.ajax({
			type: "POST",
			url: "../ajax/setup_actions.php",
			data: "ses=<?php echo $ses ?>&action=eips&"+unixtime(),
			success: function(data){
				print_eips( data ) ;
			}
		});
	}

	function fetch_sips()
	{
		$.ajax({
			type: "POST",
			url: "../ajax/setup_actions.php",
			data: "ses=<?php echo $ses ?>&action=sips&"+unixtime(),
			success: function(data){
				print_sips( data ) ;
			}
		});
	}

	function print_eips( thedata )
	{
		eval( thedata ) ;

		if ( json_data.ips != undefined )
		{
			var ip_string = "<table cellspacing=0 cellpadding=0 border=0 width=\"100%\">" ;
			for ( c = 0; c < json_data.ips.length; ++c )
			{
				var ip = json_data.ips[c]["ip"] ;
				var ip_ = ip.replace( /\./g, "" ) ;

				ip_string += "<tr><td class=\"td_dept_td\" width=\"14\"><div id=\"eip_"+ip_+"\"><a href=\"JavaScript:void(0)\" onClick=\"remove_eip( '"+ip+"' )\"><img src=\"../pics/icons/delete.png\" width=\"14\" height=\"14\" border=\"0\" alt=\"\"></a></div></td><td class=\"td_dept_td\">"+ip+"</td></tr>" ;
			}
			if ( !c )
				ip_string += "<tr><td class=\"td_dept_td\">blank results</td></tr>" ;
		}
		ip_string += "</table>" ;
		$('#eips').html( ip_string ) ;
	}

	function print_sips( thedata )
	{
		eval( thedata ) ;

		if ( json_data.ips != undefined )
		{
			var ip_string = "<table cellspacing=0 cellpadding=0 border=0 width=\"100%\">" ;
			for ( c = 0; c < json_data.ips.length; ++c )
			{
				var ip = json_data.ips[c]["ip"] ;
				var ip_ = ip.replace( /\./g, "" ) ;

				ip_string += "<tr><td class=\"td_dept_td\" width=\"14\"><div id=\"sip_"+ip_+"\"><a href=\"JavaScript:void(0)\" onClick=\"remove_sip( '"+ip+"' )\"><img src=\"../pics/icons/delete.png\" width=\"14\" height=\"14\" border=\"0\" alt=\"\"></a></div></td><td class=\"td_dept_td\">"+ip+"</td></tr>" ;
			}
			if ( !c )
				ip_string += "<tr><td class=\"td_dept_td\">blank results</td></tr>" ;
		}
		ip_string += "</table>" ;
		$('#sips').html( ip_string ) ;
	}

	function add_eip()
	{
		var ip = $('#ip_exclude').val().replace( /[^0-9.]/g, "" ) ;
		$('#ip_exclude').val( ip ) ;

		if ( !ip )
			do_alert( 0, "Blank IP field is invalid." ) ;
		else
		{
			$.ajax({
				type: "POST",
				url: "../ajax/setup_actions.php",
				data: "ses=<?php echo $ses ?>&action=add_eip&ip="+ip+"&"+unixtime(),
				success: function(data){
					eval(data) ;
					if ( json_data.status )
						fetch_eips() ;
					else
						do_alert( 0, "IP ("+ip+") already excluded." ) ;

					$('#ip_exclude').val('') ;
				}
			});
		}
	}

	function remove_eip( theip )
	{
		var theip_ = theip.replace( /\./g, "" ) ;
		$('#eip_'+theip_).html( "<img src=\"../pics/loading_ci.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"\">" ) ;

		$.ajax({
			type: "POST",
			url: "../ajax/setup_actions.php",
			data: "ses=<?php echo $ses ?>&action=remove_eip&ip="+theip+"&"+unixtime(),
			success: function(data){
				print_eips( data ) ;
			}
		});
	}

	function add_sip()
	{
		var ip = $('#ip_spam').val().replace( /[^0-9.]/g, "" ) ;
		$('#ip_spam').val( ip ) ;

		if ( !ip )
			do_alert( 0, "Blank IP field is invalid." ) ;
		else
		{
			$.ajax({
				type: "POST",
				url: "../ajax/setup_actions.php",
				data: "ses=<?php echo $ses ?>&action=add_sip&ip="+ip+"&"+unixtime(),
				success: function(data){
					eval(data) ;
					if ( json_data.status )
						fetch_sips() ;
					else
						do_alert( 0, "IP ("+ip+") already reported as spam." ) ;

					$('#ip_spam').val('') ;
				}
			});
		}
	}

	function remove_sip( theip )
	{
		var theip_ = theip.replace( /\./g, "" ) ;
		$('#sip_'+theip_).html( "<img src=\"../pics/loading_ci.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"\">" ) ;

		$.ajax({
			type: "POST",
			url: "../ajax/setup_actions.php",
			data: "ses=<?php echo $ses ?>&action=remove_sip&ip="+theip+"&"+unixtime(),
			success: function(data){
				print_sips( data ) ;
			}
		});
	}

	function show_div( thediv )
	{
		var divs = Array( "logo", "themes", "charset", "time", "eips", "sips", "cookie", "screen", "profile" ) ;
		for ( c = 0; c < divs.length; ++c )
		{
			$('#settings_'+divs[c]).hide() ;
			$('#menu_'+divs[c]).removeClass('op_submenu_focus').addClass('op_submenu') ;
		}

		$('input#jump').val( thediv ) ;
		$('#settings_'+thediv).show() ;
		$('#menu_'+thediv).removeClass('op_submenu').addClass('op_submenu_focus') ;
	}

	function confirm_theme( thetheme )
	{
		if ( confirm( "Select theme "+thetheme+"?" ) )
			update_theme( thetheme ) ;
		else
			$('#theme_<?php echo $CONF["THEME"] ?>').attr('checked', true) ;
	}

	function update_theme( thetheme )
	{
		location.href = 'settings.php?ses=<?php echo $ses ?>&action=update&jump=themes&theme='+thetheme ;
	}

	function switch_dept( theobject )
	{
		location.href = "settings.php?ses=<?php echo $ses ?>&deptid="+theobject.value ;
	}

	function update_timezone()
	{
		var timezone = $('#timezone').val() ;

		if ( confirm( "This action will reset the chat reports data.  Are you sure?" ) )
			location.href = "settings.php?ses=<?php echo $ses ?>&action=update&jump=time&timezone="+timezone ;
	}

	function update_theme( thetheme )
	{
		location.href = 'settings.php?ses=<?php echo $ses ?>&action=update&jump=themes&theme='+thetheme ;
	}

	function update_profile()
	{
		execute = 1 ;
		var inputs = Array( "email", "login" ) ;

		for ( c = 0; c < inputs.length; ++c )
		{
			if ( $('#'+inputs[c]).val() == "" ){ $('#status_'+inputs[c]).empty().html( "&nbsp; <img src=\"../pics/icons/alert.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"\" class=\"help_tooltip\" title=\"- Please provide a value.\">" ) ; execute = 0 ; }
		}

		// check email
		if ( !check_email( $('#email').val() ) ){ do_alert( 0, "Email format is invalid. (example: you@domain.com)" ) ; execute = 0 ; }

		// check new passwords
		if ( $('#npassword').val() || $('#vpassword').val() )
		{
			if ( $('#npassword').val() != $('#vpassword').val() ){ do_alert( 0, "New Password and Verify Password does not match." ) ; execute = 0 ; }
		}

		init_tooltips() ;
		if ( execute ){ update_profile_doit() ; } ;
	}

	function update_profile_doit()
	{
		var json_data = new Object ;
		var unique = unixtime() ;

		var email = $('#email').val() ;
		var login = $('#login').val() ;
		var npassword = $('#npassword').val() ;
		var vpassword = $('#vpassword').val() ;

		$.ajax({
		type: "POST",
		url: "../ajax/setup_actions.php",
		data: "action=update_profile&ses=<?php echo $ses ?>&email="+email+"&login="+login+"&npassword="+npassword+"&vpassword="+vpassword+"&"+unique,
		success: function(data){
			eval( data ) ;
			if ( json_data.status )
			{
				$('#npassword').val('') ;
				$('#vpassword').val('') ;
				do_alert( 1, "Success" ) ;
			}
			else
				do_alert( 0, json_data.error ) ;

		},
		error:function (xhr, ajaxOptions, thrownError){
			do_alert( 0, "Connection to server was lost.  Please reload the page." ) ;
		} });
	}

	function confirm_change( theflag )
	{
		if ( global_cookie != theflag )
		{
			if ( confirm( "Update visitor cookies to "+theflag+"?" ) )
			{
				$.ajax({
					type: "POST",
					url: "../ajax/setup_actions.php",
					data: "ses=<?php echo $ses ?>&action=update_cookie&value="+theflag+"&"+unixtime(),
					success: function(data){
						global_cookie = theflag ;
						do_alert( 1, "Success!" ) ;
					}
				});
			}
			else
				$('#cookie_'+global_cookie).attr('checked', true) ;
		}
	}

	function confirm_charset( thecharset )
	{
		if ( global_charset != thecharset )
		{
			if ( confirm( "Update operator console charset to "+thecharset+"?" ) )
			{
				$.ajax({
					type: "POST",
					url: "../ajax/setup_actions.php",
					data: "ses=<?php echo $ses ?>&action=update_vars&varname=char_set&value="+thecharset+"&"+unixtime(),
					success: function(data){
						global_charset = thecharset ;
						do_alert( 1, "Success!" ) ;
					}
				});
			}
			else
				$('#charset_'+global_charset).attr('checked', true) ;
		}
	}

	function check_logo_dim()
	{
		var img = new Image() ;
		img.onload = get_img_dim ;
		img.src = '<?php print Util_Upload_GetLogo( "..", $deptid ) ?>' ;
	}

	function get_img_dim()
	{
		var img_width = this.width ;
		var img_height = this.height ;

		$('#div_logo').css({'width': img_width, 'height': img_height}) ;
	}

	function init_tooltips()
	{
		var help_tooltips = $('body').find( '.help_tooltip' ) ;
		help_tooltips.tooltip({
			event: "mouseover",
			track: true,
			delay: 0,
			showURL: false,
			showBody: "- ",
			fade: 0,
			extraClass: "stat"
		});
	}

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu" onClick="show_div('logo')" id="menu_logo">Upload Logo</div>
			<div class="op_submenu" onClick="show_div('themes')" id="menu_themes">Themes</div>
			<div class="op_submenu" onClick="show_div('charset')" id="menu_charset">Character Set</div>
			<?php if ( phpversion() >= "5.1.0" ): ?><div class="op_submenu" onClick="show_div('time')" id="menu_time">Time Zone</div><?php endif; ?>
			<div class="op_submenu" onClick="show_div('eips')" id="menu_eips">Excluded IPs</div>
			<div class="op_submenu" onClick="show_div('sips')" id="menu_sips">Spam IPs</div>
			<div class="op_submenu" onClick="show_div('cookie')" id="menu_cookie">Cookies</div>
			<div class="op_submenu" onClick="show_div('screen')" id="menu_screen">Login Screen</div>
			<div class="op_submenu" onClick="show_div('profile')" id="menu_profile"><img src="../pics/icons/key.png" width="12" height="12" border="0" alt=""> Setup Profile</div>
			<div class="op_submenu" onClick="location.href='db.php?ses=<?php echo $ses ?>'" id="menu_system">System</div>
			<div style="clear: both"></div>
		</div>

		<form method="POST" action="settings.php?submit" enctype="multipart/form-data">
		<input type="hidden" name="action" value="update">
		<input type="hidden" name="jump" id="jump" value="">
		<input type="hidden" name="ses" value="<?php echo $ses ?>">

		<div style="display: none; margin-top: 25px;" id="settings_logo">
			<?php if ( count( $departments ) > 1 ): ?>
			<div style="">
				<select name="deptid" id="deptid" style="font-size: 16px; background: #D4FFD4; color: #009000;" OnChange="switch_dept( this )">
					<option value="0">Global Default</option>
					<?php
						for ( $c = 0; $c < count( $departments ); ++$c )
						{
							$department = $departments[$c] ;
							if ( $department["name"] != "Archive" )
							{
								$selected = ( $deptid == $department["deptID"] ) ? "selected" : "" ;
								print "<option value=\"$department[deptID]\" $selected>$department[name]</option>" ;
							}
						}
					?>
				</select>
			</div>
			<div style="margin-top: 15px;">"Global Default" logo will be displayed until a new logo has been uploaded for that department.  <b>NOTE:</b> The department logo will take affect for the <a href="code.php?ses=<?php echo $ses ?>">Department Specific HTML Code</a> option only.   Otherwise, the "Global Default" logo will be auto selected for the visitor chat request window.</div>
			<?php else: ?>
			<input type="hidden" name="deptid" id="deptid" value="0">
			<?php endif ; ?>

			<table cellspacing=0 cellpadding=0 border=0 width="100%" class="edit_wrapper" style="margin-top: 25px;">
			<tr>
				<td valign="top">
					<?php if ( isset( $deptinfo["deptID"] ) && !$deptinfo["visible"] ): ?>
					<span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> <?php echo $deptinfo["name"] ?> Department is <a href="depts.php?ses=<?php echo $ses ?>">not visible</a> to the public.  Department Logo not available.</span>

					<?php else: ?>

						<?php if ( ( count( $departments ) == 1 ) && isset( $deptinfo["deptID"] ) ): ?>
						<span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> Because only one department is available, choose the "Global Default" to upload your logo.</span>

						<?php else: ?>
						<div class="edit_title"><?php echo ( isset( $deptinfo["name"] ) ) ? $deptinfo["name"] : "Global Default" ; ?> LOGO</div>
						<div style="margin-top: 10px;">
							<input type="file" name="logo" size="30"><p>
							<input type="submit" value="Upload Image" style="margin-top: 10px;" class="btn">
						</div>

						<div style="margin-top: 15px;"><img src="../pics/icons/info.png" width="12" height="12" border="0" alt=""> Recommended maximum logo size is 580 pixels width and 55 pixels height.</div>
						<div id="div_logo" style="border: 1px solid #DFDFDF; margin-top: 25px; background: url( <?php print Util_Upload_GetLogo( "..", $deptid ) ?> ) no-repeat;">&nbsp;</div>

						<div style="margin-top: 15px;"><img src="../pics/icons/bullet.png" width="16" height="16" border="0" alt=""> <a href="JavaScript:void(0)" onClick="preview_theme('<?php echo $CONF["THEME"] ?>', <?php echo $VARS_CHAT_WIDTH ?>, <?php echo $VARS_CHAT_HEIGHT ?>, <?php echo $deptid ?> )">view how it looks</a></div>
						<?php endif ; ?>

					<?php endif; ?>
				</td>
			</tr>
			</table>
		</div>

		<div style="display: none; margin-top: 25px; text-align: justify;" id="settings_themes">
			<div>Click on the theme name to preview the theme, select the radio button to choose.  Keep in mind, this portion of theme selection only affects the visitor chat window.  Operators will be able to set their own themes by logging into the operator area.</div>

			<table cellspacing=0 cellpadding=2 border=0 width="100%" style="margin-top: 25px;">
			<tr>
				<td>
					<?php
						$dir_themes = opendir( "$CONF[DOCUMENT_ROOT]/themes/" ) ;

						$themes = Array() ;
						while ( $this_theme = readdir( $dir_themes ) )
							$themes[] = $this_theme ;
						closedir( $dir_themes ) ;

						for ( $c = 0; $c < count( $themes ); ++$c )
						{
							$this_theme = $themes[$c] ;

							$checked = "" ;
							if ( $CONF["THEME"] == $this_theme )
								$checked = "checked" ;

							if ( preg_match( "/[a-z]/i", $this_theme ) && ( $this_theme != "initiate" ) )
								print "<div class=\"li_op round\"><input type=\"radio\" name=\"theme\" id=\"theme_$this_theme\" value=\"$this_theme\" $checked onClick=\"confirm_theme('$this_theme')\"> <span style=\"cursor: pointer;\" onClick=\"preview_theme('$this_theme', $VARS_CHAT_WIDTH, $VARS_CHAT_HEIGHT, 0 )\">$this_theme</span></div>" ;
						}
					?>
					<div style="clear: both;"></div>
				</td>
			</tr>
			</table>
		</div>

		<div style="display: none; margin-top: 25px;" id="settings_charset">
			If multi-language characters are not rendering properly on the operator chat window or while viewing transcripts, try updating the character set value.  UTF-8 is suggested.

			<div style="margin-top: 25px;">
				<div class="li_op round"><input type="radio" name="charset" id="charset_UTF-8" value="UTF-8" onClick="confirm_charset(this.value)" <?php echo ( $charset[0] == "UTF-8" ) ? "checked" : "" ?>> UTF-8</div>
				<div class="li_op round"><input type="radio" name="charset" id="charset_ISO-8859-1" value="ISO-8859-1" onClick="confirm_charset(this.value)" <?php echo ( $charset[0] == "ISO-8859-1" ) ? "checked" : "" ?>> ISO-8859-1</div>
				<div style="clear: both;"></div>
			</div>
		</div>

		<?php if ( phpversion() >= "5.1.0" ): ?>
		<div style="display: none; margin-top: 25px;" id="settings_time">
			<div style="font-size: 32px; font-weight: bold; color: #79C2EB; font-family: sans-serif;"><?php echo date( "M j, Y (g:i:s a)", time() ) ; ?></div>

			<div style="margin-top: 15px;">Updating the timezone will clear the <a href="reports_chat.php?ses=<?php echo $ses ?>">chat reports data</a>.  The chat report reset is done because the past data timezone will conflict with the new timezone.  Be sure to print out the report as backup before continuing.  The chat <a href="transcripts.php?ses=<?php echo $ses ?>">transcripts</a> will not be affected but the creation timestamp may be different from original due to the timezone change.</div>

			<div style="margin-top: 15px;">
				<select id="timezone">
				<?php
					for ( $c = 0; $c < count( $timezones ); ++$c )
					{
						$selected = "" ;
						if ( $timezones[$c] == date_default_timezone_get() )
							$selected = "selected" ;

						print "<option value=\"$timezones[$c]\" $selected>$timezones[$c]</option>" ;
					}
				?>
				</select>
			</div>
			
			<div style="margin-top: 25px;"><button type="button" onClick="update_timezone()" class="btn">Update</button></div>
		</div>
		<?php endif; ?>

		<div style="display: none; margin-top: 25px; text-align: justify;" id="settings_eips">
			To avoid misleading page views when developing a site, exclude internal or company IP from being counted towards the overall footprint report.  Excluded IPs will not be visible on the traffic monitor. URL footprints of Excluded IPs will not be stored in the database and will not count towars the overall <a href="reports_traffic.php?ses=<?php echo $ses ?>">footprint report</a>.

			<div style="margin-top: 25px;">
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td valign="top" nowrap style="padding-right: 25px;">
						<div class="info_box">
							<div>Your IP: <span class="txt_orange"><?php echo Util_IP_GetIP() ?></span></div>
							<div style="margin-top: 15px;"><input type="text" name="ip_exclude" id="ip_exclude" size="20" maxlength="45" onKeyPress="return numbersonly(event)"></div>
							<div style="margin-top: 25px;"><input type="button" onClick="add_eip()" value="Add Exclude IP" class="btn"></div>
						</div>
					</td>
					<td valign="top" width="100%">
						<div><div class="td_dept_header">Current Excluded IPs:</div></div>
						<div id="eips" style="max-height: 300px; overflow: auto;"></div>
					</td>
				</tr>
				</table>
			</div>
		</div>

		<div style="display: none; margin-top: 25px;" id="settings_sips">
			Spam IPs will always see an OFFLINE status icon.  Operators can specify a spam IP during a chat session or you can provide an IP address here.  Spam IPs will still be displayed on the traffic monitor.

			<div style="margin-top: 25px;">
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td valign="top" nowrap style="padding-right: 25px;">
						<div class="info_box">
							<div>Example: <span class="txt_orange">123.456.789.101</span></div>
							<div style="margin-top: 15px;"><input type="text" name="ip_spam" id="ip_spam" size="20" maxlength="45" onKeyPress="return numbersonly(event)"></div>
							<div style="margin-top: 25px;"><input type="button" onClick="add_sip()" value="Add Spam IP" class="btn"></div>
						</div>
					</td>
					<td valign="top" width="100%">
						<div><div class="td_dept_header">Current Spam IPs:</div></div>
						<div id="sips" style="max-height: 300px; overflow: auto;"></div>
					</td>
				</tr>
				</table>
			</div>
		</div>

		<div style="display: none; margin-top: 25px;" id="settings_cookie">
			Switch on/off the use of cookies on the visitor chat window.  The cookies provide convenience for the visitor and does not affect the actual chat functions.
			<div style="margin-top: 25px;" class="info_info">
				Cookies set by the system:
				<li style="margin-top: 5px;"> <code>phplive_vname</code> - The visitor's name
				<li> <code>phplive_vemail</code> - The visitor's email address
			</div>

			<div style="margin-top: 25px;">
				<div class="li_op round"><input type="radio" name="cookie" id="cookie_on" value="on" onClick="confirm_change('on')" <?php echo $cookie_on ?>> Set cookies</div>
				<div class="li_op round"><input type="radio" name="cookie" id="cookie_off" value="off" onClick="confirm_change('off')" <?php echo $cookie_off ?>> Do not set cookies</div>
				<div style="clear: both;"></div>
			</div>
		</div>

		<div style="display: none; margin-top: 25px;" id="settings_screen">
			Choose whether to display the operator login and the setup login screens on the same URL or separate URLs.
		
			<div style="margin-top: 25px;">
				<div class="li_op round"><input type="radio" name="screen" id="screen_one" value="same" onClick="location.href='settings.php?ses=<?php echo $ses ?>&action=screen&screen=same'" <?php echo $screen_same ?>> Same URL</div>
				<div class="li_op round"><input type="radio" name="screen" id="screen_two" value="separate" onClick="location.href='settings.php?ses=<?php echo $ses ?>&action=screen&screen=separate'" <?php echo $screen_separate ?>> Separate URLs</div>
				<div style="clear: both;"></div>
			</div>

			<div style="margin-top: 25px;">
				<div id="urls_same" style="display: none;" class="info_info">
					<div style=""><img src="../pics/icons/key.png" width="16" height="16" border="0" alt=""> <img src="../pics/icons/bulb.png" width="16" height="16" border="0" alt="">Operator and Setup Login URL</div>
					<div style="margin-top: 5px; font-size: 32px; font-weight: bold; text-shadow: 1px 1px #FFFFFF;"><a href="<?php echo ( !preg_match( "/^(http)/", $CONF["BASE_URL"] ) ) ? "http$https:$CONF[BASE_URL]" : $CONF["BASE_URL"] ; ?>" target="new" style="color: #8DB173;" class="nounder"><?php echo ( !preg_match( "/^(http)/", $CONF["BASE_URL"] ) ) ? "http$https:$CONF[BASE_URL]" : $CONF["BASE_URL"] ; ?></a></div>
				</div>
				<div id="urls_separate" style="display: none;">
					<div class="info_info">
						<div style=""><img src="../pics/icons/bulb.png" width="16" height="16" border="0" alt=""> Operator Login URL</div>
						<div style="margin-top: 5px; font-size: 32px; font-weight: bold; text-shadow: 1px 1px #FFFFFF;"><a href="<?php echo ( !preg_match( "/^(http)/", $CONF["BASE_URL"] ) ) ? "http$https:$CONF[BASE_URL]" : $CONF["BASE_URL"] ; ?>" target="new" style="color: #8DB173;" class="nounder"><?php echo ( !preg_match( "/^(http)/", $CONF["BASE_URL"] ) ) ? "http$https:$CONF[BASE_URL]" : $CONF["BASE_URL"] ; ?></a></div>
					</div>

					<div class="info_info" style="margin-top: 25px;">
						<div style=""><img src="../pics/icons/key.png" width="16" height="16" border="0" alt=""> Setup Login URL</div>
						<div style="margin-top: 5px; font-size: 32px; font-weight: bold; text-shadow: 1px 1px #FFFFFF;"><a href="<?php echo ( !preg_match( "/^(http)/", $CONF["BASE_URL"] ) ) ? "http$https:$CONF[BASE_URL]" : $CONF["BASE_URL"] ; ?>/setup" target="new" style="color: #8DB173;" class="nounder"><?php echo ( !preg_match( "/^(http)/", $CONF["BASE_URL"] ) ) ? "http$https:$CONF[BASE_URL]" : $CONF["BASE_URL"] ; ?>/setup</a></div>
					</div>
				</div>
			</div>
		</div>

		<div style="display: none; margin-top: 25px;" id="settings_profile">
			Update the setup admin contact email address and the password to this setup area.  The admin email is used for various areas of the system, such as notification when an error is produced or when updating various setup settings.

			<div style="margin-top: 25px;">
				<input type="hidden" name="login" id="login" value="<?php echo $admininfo["login"] ?>">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td class="td_dept_td" width="120">Setup Admin Email</td>
					<td class="td_dept_td"><input type="text" class="input" size="35" maxlength="50" name="email" id="email" value="<?php echo $admininfo["email"] ?>" onKeyPress="return justemails(event)" value=""></td>
				</tr>
				<tr>
					<td colspan="4" style="padding-top: 15px;">
						<div style="font-size: 14px; font-weight: bold;">Update Password (optional)</div>
						<div style="margin-top: 15px;">
							<table cellspacing=0 cellpadding=4 border=0>
							<tr> 
								<td class="td_dept_td" width="120">New Password</td> 
								<td class="td_dept_td"><input type="password" class="input" size="35" maxlength="32" id="npassword" onKeyPress="return nospecials(event)"></td> 
							</tr>
							<tr>
								<td class="td_dept_td" width="120">Verify Password</td> 
								<td class="td_dept_td"><input type="password" class="input" size="35" maxlength="32" id="vpassword" onKeyPress="return nospecials(event)"></td> 
							</tr>
							</table>
						</div>
					</td>
				</tr>
				<tr> 
					<td></td> 
					<td class="td_dept_td"><input type="button" value="Update Profile" id="btn_submit" onClick="update_profile()" class="btn"></td> 
				</tr> 
				</table>
			</div>
		</div>

		</form>

<?php include_once( "./inc_footer.php" ) ?>

