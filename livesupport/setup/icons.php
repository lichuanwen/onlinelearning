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

	$error = "" ;

	include_once( "../API/Util_Upload.php" ) ;
	include_once( "../API/Ops/get.php" ) ;
	include_once( "../API/Depts/get.php" ) ;

	$deptinfo = Array() ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "ln" ) ;
	$jump = Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) ;

	$upload_dir = "$CONF[DOCUMENT_ROOT]/web" ;
	$offline = ( isset( $VALS['OFFLINE'] ) && $VALS['OFFLINE'] ) ? unserialize( $VALS['OFFLINE'] ) : Array() ;

	if ( $action == "upload" )
	{
		$icon = isset( $_FILES['icon_online']['name'] ) ? "icon_online" : "icon_offline" ;
		$error = Util_Upload_File( $icon, $deptid ) ;
	}
	else if ( $action == "update_offline" )
	{
		include_once( "../API/Util_Vals.php" ) ;

		$option = Util_Format_Sanatize( Util_Format_GetVar( "option" ), "ln" ) ;
		$url = Util_Format_Sanatize( Util_Format_GetVar( "url" ), "url" ) ;

		$dept_hash = Array() ; $departments = Depts_get_AllDepts( $dbh ) ;
		for ( $c = 0; $c < count( $departments ); ++$c )
		{
			$department = $departments[$c] ;
			$dept_hash[$department["deptID"]] = 1 ; 
		}

		foreach( $offline as $key => $value )
		{
			if ( $key && !isset( $dept_hash[$key] ) )
				unset( $offline[$key] ) ;
		}
		$offline[$deptid] = ( $option == "redirect" ) ? "$url" : $option ;
		Util_Vals_WriteToFile( "OFFLINE", serialize( $offline ) ) ;
		$jump = "options" ;
	}
	else if ( $action == "reset" )
	{
		include_once( "../API/Util_Vals.php" ) ;

		foreach( $offline as $key => $value )
		{
			if ( $key && ( $key == $deptid ) )
				unset( $offline[$key] ) ;
		}
		Util_Vals_WriteToFile( "OFFLINE", serialize( $offline ) ) ;
		$jump = "options" ;
	}

	if ( !isset( $departments ) )
		$departments = Depts_get_AllDepts( $dbh ) ;
	
	if ( $deptid )
		$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;

	$offline_option = "icon" ; $redirect_url = "" ;
	if ( isset( $offline[$deptid] ) )
	{
		if ( !preg_match( "/^(icon|hide|embed)$/", $offline[$deptid] ) ) { $offline_option = "redirect" ; $redirect_url = $offline[$deptid] ; }
		else{ $offline_option = $offline[$deptid] ; }
	}
	else
	{
		if ( isset( $offline[0] ) )
		{
			if ( !preg_match( "/^(icon|hide|embed)$/", $offline[0] ) ) { $offline_option = "redirect" ; $redirect_url = $offline[0] ; }
			else{ $offline_option = $offline[0] ; }
		}
	}
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

