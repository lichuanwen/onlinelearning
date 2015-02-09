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

	include_once( "../API/Util_Vals.php" ) ;
	include_once( "../API/Util_Upload.php" ) ;
	include_once( "../API/Ops/get.php" ) ;
	include_once( "../API/Ops/get_itr.php" ) ;
	include_once( "../API/Depts/get.php" ) ;
	include_once( "../API/Vars/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$jump = ( Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) ) ? Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) : "main" ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "ln" ) ;
	$proto = Util_Format_Sanatize( Util_Format_GetVar( "proto" ), "ln" ) ;
	$update = Util_Format_Sanatize( Util_Format_GetVar( "update" ), "ln" ) ;
	$position = Util_Format_Sanatize( Util_Format_GetVar( "position" ), "ln" ) ;
	$upload_dir = "$CONF[DOCUMENT_ROOT]/web" ;

	if ( !isset( $CONF["icon_check"] ) )
		$CONF["icon_check"] = "off" ;

	$icon_check_off = ( $CONF["icon_check"] == "off" ) ? "checked" : "" ;
	$icon_check_on = ( $icon_check_off == "checked" ) ? "" : "checked" ;

	$departments = Depts_get_AllDepts( $dbh ) ;
	$ops_assigned = 0 ;
	for ( $c = 0; $c < count( $departments ); ++$c )
	{
		$department = $departments[$c] ;
		$ops = Depts_get_DeptOps( $dbh, $department["deptID"] ) ;
		if ( count( $ops ) )
			$ops_assigned = 1 ;
	}
	$deptinfo = Array() ;
	if ( $deptid )
		$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;

	$total_ops = Ops_get_TotalOps( $dbh ) ;
	$total_ops_online = Ops_get_itr_AnyOpsOnline( $dbh, $deptid ) ;

	if ( $update )
	{
		include_once( "../API/Vars/update.php" ) ;

		$vars = Util_Format_Get_Vars( $dbh ) ;
		if ( $proto != $vars["code"] )
		{
			include_once( "../API/Util_Vals.php" ) ;

			$error = "" ;
			if ( !$proto && preg_match( "/^(https:\/\/)|(http:\/\/)/i", $CONF["BASE_URL"] ) )
				$error = ( Util_Vals_WriteToConfFile( "BASE_URL", preg_replace( "/^(http:\/\/)|(https:\/\/)/i", "//", $CONF["BASE_URL"] ) ) ) ? "" : "Could not write to config file." ;
			else if ( ( $proto == 1 ) && preg_match( "/^(https:\/\/)|(\/\/)/i", $CONF["BASE_URL"] ) )
				$error = ( Util_Vals_WriteToConfFile( "BASE_URL", preg_replace( "/^(https:\/\/)|(http:\/\/)|(\/\/)/i", "http://", $CONF["BASE_URL"] ) ) ) ? "" : "Could not write to config file." ;
			else if ( ( $proto == 2 ) && preg_match( "/^(http:\/\/)|(\/\/)/i", $CONF["BASE_URL"] ) )
				$error = ( Util_Vals_WriteToConfFile( "BASE_URL", preg_replace( "/^(https:\/\/)|(http:\/\/)|(\/\/)/i", "https://", $CONF["BASE_URL"] ) ) ) ? "" : "Could not write to config file." ;

			if ( !$error ) { Vars_update_Var( $dbh, "code", $proto ) ; }
		}
		Vars_update_Var( $dbh, "position", $position ) ;
	}

	$position_css = "" ;
	$vars = Util_Format_Get_Vars( $dbh ) ;
	if ( isset( $vars["code"] ) )
	{
		$proto = $vars["code"] ;
		// perhaps use CSS to rotate but best to have it so it is by image to limit confusion
		// http://www.w3schools.com/cssref/css3_pr_transform-origin.asp
		switch ( $vars["position"] )
		{
			case 2:
				$position_css = " position: fixed; bottom: 0px; right: 0px; z-index: 1000;" ;
				break ;
			case 3:
				$position_css = " position: fixed; bottom: 0px; left: 0px; z-index: 1000;" ;
				break ;
			case 4:
				$position_css = " position: fixed; top: 0px; right: 0px; z-index: 1000;" ;
				break ;
			case 5:
				$position_css = " position: fixed; top: 0px; left: 0px; z-index: 1000;" ;
				break ;
			case 6:
				$position_css = " position: fixed; top: 50%; left: 0px; z-index: 1000;" ;
				break ;
			case 7:
				$position_css = " position: fixed; top: 50%; right: 0px; z-index: 1000;" ;
				break ;
			default:
				$position_css = "" ;
		}
	}

	$now = time() ;
	$base_url = $CONF["BASE_URL"] ;
	$code = "&lt;!-- Begin PHP Live! HTML Code --&gt;-nl-&lt;span id=\"phplive_btn_$now\" onclick=\"phplive_launch_chat_$deptid(0)\" style=\"color: #0000FF; text-decoration: underline; cursor: pointer;$position_css\"&gt;&lt;/span&gt;-nl-&lt;script type=\"text/JavaScript\"&gt;-nl-(function() {-nl-	var phplive_e_$now = document.createElement(\"script\") ;-nl-	phplive_e_$now.type = \"text/javascript\" ;-nl-	phplive_e_$now.async = true ;-nl-	phplive_e_$now.src = \"%%base_url%%/js/phplive_v2.js.php?v=$deptid|$now|$proto|%%text_string%%\" ;-nl-	document.getElementById(\"phplive_btn_$now\").appendChild( phplive_e_$now ) ;-nl-})();-nl-&lt;/script&gt;-nl-&lt;!-- End PHP Live! HTML Code --&gt;" ;
	$temp = $now+1 ;

	if ( $proto == 1 )
		$base_url = preg_replace( "/(http:)|(https:)/", "http:", $base_url ) ;
	else if ( $proto == 2 )
		$base_url = preg_replace( "/(http:)|(https:)/", "https:", $base_url ) ;
	else
		$base_url = preg_replace( "/(http:)|(https:)/", "", $base_url ) ;

	$thecode = preg_replace( "/%%base_url%%/", $base_url, $code ) ;
	$code_html = preg_replace( "/&lt;/", "<", $thecode ) ;
	$code_html = preg_replace( "/&gt;/", ">", $code_html ) ;
	$code_html = preg_replace( "/-nl-/", "\r\n", $code_html ) ;

	/*** Automatic Initiate Chat ***/
	if ( ( $action == "update" ) && isset( $_FILES['icon_initiate']['name'] ) && $_FILES['icon_initiate']['name'] )
	{
		$error = Util_Upload_File( "icon_initiate", 0 ) ;
		if ( !$error )
		{
			database_mysql_close( $dbh ) ;
			HEADER( "location: code.php?ses=$ses&action=update&jump=auto" ) ;
			exit ;
		}
	}
	else if ( $action == "update" )
	{
		$duration = Util_Format_Sanatize( Util_Format_GetVar( "duration" ), "ln" ) ;
		$andor = Util_Format_Sanatize( Util_Format_GetVar( "andor" ), "ln" ) ;
		$footprints = Util_Format_Sanatize( Util_Format_GetVar( "footprints" ), "ln" ) ;
		$reset = Util_Format_Sanatize( Util_Format_GetVar( "reset" ), "ln" ) ;
		$pos = Util_Format_Sanatize( Util_Format_GetVar( "pos" ), "ln" ) ;
		$exclude = preg_replace( "/[;*\/:\[\]\"\']/", "", stripslashes( Util_Format_Sanatize( Util_Format_GetVar( "exclude" ), "ln" ) ) ) ;

		$exclude_array = explode( ",", $exclude ) ;
		$exclude_string = "" ;
		for ( $c = 0; $c < count( $exclude_array ); ++$c )
		{
			$temp = preg_replace( "/ +/", "", $exclude_array[$c] ) ;
			if ( $temp )
				$exclude_string .= "$temp," ;
		}

		if ( $exclude_string ) { $exclude_string = substr_replace( $exclude_string, "", -1 ) ; }

		$initiate = Array() ;
		$initiate["duration"] = $duration ;
		$initiate["andor"] = $andor ;
		$initiate["footprints"] = $footprints ;
		$initiate["reset"] = $reset ;
		$initiate["exclude"] = $exclude_string ;
		$initiate["pos"] = $pos ;

		$initiate_serialize = serialize( $initiate ) ;
		$admininfo["initiate"] = $initiate_serialize ;

		if ( $andor )
		{
			$error = ( Util_Vals_WriteToConfFile( "auto_initiate", htmlentities( $initiate_serialize ) ) ) ? "" : "Could not write to config file." ;
			if ( !$error )
			{
				database_mysql_close( $dbh ) ;
				HEADER( "location: code.php?ses=$ses&action=update&jump=auto" ) ;
				exit ;
			}
		}
	}

	$initiate = ( isset( $CONF["auto_initiate"] ) && $CONF["auto_initiate"] ) ? unserialize( html_entity_decode( $CONF["auto_initiate"] ) ) : Array() ;
	
	$offline = ( isset( $VALS['OFFLINE'] ) ) ? unserialize( $VALS['OFFLINE'] ) : Array() ;
	$offline_option = "icon" ;
	if ( isset( $offline[$deptid] ) )
	{
		if ( !preg_match( "/^(icon|hide)$/", $offline[$deptid] ) ) { $offline_option = "redirect" ; }
		else{ $offline_option = $offline[$deptid] ; }
	}
	else
	{
		if ( isset( $offline[0] ) )
		{
			if ( !preg_match( "/^(icon|hide)$/", $offline[0] ) ) { $offline_option = "redirect" ; }
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
	var global_icon_check = "<?php echo $CONF["icon_check"] ?>" ;
	var global_pos = <?php echo ( isset( $initiate["pos"] ) ) ? $initiate["pos"] : 1 ; ?> ;
	var thecode = '<?php echo $thecode ?>' ;
	thecode = thecode.replace( /-nl-/g, "\r\n" ) ;
	thecode = thecode.replace( /&lt;/g, "<" ) ;
	thecode = thecode.replace( /&gt;/g, ">" ) ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "html" ) ;

		populate_code( "standard" ) ;
		show_div( "<?php echo $jump ?>" ) ;

		<?php if ( isset( $initiate["andor"] ) && $initiate["andor"] ): ?>toggle_select_footprints( <?php echo $initiate["andor"] ?> ) ;<?php endif ; ?>

		<?php if ( $update && !$error ): ?>do_alert(1, "New HTML Code Generated") ;
		<?php elseif ( $update && $error ): ?>do_alert(0, "<?php echo $error ?>") ;
		<?php elseif ( $action && !$error ): ?>do_alert( 1, "Success" ) ;
		<?php elseif ( $action && $error ): ?>do_alert( 0, "<?php echo $error ?>" ) ;
		<?php endif ; ?>
	});

	function switch_dept( theobject )
	{
		location.href = "code.php?ses=<?php echo $ses ?>&deptid="+theobject.value+"&proto=<?php echo $proto ?>&"+unixtime() ;
	}

	function populate_code( thetextarea )
	{
		if ( thetextarea == "standard" )
		{
			var code = thecode.replace( /%%text_string%%/g, "" ) ;
			$('#textarea_code_'+thetextarea).val( code ) ;
		}
		else if ( thetextarea == "text" )
		{
			var text = escape( $('#code_text').val() ) ;
			var code = thecode.replace( /%%text_string%%/g, text ) ;

			if ( text == "" )
				do_alert( 0, "Please provide the text." ) ;
			else
			{
				$('#code_text_code').show() ;
				$('#html_code_text_output').html("<span onClick=\"phplive_launch_chat_<?php echo $deptid ?>(0)\" style=\"cursor: pointer;\">"+$('#code_text').val()+"</span>") ;
				$('#textarea_code_'+thetextarea).val( code ) ;
				$('#html_code_text_output_tip').show() ;
			}
		}
	}

	function input_text_listen_text( e )
	{
		var key = -1 ;
		var shift ;

		key = e.keyCode ;
		shift = e.shiftKey ;

		if ( !shift && ( ( key == 13 ) || ( key == 10 ) ) )
			$('#btn_generate').click() ;
	}

	function toggle_code( theproto )
	{
		var proto = $('input[name=proto]:checked', '#form_proto').val() ;
		var position = $('#position').val() ;

		location.href = "./code.php?ses=<?php echo $ses ?>&deptid=<?php echo $deptid ?>&proto="+proto+"&position="+position+"&update=1&"+unixtime() ;
	}

	function show_div( thediv )
	{
		var divs = Array( "main", "check", "auto" ) ;
		for ( c = 0; c < divs.length; ++c )
		{
			$('#code_'+divs[c]).hide() ;
			$('#menu_code_'+divs[c]).removeClass('op_submenu_focus').addClass('op_submenu') ;
		}

		$('#code_'+thediv).show() ;
		$('#menu_code_'+thediv).removeClass('op_submenu').addClass('op_submenu_focus') ;
	}

	function confirm_change( theflag )
	{
		if ( global_icon_check != theflag )
		{
			if ( confirm( "Switch real-time icon checking to "+theflag+"?" ) )
			{
				$.ajax({
					type: "POST",
					url: "../ajax/setup_actions.php",
					data: "ses=<?php echo $ses ?>&action=update_icon_check&value="+theflag+"&"+unixtime(),
					success: function(data){
						global_icon_check = theflag ;
						do_alert( 1, "Success!" ) ;
					}
				});
			}
			else
				$('#icon_check_'+global_icon_check).attr('checked', true) ;
		}
	}

	function show_html_code()
	{
		$('#btn_show').hide() ;
		$('#div_html_code').show() ;
	}

	function toggle_select_footprints( thevalue )
	{
		return true ;

		if( thevalue > 0 )
			$('#div_footprints').show() ;
		else
			$('#div_footprints').hide() ;
	}

	function view_invite()
	{
		if ( global_pos && ( global_pos != $('#pos').val() ) )
			alert( "Please save changes before viewing." ) ;
		else
		{
			$.ajax({
				type: "POST",
				url: "../ajax/setup_actions.php",
				data: "ses=<?php echo $ses ?>&action=view_invite&"+unixtime(),
				success: function(data){
					eval( data ) ;

					if ( json_data.status )
					{
						phplive_footprint_tracker_<?php echo $deptid ?>() ;
						do_alert( 1, "Launching..." ) ;
					}
					else
						do_alert( 0, "Error: Could not create initiate file." ) ;
				}
			});
		}
	}
