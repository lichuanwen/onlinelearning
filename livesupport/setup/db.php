<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	/****************************************/
	// STANDARD header for Setup
	if ( !is_file( "../web/config.php" ) ){ HEADER("location: install.php") ; exit ; }
	include_once( "../web/config.php" ) ;
	include_once( "../API/Util_Format.php" ) ;
	include_once( "../API/Util_Error.php" ) ;
	include_once( "../API/SQL.php" ) ;
	include_once( "../API/Util_Security.php" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	if ( !$admininfo = Util_Security_AuthSetup( $dbh, $ses ) ){ ErrorHandler( 608, "Invalid setup session or session has expired.", $PHPLIVE_FULLURL, 0, Array() ) ; }
	// STANDARD header end
	/****************************************/

	include_once( "../API/Util_Functions.php" ) ;
	include_once( "../API/Util_DB.php" ) ;

	// permission checking
	$perm_web = is_writable( "$CONF[DOCUMENT_ROOT]/web" ) ;
	$perm_conf = is_writeable( "$CONF[DOCUMENT_ROOT]/web/config.php" ) ;
	$perm_chats = is_writeable( $CONF["CHAT_IO_DIR"] ) ;
	$perm_initiate = is_writeable( $CONF["TYPE_IO_DIR"] ) ;
	$perm_patches = is_writeable( "$CONF[DOCUMENT_ROOT]/web/patches" ) ;
	$disabled_functions = ini_get( "disable_functions" ) ;
	$ini_open_basedir = ini_get("open_basedir") ;
	$ini_safe_mode = ini_get("safe_mode") ;
	$safe_mode = preg_match( "/on/i", $ini_safe_mode ) ? 1 : 0 ;

	$tables = Util_DB_GetTableNames( $dbh ) ;

	$created = date( "M j, Y", $admininfo["created"] ) ;
	$diff = time() - $admininfo["created"] ; $days_running = round( $diff/(60*60*24) ) ;
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
	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "settings" ) ;
		fetch_admins() ;

		var help_tooltips = $( 'body' ).find('.help_tooltip' ) ;
		help_tooltips.tooltip({
			event: "mouseover",
			track: true,
			delay: 0,
			showURL: false,	
			showBody: "- ",
			fade: 0,
			positionLeft: false,
			extraClass: "stat"
		});
	});

	function generate_admin()
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		$('#btn_generate').attr( "disabled", true ) ;

		$.ajax({
		type: "GET",
		url: "../ajax/setup_actions.php",
		data: "action=generate_setup_admin&ses=<?php echo $ses ?>&"+unique,
		success: function(data){
			eval( data ) ;

			$('#btn_generate').attr( "disabled", false ) ;
			if ( json_data.status )
			{
				do_alert( 1, "Account Created" ) ;
				fetch_admins() ;
			}
			else
				do_alert( 0, json_data.error ) ;
		},
		error:function (xhr, ajaxOptions, thrownError){
			$('#btn_generate').attr( "disabled", false ) ;
			alert( "Could not connect to server.  Try reloading this page." ) ;
		} });
	}

	function fetch_admins()
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		$.ajax({
		type: "GET",
		url: "../ajax/setup_actions.php",
		data: "action=fetch_setup_admins&ses=<?php echo $ses ?>&"+unique,
		success: function(data){
			eval( data ) ;

			if ( json_data.status )
			{
				var admin_string = "<table cellspacing=0 cellpadding=0 border=0 width=\"100%\"><tr><td width=\"30\"><div class=\"td_dept_td\">&nbsp;</div></td><td width=\"70\"><div class=\"td_dept_td\"><b>Login</b></div></td><td width=\"70\"><div class=\"td_dept_td\"><b>Password</b></div></td><td><div class=\"td_dept_td\"><b>Accessed</b></div></td></tr>" ;
				for ( c = 0; c < json_data.admins.length; ++c )
				{
					var admin = json_data.admins[c] ;
					var password = admin["password"] ;
					var delete_option = "<img src=\"../pics/icons/delete.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"delete\" title=\"delete\" style=\"cursor: pointer;\" onClick=\"delete_admin("+admin["adminid"]+")\">" ;

					admin_string += "<tr><td><div class=\"td_dept_td\">"+delete_option+"</div></td><td><div class=\"td_dept_td\">"+admin["login"]+"</div></td><td><div class=\"td_dept_td\">"+password+"</div></td><td><div class=\"td_dept_td\">"+admin["lastactive"]+"</div></td></tr>" ;
				}
				admin_string += "</table>" ;
				
				$('#div_admins').html( admin_string ) ;
			}
			else
			{
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			alert( "Could not connect to server.  Try reloading this page." ) ;
		} });
	}

	function delete_admin( theadminid )
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		if ( confirm( "Really delete this Admin?" ) )
		{
			$.ajax({
			type: "GET",
			url: "../ajax/setup_actions.php",
			data: "action=delete_setup_admin&ses=<?php echo $ses ?>&adminid="+theadminid+"&"+unique,
			success: function(data){
				eval( data ) ;

				$('#btn_generate').attr( "disabled", false ) ;
				if ( json_data.status )
				{
					do_alert( 1, "Account Deleted" ) ;
					fetch_admins() ;
				}
				else
					do_alert( 0, json_data.error ) ;
			},
			error:function (xhr, ajaxOptions, thrownError){
				alert( "Could not connect to server.  Try reloading this page." ) ;
			} });
		}
	}

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

			<div class="op_submenu_wrapper">
				<div class="op_submenu" onClick="location.href='settings.php?ses=<?php echo $ses ?>&jump=logo'" id="menu_logo">Upload Logo</div>
				<div class="op_submenu" onClick="location.href='settings.php?ses=<?php echo $ses ?>&jump=themes'" id="menu_themes">Themes</div>
				<div class="op_submenu" onClick="location.href='settings.php?ses=<?php echo $ses ?>&jump=charset'" id="menu_charset">Character Set</div>
				<?php if ( phpversion() >= "5.1.0" ): ?><div class="op_submenu" onClick="location.href='settings.php?ses=<?php echo $ses ?>&jump=time'" id="menu_time">Time Zone</div><?php endif; ?>
				<div class="op_submenu" onClick="location.href='settings.php?ses=<?php echo $ses ?>&jump=eips'" id="menu_eips">Excluded IPs</div>
				<div class="op_submenu" onClick="location.href='settings.php?ses=<?php echo $ses ?>&jump=sips'" id="menu_sips">Spam IPs</div>
				<div class="op_submenu" onClick="location.href='settings.php?ses=<?php echo $ses ?>&jump=cookie'" id="menu_cookie">Cookies</div>
				<div class="op_submenu" onClick="location.href='settings.php?ses=<?php echo $ses ?>&jump=screen'" id="menu_screen">Login Screen</div>
				<div class="op_submenu" onClick="location.href='settings.php?ses=<?php echo $ses ?>&jump=profile'" id="menu_profile"><img src="../pics/icons/key.png" width="12" height="12" border="0" alt=""> Setup Profile</div>
				<div class="op_submenu_focus" id="menu_system">System</div>
				<div style="clear: both"></div>
			</div>

			<div style="margin-top: 25px;">
				<form>
				<div style="float: left; border: 0px solid transparent; width: 258px; margin-right: 25px;">
					<div style="margin-top: 10px; overflow: auto;">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td width="120"><div class="td_dept_header">Table</div></td>
							<td width="80"><div class="td_dept_header">Rows</div></td>
							<td><div class="td_dept_header">Status</div></td>
						</tr>
						<?php
							$approx_disc_use = 0 ;
							for( $c = 0; $c < count( $tables ); ++$c )
							{
								$analyze = Util_DB_AnalyzeTable( $dbh, $tables[$c] ) ;
								$stats = Util_DB_TableStats( $dbh, $tables[$c] ) ;

								$name = $stats["Name"] ;
								$type = $analyze["Msg_type"] ;
								$status = $analyze["Msg_text"] ;

								if ( preg_match( "/^p_/", $name ) )
								{
									if ( $status = "Table is already up to date" )
										$status = "OK" ;

									$rows = $stats["Rows"] ;
									$ave_row_size = $stats["Avg_row_length"] ;
									$ave_disk = $ave_row_size * $rows ;
									$ave_size = Util_Functions_Bytes( $ave_disk ) ;
									
									$approx_disc_use += $ave_disk ;

									print "<tr class=\"help_tooltip\" title=\"- <b>Table: $name</b><br>Status: $status<br>Records: $rows rows<br>Approx. disk usage: $ave_size\" style=\"cursor: pointer;\"><td class=\"td_dept_td_td\">$name</td><td class=\"td_dept_td_td\">$rows</td><td class=\"td_dept_td_td\">$status</td></tr>" ;
								}
							}

							$approx_disc_use = Util_Functions_Bytes( $approx_disc_use ) ;
							print "<tr><td></td><td class=\"help_tooltip\" title=\"Total Disk Usage - Approximately $approx_disc_use\" style=\"cursor: pointer;\"><b>$approx_disc_use</b></td><td></td></tr>" ;
						?>
						</table>
					</div>
				</div>
				<div style="float: left; width: 610px;">

					<div class="info_box"><b>License Key:</b> <?php echo $KEY ?></div>
					<div style="margin-top: 10px; font-size: 10px;">
					<?php
						$query = "SHOW GRANTS FOR '$CONF[SQLLOGIN]'@'$CONF[SQLHOST]'" ;
						database_mysql_query( $dbh, $query ) ;

						$output = Array() ;
						if ( $dbh[ 'ok' ] )
						{
							while ( $data = database_mysql_fetchrow( $dbh ) )
								print_r( preg_replace( "/PASSWORD (.*?)( |$)/i", "PASSWORD ************* ", $data ) ) ;
						}
						else
							print $dbh["error"] ;
					?>
					</div>

					<div class="home_box_header" style="margin-top: 25px;">Server Information</div>
					<ul style="margin-top: 10px;">
						<li> OS: <?php echo PHP_OS ?>
						<li> Webserver: <?php echo function_exists( "apache_get_version" ) ? apache_get_version() : "could not detect" ; ?>
						<li> PHP: <?php echo PHP_VERSION ?>
						<li> MySQL: <?php echo database_mysql_version( $dbh ) ?>
						<li> Disabled Functions: <?php print "$disabled_functions " ; ?>
						<li> Safe Mode: <?php echo "$ini_safe_mode, $safe_mode (obd: $ini_open_basedir)" ?>
					</ul>

					<div class="home_box_header" style="margin-top: 25px;">Directory and file permissions:</div>
					<ul style="margin-top: 10px;">
						<li> web/ directory: <?php echo ( $perm_web ) ? "write access OK" : "permission error" ; ?>
						<li> web/config.php: <?php echo ( $perm_conf ) ? "write access OK" : "permission error" ; ?>
						<li> web/chat_sessions: <?php echo ( $perm_chats ) ? "write access OK" : "permission error" ; ?>
						<li> web/chat_initiate: <?php echo ( $perm_initiate ) ? "write access OK" : "permission error" ; ?>
						<li> web/patches: <?php echo ( $perm_patches ) ? "write access OK" : "permission error" ; ?>
					</ul>

					<?php if ( $admininfo["status"] != -1 ): ?>
					<div class="home_box_header" style="margin-top: 25px;">System Installed:</div>
					<ul style="margin-top: 10px;">
						<li>  <?php echo $created ?>, up <?php echo $days_running ?> days
					</ul>

					<div style="margin-top: 25px; height: 150px; text-align: justify;" class="info_info">
						<div style="float: left; width: 200px; margin-right: 15px;">
							<div class="edit_title">Temporary Admins</div>
							<div style="margin-top: 15px;">At this time, temporary setup admins have access to all the setup options.</div>
							<div style="margin-top: 15px;"><button type="button" onClick="generate_admin()" id="btn_generate" class="btn">Generate Admin</button></div>
						</div>
						<div style="float: left; width: 350px;">
							<div style="margin-top: 15px; max-height: 145px; overflow: auto;" id="div_admins"></div>
						</div>
						<div style="clear: both;"></div>
					</div>
					<?php endif ; ?>

				</div>
				<div style="clear:both;"></div>
				</form>

			</div>

<?php include_once( "./inc_footer.php" ) ?>