<script type="text/javascript">
<!--
	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "icons" ) ;
		
		<?php if ( $jump == "options" ): ?>show_div( 'options' ) ;<?php else: ?>show_div( 'icon' ) ;<?php endif ; ?>

		<?php if ( $action && !$error ): ?>do_alert( 1, "Success" ) ;<?php endif ; ?>
		<?php if ( $action && $error ): ?>do_alert( 0, "<?php echo $error ?>" ) ;<?php endif ; ?>
	});

	function switch_dept( theobject )
	{
		location.href = "icons.php?ses=<?php echo $ses ?>&deptid="+theobject.value+"&"+unixtime() ;
	}

	function show_div( thediv )
	{
		var divs = Array( "icon", "options" ) ;
		for ( c = 0; c < divs.length; ++c )
		{
			$('#offline_'+divs[c]).hide() ;
			$('#menu_'+divs[c]).removeClass('op_submenu_focus').addClass('op_submenu') ;
		}

		$('#offline_'+thediv).show() ;
		$('#menu_'+thediv).removeClass('op_submenu').addClass('op_submenu_focus') ;
	}

	function check_url()
	{
		var url = $('#offline_url').val() ;
		var url_ok = ( url.match( /(http:\/\/)|(https:\/\/)/i ) ) ? 1 : 0 ;

		if ( !url )
			return "Please provide the webpage URL." ;
		else if ( !url_ok )
			return "URL should begin with http:// or https:// for correct loading." ;
		else
			return false ;
	}

	function open_url()
	{
		var unique = unixtime() ;
		var url = $('#offline_url').val() ;
		var error = check_url() ;

		if ( error )
			do_alert( 0, error ) ;
		else
			window.open(url, unique, 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1') ;
	}

	function update_offline()
	{
		var unique = unixtime() ;
		var url = $('#offline_url').val().replace( /http/ig, "hphp" ) ;
		var option = $("input[name='option']:checked").val() ;
		var error = check_url() ;

		if ( error && ( option == "redirect" ) )
			do_alert( 0, error ) ;
		else
			location.href = "./icons.php?action=update_offline&ses=<?php echo $ses ?>&deptid=<?php echo $deptid ?>&option="+option+"&url="+url+"&"+unique ;
	}

	function reset_offline( thedeptid )
	{
		if ( confirm( "Clear current offline settings and use Global Default?" ) )
			location.href = "./icons.php?ses=<?php echo $ses ?>&action=reset&deptid="+thedeptid ;
	}

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div style="">
			<div style="">The "Global Default" chat icons is displayed until new chat icons have been uploaded for that department. Browse available <a href="http://www.phplivesupport.com/r.php?r=icons" target="icons">chat icons</a> to download and to use for your system.</div>

			<div style="margin-top: 15px;">
				<form method="POST" action="manager_canned.php?submit" id="form_theform">
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
				</form>
			</div>
		</div>

		<div style="margin-top: 25px;">
		
			<table cellspacing=0 cellpadding=0 border=0 width="100%">
			<tr>
				<td></td>
				<td>
					<div style="padding-bottom: 15px;">
						<div class="op_submenu" onClick="show_div('icon')" id="menu_icon">Offline Icon</div>
						<div class="op_submenu" onClick="show_div('options')" id="menu_options">Offline Options</div>
						<div style="clear: both"></div>
					</div>
				</td>
			</tr>
			<tr>
				<td valign="top" width="50%">
					<form method="POST" action="icons.php?submit" enctype="multipart/form-data">
					<input type="hidden" name="ses" value="<?php echo $ses ?>">
					<input type="hidden" name="action" value="upload">
					<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
					<div class="edit_title"><?php echo ( isset( $deptinfo["name"] ) ) ? $deptinfo["name"] : "Global Default" ; ?> ONLINE</div>
					<div style="margin-top: 10px;">
						<input type="file" name="icon_online" size="30"><p>
						<input type="submit" value="Upload Image" style="margin-top: 10px;" class="btn">
					</div>
					
					<div style="margin-top: 15px;"><img src="<?php print Util_Upload_GetChatIcon( "..", "icon_online", $deptid ) ?>" border="0" alt=""></div>
					</form>
				</td>
				<td valign="top" width="50%">
					<form method="POST" action="icons.php?submit" enctype="multipart/form-data" id="form_offline" name="form_offline">
					<input type="hidden" name="ses" value="<?php echo $ses ?>">
					<input type="hidden" name="action" value="upload">
					<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
					<div class="edit_title"><?php echo ( isset( $deptinfo["name"] ) ) ? $deptinfo["name"] : "Global Default" ; ?> OFFLINE</div>
					<div id="offline_icon" style="display: none;">
						<div style="margin-top: 10px;">
							<input type="file" name="icon_offline" size="30"><p>
							<input type="submit" value="Upload Image" style="margin-top: 10px;" class="btn">
						</div>
						
						<div style="margin-top: 15px;"><img src="<?php print Util_Upload_GetChatIcon( "..", "icon_offline", $deptid ) ?>" border="0" alt=""></div>
					</div>
					<div id="offline_options" style="display: none; margin-top: 10px;" class="info_info">
						<?php if ( $deptid && ( count( $departments ) == 1 ) ): ?>
						<table cellspacing=1 cellpadding=5 border=0 width="100%">
						<tr>
							<td colspan=2><div style="font-size: 14px; font-weight: bold;">When live chat is offline:</div></td>
						</tr>
						<tr>
							<td>Only one department exists.  Set the <a href="./icons.php?ses=<?php echo $ses ?>&jump=options">Global Default</a> settings.</td>
						</tr>
						</table>

						<?php else: ?>
						<table cellspacing=1 cellpadding=5 border=0 width="100%">
						<tr>
							<td colspan=2><div style="font-size: 14px; font-weight: bold;">When live chat is offline:</div></td>
						</tr>
						<tr>
							<td width="25" align="center"><input type="radio" name="option" value="icon" <?php echo ( $offline_option == "icon" ) ? "checked" : "" ; ?>></td>
							<td>Display the offline chat icon and open the leave a message window when the icon is clicked.</td>
						</tr>
						<tr>
							<td width="25" align="center"><input type="radio" name="option" value="embed" <?php echo ( $offline_option == "embed" ) ? "checked" : "" ; ?>></td>
							<td>Display the offline chat icon and <b>(embed)</b> the leave a message window on the webpage when the icon is clicked.</td>
						</tr>
						<tr>
							<td width="25" align="center"><input type="radio" name="option" id="option_redirect" value="redirect" <?php echo ( $offline_option == "redirect" ) ? "checked" : "" ; ?> onClick="$('#offline_url').focus()"></td>
							<td>Display the offline chat icon and redirect the visitor to a webpage when the icon is clicked. Provide the redirect URL below:<div style="margin-top: 5px;"><input type="text" class="input" style="width: 80%;" maxlength="255" name="offline_url" id="offline_url" value="<?php echo $redirect_url ?>" onFocus="$('#option_redirect').attr('checked', 'checked')"> &nbsp; <span style="">&middot; <a href="JavaScript:void(0)" onClick="open_url()">visit</a></span></div></td>
						</tr>
						<tr>
							<td width="25" align="center"><input type="radio" name="option" value="hide" <?php echo ( $offline_option == "hide" ) ? "checked" : "" ; ?>></td>
							<td>Do not display the offline chat icon.</td>
						</tr>
						<tr>
							<td></td>
							<td><div style="padding-top: 5px;"><button type="button" onClick="update_offline()" class="btn">Update</button>
							<?php
								if ( $deptid && !isset( $offline[$deptid] ) ):
									print " currently using Global Default" ;
								elseif ( $deptid ):
									print " or reset and use <a href=\"JavaScript:void(0)\" onClick=\"reset_offline( $deptid )\">Global Default</a>" ;
								endif ;
							?>
							</div></td>
						</tr>
						</table>
						<?php endif ; ?>
					</div>
					</form>
				</td>
			</tr>
			</table>

		</div>

<?php include_once( "./inc_footer.php" ) ?>