//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<?php if ( !count( $departments ) ): $display = 0 ; ?>
		<span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> Create a <a href="depts.php?ses=<?php echo $ses ?>" style="color: #FFFFFF;">Department</a> to continue.</span>
		<?php elseif ( !$total_ops ): $display = 0 ; ?>
		<span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> Create an <a href="ops.php?ses=<?php echo $ses ?>" style="color: #FFFFFF;">Operator</a> to continue.</span>
		<?php elseif ( !$ops_assigned ): $display = 0 ; ?>
		<span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> <a href="ops.php?ses=<?php echo $ses ?>&jump=assign" style="color: #FFFFFF;">Assign an operator to a department</a> to continue.</span>
		<?php
			else:
			$display = 1 ;
			$select_depts = 1 ;

			if ( count( $departments ) == 1 )
			{
				$department = $departments[0] ;
				if ( $department["visible"] )
					$select_depts = 0 ;
			}
		?>
		<?php endif ; ?>

		<?php if ( $display ): ?>
		<div class="op_submenu_wrapper">
			<div class="op_submenu" onClick="show_div('main')" id="menu_code_main">HTML Code</div>
			<div class="op_submenu" onClick="show_div('check')" id="menu_code_check">Settings</div>
			<div class="op_submenu" onClick="show_div('auto')" id="menu_code_auto">Automatic Initiate Chat</div>
			<div style="clear: both"></div>
		</div>

		<div id="code_main" style="display: none; margin-top: 25px;">
			<div id="code_main_info" class="info_info">
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td valign="top" style="padding-right: 50px; text-align: justify;" width="50%">
						<div>HTML Code can be generated to display all departments or just one.</div>
						<div style="margin-top: 15px;">"All Departments" HTML Code will allow the visitor to select a department on the chat request window.  For the department only HTML Code, the department selection is not available because the visitor is automatically routed to that department.</div>
						<div style="margin-top: 25px;">
							<form method="POST" action="manager_canned.php?submit" id="form_theform">
							<span class="home_box_header">HTML Code for </span> <select name="deptid" id="deptid" style="font-size: 16px; background: #D4FFD4; color: #009000;" OnChange="switch_dept( this )">
							<option value="0">ALL Departments</option>
							<?php
								if ( $select_depts )
								{
									for ( $c = 0; $c < count( $departments ); ++$c )
									{
										$department = $departments[$c] ;

										if ( $department["name"] != "Archive" )
										{
											$selected = ( $deptid == $department["deptID"] ) ? "selected" : "" ;
											print "<option value=\"$department[deptID]\" $selected>$department[name]</option>" ;
										}
									}
								}
							?>
							</select>
							</form>
						</div>
					</td>
					<td width="50%">
						<form id="form_proto">
						<div style="">
							<div style=""><input type="radio" name="proto" value="0" <?php echo ( !isset( $vars["code"] ) || !$vars["code"] ) ? "checked" : "" ?> onClick="toggle_code()"> Toggle <b><i>HTTP and HTTPS</i></b> based on URL (recommended)</div>
							<div style="margin-top: 5px;"><input type="radio" name="proto" value="1" <?php echo ( $vars["code"] == 1 ) ? "checked" : "" ?> onClick="toggle_code()"> Always <b><i>HTTP</i></b></div>
							<div style="margin-top: 5px;"><input type="radio" name="proto" value="2" <?php echo ( $vars["code"] == 2 ) ? "checked" : "" ?> onClick="toggle_code()"> Always <b><i>HTTPS</i></b> (SSL enabled servers)</div>
						</div>
						</form>

						<div style="margin-top: 25px;">
							<form id="proto_pos">
							<div style="">Chat Icon Position</div>
							<div style="margin-top: 5px;">
								<select name="position" id="position" onChange="toggle_code()" style="background: #D5E6FD;">
									<option value="1" <?php echo ( $vars["position"] == 1 ) ? "selected" : "" ; ?>>Display the chat icon where the HTML code is placed. (default)</option>
									<option value="2" <?php echo ( $vars["position"] == 2 ) ? "selected" : "" ; ?>>Hover: bottom right corner of the page.</option>
									<option value="3" <?php echo ( $vars["position"] == 3 ) ? "selected" : "" ; ?>>Hover: bottom left corner of the page.</option>
									<option value="4" <?php echo ( $vars["position"] == 4 ) ? "selected" : "" ; ?>>Hover: top right corner of the page.</option>
									<option value="5" <?php echo ( $vars["position"] == 5 ) ? "selected" : "" ; ?>>Hover: top left corner of the page.</option>
									<option value="6" <?php echo ( $vars["position"] == 6 ) ? "selected" : "" ; ?>>Hover: center left of the page.</option>
									<option value="7" <?php echo ( $vars["position"] == 7 ) ? "selected" : "" ; ?>>Hover: center right of the page. (next to scrollbar)</option>
								</select>
							</div>
							</form>
						</div>
					</td>
				</tr>
				</table>

			</div>

			<?php if ( isset( $deptinfo["deptID"] ) && !$deptinfo["visible"] ): ?>
			<div class="info_error" style="margin-top: 15px;"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> Although this department is <a href="depts.php?ses=<?php echo $ses ?>" style="color: #FFFFFF;">not visible for selection</a>, visitors can still reach this department with the department specific HTML Code below.</div>
			<?php endif ; ?>

			<div id="div_html_code" style="margin-top: 25px; text-align: justify;">
				
				<div style="" class="td_dept_td">
					<div class="edit_title"><img src="../pics/icons/code.png" width="16" height="16" border="0" alt=""> Standard HTML Code (recommended)</div>
					<div style="margin-top: 5px;">Copy/Paste the below HTML Code onto your webpages to display the online/offline chat icon.  It is recommended that you place the HTML Code on all of your webpages. For multiple live chat icons on the same page, please refer to the <a href="http://www.phplivesupport.com/r.php?r=multi" target="new">multiple chat icons documentation</a>.</div>
					<div style="margin-top: 15px;"><textarea wrap="virtual" id="textarea_code_standard" style="padding: 20px; width: 930px; height: 100px; resize: none;" onMouseDown="setTimeout(function(){ $('#textarea_code_standard').select(); }, 200);" readonly></textarea></div>

					<div style="margin-top: 10px; font-size: 10px;">The above code will produce the following status icon.</div>
					<?php if ( !$total_ops_online && ( $offline_option == "hide" ) ): ?>
					<div class="info_box" style="margin-top: 5px;">Reminder: Offline chat icon is not displayed based on the current <a href="icons.php?ses=<?php echo $ses ?>&deptid=<?php echo $deptid ?>&jump=options">offline setting</a>.</div>
					<?php else: ?>
					<div style="margin-top: 5px;" id="output_code"><?php echo preg_replace( "/%%text_string%%/", "", $code_html ) ?></div>
					<?php endif; ?><p>
				</div>
				<div style="" class="td_dept_td">
					<div style="font-size: 14px; font-weight: bold;"><img src="../pics/icons/code.png" width="14" height="14" border="0" alt=""> Text Link HTML Code &nbsp; <span style="font-size: 12px; font-weight: normal;"><a href="JavaScript:void(0)" onClick="$('#div_code_text').toggle()">[+] expand</a></span></div>
					<div id="div_code_text" style="display: none; margin-top: 15px;">
					<?php if ( $offline_option == "hide" ): ?>
						Not available for current <a href="icons.php?ses=<?php echo $ses ?>&deptid=<?php echo $deptid ?>&jump=options">offline setting</a>.
					<?php else: ?>
						<div style="margin-top: 15px;"><textarea wrap="virtual" id="textarea_code_text" style="padding: 20px; width: 930px; height: 100px; resize: none;" onMouseDown="setTimeout(function(){ $('#textarea_code_text').select(); }, 200);" readonly></textarea></div>

						<div style="margin-top: 10px;"><input type="text" class="input" size="25" maxlength="155" id="code_text" onKeydown="input_text_listen_text(event);" value="Click for Live Support"> <input type="button" value="Generate" onClick="populate_code('text')" id="btn_generate"></div>
						<div style="margin-top: 10px;">Example: <i>"Click for Live Support"</i></div>
						<div id="code_text_code" style="display: none; margin-top: 10px; font-size: 10px;">The above code will produce the following text link.</div>
						<div id="html_code_text_output" style="margin-top: 5px; color: #0000FF; text-decoration: underline;"></div>
						<div class="info_box" style="display: none; margin-top: 15px;" id="html_code_text_output_tip">
							To achieve design consistency of your website, modify the &lt;span&gt; style portion of the above code.
						</div>
					<?php endif ; ?>
					</div>
				</div>
				<div style="" class="td_dept_td">
					<div style="font-size: 14px; font-weight: bold;"><img src="../pics/icons/code.png" width="14" height="14" border="0" alt=""> Plain HTML Code (no JavaScript) &nbsp; <span style="font-size: 12px; font-weight: normal;"><a href="JavaScript:void(0)" onClick="$('#div_plain_text').toggle()">[+] expand</a></span></div>
					<div id="div_plain_text" style="display: none; margin-top: 15px;">
						<textarea wrap="virtual" id="textarea_code_plain" style="padding: 20px; width: 930px; height: 50px; resize: none;" onMouseDown="setTimeout(function(){ $('#textarea_code_plain').select(); }, 200);" readonly><a href="<?php echo $base_url ?>/phplive.php?d=<?php echo $deptid ?>&onpage=livechatimagelink&title=Live+Chat+Image+Link" target="new"><img src="<?php echo $base_url ?>/ajax/image.php?d=<?php echo $deptid ?>" border=0></a></textarea>

						<div style="margin-top: 10px;">
							<img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> Plain HTML is for publishing software with strict HTML guidelines.  The above code will work for most publishing software by inserting the code as HTML.
						</div>
						<div style="margin-top: 10px;">
							<img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> Due to the lack of javascript, automatic chat invite, initiate chat and real-time visitor footprint tracking will not be available for this code option.
						</div>
						<div style="margin-top: 10px;">
							<img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> Due to the lack of javascript, the traffic monitor will not display visitors arriving from this code option.  Also, the visitor footprints will not be tracked or stored.
						</div>
						<div style="margin-top: 10px; font-size: 10px;">The above code will produce the below status icon.</div>
						<div style="margin-top: 5px;"><a href="<?php echo $base_url ?>/phplive.php?d=<?php echo $deptid ?>&onpage=livechatimagelink&title=Live+Chat+Image+Link" target="new"><img src="<?php echo $base_url ?>/ajax/image.php?d=<?php echo $deptid ?>" border=0></a></div>
					</div>
				</div>
			</div>
		</div>

		<div id="code_check" style="display: none; margin-top: 25px;">
			Real-time chat icon status check will monitor the online/offline status icon every <?php echo $VARS_JS_ICON_CHECK ?> seconds while the visitor is on the website.  The status icon will dynamically toggle the online/offline status image without the visitor having to refresh or browse to another page.

			<div style="margin-top: 10px;"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> For websites receiving 500+ visitors <i>an hour</i>, we recommend turning this feature <b>Off</b> to save on server resources.</div>

			<div style="margin-top: 25px;">
				<div class="li_op round"><input type="radio" name="icon_check" id="icon_check_on" value="on" onClick="confirm_change('on')" <?php echo $icon_check_on ?>> On</div>
				<div class="li_op round"><input type="radio" name="icon_check" id="icon_check_off" value="off" onClick="confirm_change('off')" <?php echo $icon_check_off ?>> Off</div>
				<div style="clear: both;"></div>
			</div>
		</div>

		<div id="code_auto" style="display: none; margin-top: 25px;">

			<form method="POST" action="code.php?submit" enctype="multipart/form-data">
			<input type="hidden" name="action" value="update">
			<input type="hidden" name="ses" value="<?php echo $ses ?>">
			<div style="margin-top: 25px;">
				
				<table cellspacing=0 cellpadding=2 border=0 style="margin-top: 25px;">
				<tr>
					<td valign="top">
						<div>Current chat invitation image:</div>
						<div style="margin-top: 5px;"><img src="<?php echo Util_Upload_GetInitiate( $CONF["BASE_URL"], 0 ) ; ?>" width="250" height="160" border="0" alt="" class="round"></div>
						<div class="home_box_header" style="margin-top: 15px;">Update New Initiate Chat Image</div>
						<div style="margin-top: 10px;">
							<input type="file" name="icon_initiate" size="30"><p>
							<input type="submit" value="Upload Image" style="margin-top: 10px;" class="btn">
						</div>
					</td>
					<td valign="top">
						<div style="padding-left: 30px;">
							<div style="">
								<table cellspacing=0 cellpadding=0 border=0>
								<tr>
									<td>
										<div class="info_info" style="">
											<div>On webpages with the <a href="JavaScript:void(0)" onClick="show_div('main')">HTML Code</a>, automatically display a chat invitation to the visitor when certain criterias are met and when live chat is available (online).</div>
											<div style="margin-top: 15px;">Set a criteria to BLANK to inactivate. Set both criterias to BLANK to switch off the automatic chat invitation.</div>
										</div>
									</td>
								</tr>
								<tr>
									<td class="td_dept_td">
										Display the initiate chat when the visitor remains on the same page for
										<select name="duration">
										<?php
											$options = Array( "", 15, 30, 60, 90, 120, 150, 180 ) ;
											for( $c = 0; $c < count( $options ); ++$c )
											{
												$selected = "" ;
												if ( isset( $initiate["duration"] ) && ( $initiate["duration"] == $options[$c] ) )
													$selected = "selected" ;

												print "<option value='$options[$c]' $selected>$options[$c]</option>" ;
											}
										?></select> seconds <select name="andor" onChange="toggle_select_footprints(this.value)"><option value="1" <?php echo ( isset( $initiate["andor"] ) && ( $initiate["andor"] == 1 ) ) ? "selected" : "" ?> >or</option><option value="2" <?php echo ( isset( $initiate["andor"] ) && ( $initiate["andor"] == 2 ) ) ? "selected" : "" ?> >and</option></select> <span id="div_footprints">has viewed at least
										<select name="footprints"><option value="0"></option>
										<?php
											for( $c = 2; $c <= 25; ++$c )
											{
												$selected = "" ;
												if ( isset( $initiate["footprints"] ) && ( $initiate["footprints"] == $c ) )
													$selected = "selected" ;

												print "<option value=\"$c\" $selected>$c</option>" ;
											}
										?></select> pages (NOTE: viewing the same page 2 times would count as 2 pages).</span>
									</td>
								</tr>
								<tr>
									<td class="td_dept_td">
										After the automatic chat invite has been displayed, reset the invite settings and display the invitation to the visitor again after
										<select name="reset">
										<?php
											for( $c = 1; $c <= 30; ++$c )
											{
												$selected = "" ;
												if ( isset( $initiate["reset"] ) && ( $initiate["reset"] == $c ) )
													$selected = "selected" ;
												print "<option value=\"$c\" $selected>$c</option>" ;
											}
										?></select> day(s).  <img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> When the visitor requests live chat, their chat invite reset timer will be reset.

										<div class="info_box" style="margin-top: 15px;">
											<div>
												Initiate chat icon position:
												<select name="pos" id="pos" style="background: #D5E6FD;">
													<option value="1" <?php echo ( !isset( $initiate["pos"] ) || ( $initiate["pos"] == 1 ) ) ? "selected" : "" ; ?> >Hover in from the Left (default)</option>
													<option value="2" <?php echo ( isset( $initiate["pos"] ) && ( $initiate["pos"] == 2 ) ) ? "selected" : "" ; ?> >Hover in from the Right</option>
													<option value="3" <?php echo ( isset( $initiate["pos"] ) && ( $initiate["pos"] == 3 ) ) ? "selected" : "" ; ?> >Hover in from the Bottom Left</option>
													<option value="4" <?php echo ( isset( $initiate["pos"] ) && ( $initiate["pos"] == 4 ) ) ? "selected" : "" ; ?> >Hover in from the Bottom Right</option>
													<option value="100" <?php echo ( isset( $initiate["pos"] ) && ( $initiate["pos"] == 100 ) ) ? "selected" : "" ; ?> >Display at the Center of the page</option>
												</select>

												&bull; <a href="JavaScript:void(0)" onClick="view_invite()">view how it looks</a>
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td class="td_dept_td">
										<div>Exclude automatic chat invitation for URLs that contain:</div>
										<div style="margin-top: 5px; color: #5F7AAB;">
											* Include the page itself and not the full URL. Example: <code>purchase.php,checkout.html,trial.asp</code><br>
											* Separate the values with a comma (,) without spaces<br>
											* Quotes ("), forward slash (/), back slash (\) and other special characters will be omitted.
										</div>
										<div style="margin-top: 5px;"><input type="text" class="input" size="80" name="exclude" id="exclude" onKeyPress="return noquotes(event)" value="<?php echo ( isset( $initiate["exclude"] ) && $initiate["exclude"] ) ? $initiate["exclude"] : "" ; ?>"></div>
									</td>
								</tr>
								<tr>
									<td class="td_dept_td">
										<div style="margin-top: 15px;"><input type="submit" value="Update" class="btn"></div>
									</td>
								</tr>
								</table>
							</div>
						</div>
					</td>
				</tr>
				</table>

			</div>
			</form>
		</div>
		<?php endif ; ?>

<?php include_once( "./inc_footer.php" ) ?>