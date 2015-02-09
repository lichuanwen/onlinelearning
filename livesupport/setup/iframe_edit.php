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

	$error = $sub = "" ;

	include_once( "../API/Ops/get.php" ) ;
	include_once( "../API/Depts/get.php" ) ;

	$page = ( Util_Format_Sanatize( Util_Format_GetVar( "page" ), "ln" ) ) ? Util_Format_Sanatize( Util_Format_GetVar( "page" ), "ln" ) : 0 ;
	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$option = Util_Format_Sanatize( Util_Format_GetVar( "option" ), "ln" ) ;
	$sub = Util_Format_Sanatize( Util_Format_GetVar( "sub" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "ln" ) ;

	$departments = Depts_get_AllDepts( $dbh ) ;

	if ( $action == "update" )
	{
		include_once( "../API/Depts/update.php" ) ;

		$copy_all = Util_Format_Sanatize( Util_Format_GetVar( "copy_all" ), "ln" ) ;
		$message = preg_replace( "/<script(.*?)<\/script>/i", "", Util_Format_Sanatize( Util_Format_GetVar( "message" ), "" ) ) ;
		$message_busy = preg_replace( "/<script(.*?)<\/script>/i", "", Util_Format_Sanatize( Util_Format_GetVar( "message_busy" ), "" ) ) ;

		if ( ( $sub == "greeting" ) || ( $sub == "offline" ) || ( $sub == "temail" ) )
		{
			$table_name = "" ;
			if ( $sub == "greeting" ) { $table_name = "msg_greet" ; }
			else if ( $sub == "offline" ) { $table_name = "msg_offline" ; }
			else if ( $sub == "temail" ) { $table_name = "msg_email" ; }

			if ( !$message )
				$error = "Blank input is invalid.  Message has been reset." ;
			else if ( ( $sub == "offline" ) && ( !$message_busy ) )
				$error = "Blank input is invalid.  Message has been reset." ;
			else if ( $message && $table_name )
			{
				if ( $copy_all )
				{
					for( $c = 0; $c < count( $departments ); ++$c )
					{
						Depts_update_UserDeptValue( $dbh, $departments[$c]["deptID"], $table_name, $message ) ;
						if ( $sub == "offline" ) { Depts_update_UserDeptValue( $dbh, $departments[$c]["deptID"], "msg_busy", $message_busy ) ; }
					}
				}
				else
				{
					Depts_update_UserDeptValue( $dbh, $deptid, $table_name, $message ) ;
					if ( $sub == "offline" ) { Depts_update_UserDeptValue( $dbh, $deptid, "msg_busy", $message_busy ) ; }
				}
			}
			else
				$error = "Invalid action: $sub" ;
		}
		else if ( $sub == "settings" )
		{
			$temail = Util_Format_Sanatize( Util_Format_GetVar( "temail" ), "ln" ) ;
			$temaild = Util_Format_Sanatize( Util_Format_GetVar( "temaild" ), "ln" ) ;

			if ( $copy_all )
			{
				for( $c = 0; $c < count( $departments ); ++$c )
					Depts_update_UserDeptValues( $dbh, $departments[$c]["deptID"], "temail", $temail, "temaild", $temaild ) ;
			}
			else
				Depts_update_UserDeptValues( $dbh, $deptid, "temail", $temail, "temaild", $temaild ) ;
		}
		else if ( $sub == "custom" )
		{
			$remail = Util_Format_Sanatize( Util_Format_GetVar( "remail" ), "ln" ) ;
			$custom_field = preg_replace( "/'/", "", preg_replace( "/\"/", "", Util_Format_Sanatize( Util_Format_GetVar( "custom_field" ), "notags" ) ) ) ;
			$custom_field_required = Util_Format_Sanatize( Util_Format_GetVar( "custom_field_required" ), "ln" ) ;

			$custom_array = ( !$custom_field ) ? serialize( Array() ) : serialize( Array( "$custom_field", $custom_field_required ) ) ;

			if ( $copy_all )
			{
				for( $c = 0; $c < count( $departments ); ++$c )
				{
					Depts_update_UserDeptValue(  $dbh, $departments[$c]["deptID"], "remail", $remail ) ;
					Depts_update_UserDeptValue(  $dbh, $departments[$c]["deptID"], "custom", $custom_array ) ;
				}
			}
			else
			{
				Depts_update_UserDeptValue( $dbh, $deptid, "remail", $remail ) ;
				Depts_update_UserDeptValue( $dbh, $deptid, "custom", $custom_array ) ;
			}
		}
		else if ( $sub == "canned" )
		{
			include_once( "../API/Canned/get.php" ) ;
			include_once( "../API/Canned/put.php" ) ;

			$canid = Util_Format_Sanatize( Util_Format_GetVar( "canid" ), "ln" ) ;
			$title = Util_Format_Sanatize( Util_Format_GetVar( "title" ), "ln" ) ;
			$message = Util_Format_ConvertQuotes( Util_Format_Sanatize( Util_Format_GetVar( "message" ), "htmltags" ) ) ;
			$sub_deptid = Util_Format_Sanatize( Util_Format_GetVar( "sub_deptid" ), "ln" ) ;

			$caninfo = Canned_get_CanInfo( $dbh, $canid ) ;
			if ( isset( $caninfo["opID"] ) )
				$opid = $caninfo["opID"] ;
			else
				$opid = 1111111111 ;

			if ( !Canned_put_Canned( $dbh, $canid, $opid, $deptid, $title, $message ) )
				$error = "Error processing canned message." ;
			$deptid = $sub_deptid ;
		}
		$action = $sub ;
	}
	else if ( $action == "delete" )
	{
		include_once( "../API/Canned/get.php" ) ;
		include_once( "../API/Canned/remove.php" ) ;

		$canid = Util_Format_Sanatize( Util_Format_GetVar( "canid" ), "ln" ) ;

		$caninfo = Canned_get_CanInfo( $dbh, $canid ) ;
		Canned_remove_Canned( $dbh, $caninfo["opID"], $canid ) ;
		$action = $sub ;
	}
	
	$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
	include_once( "../lang_packs/$deptinfo[lang].php" ) ;
	$deptname = $deptinfo["name"] ;

	switch ( $action )
	{
		case "greeting":
			$message = $deptinfo["msg_greet"] ;
			break ;
		case "offline":
			$message = $deptinfo["msg_offline"] ;
			break ;
		case "temail":
			$message = $deptinfo["msg_email"] ;
			break ;
		default:
			break ;
	}

	include_once( "../lang_packs/$deptinfo[lang].php" ) ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> PHP Live! Support <?php echo $VERSION ?> </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=<?php echo $LANG["CHARSET"] ?>"> 

<link rel="Stylesheet" href="../css/setup.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var winname = unixtime() ;
	var option = <?php echo $option ?> ; // used to communicate with depts.php to toggle iframe

	$(document).ready(function()
	{
		$("body").css({'background-color': '#FFFFFF'}) ;

		<?php if ( $action == "offline" ): ?>$('#div_offline_busy').show() ;<?php endif ; ?>

		<?php if ( $sub && !$error ): ?>parent.do_alert( 1, "Success" ) ;<?php elseif ( $error ): ?>parent.do_alert( 0, "<?php echo $error ?>" ) ;<?php endif ; ?>
	});

	function do_edit( thecanid, thetitle, thedeptid, themessage )
	{
		$( "input#canid" ).val( thecanid ) ;
		$( "input#title" ).val( thetitle.replace( /&-#39;/g, "'" ) ) ;
		$( "#deptid" ).val( thedeptid ) ;
		$( "#message" ).val( themessage.replace(/<br>/g, "\r\n").replace( /&-#39;/g, "'" ) ) ;
		
		toggle_new(0) ;
	}

	function toggle_new( theflag )
	{
		// theflag = 1 means force show, not toggle
		if ( $('#canned_box_new').is(':visible') && !theflag )
		{
			$( "input#canid" ).val( "0" ) ;
			$( "input#title" ).val( "" ) ;
			$( "#deptid" ).val( <?php echo $deptid ?> ) ;
			$( "#message" ).val( "" ) ;

			$('#canned_box_new').fadeOut("fast", function() { $('#canned_list').fadeIn("fast") ; }) ;
		}
		else
		{
			$('#canned_list').fadeOut("fast", function() { $('#canned_box_new').fadeIn("fast") ; }) ;
		}

		$('#title').focus() ;
	}

	function do_delete( thecanid )
	{
		var unique = unixtime() ;

		if ( confirm( "Really delete this canned response?" ) )
			location.href = "iframe_edit.php?ses=<?php echo $ses ?>&action=delete&sub=canned&deptid=<?php echo $deptid ?>&canid="+thecanid+"&"+unique ;
	}

	function do_submit()
	{
		var canid = $('#canid').val() ;
		var title = $('#title').val() ;
		var deptid = $('#deptid').val() ;
		var message = $('#message').val() ;

		if ( title == "" )
			parent.do_alert( 0, "Please provide a title." ) ;
		else if ( message == "" )
			parent.do_alert( 0, "Please provide a message." ) ;
		else
			$('#theform').submit() ;
	}

	function do_savem( theflag )
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		$.get("../ajax/setup_actions.php", { action: "update_savem", ses: '<?php echo $ses ?>', deptid: <?php echo $deptid ?>, savem: theflag, unique: unique },  function(data){
			eval( data ) ;

			if ( json_data.status )
				parent.do_alert( 1, "Success" ) ;
		});
	}

	function do_redirect( theaction )
	{
		if ( theaction == "settings" )
		{
			parent.do_options( 5, <?php echo $deptid ?> ) ;
		}
	}

//-->
</script>
</head>
<body>

<div id="iframe_body" style="height: 350px;">
	<?php if ( $action != "canned" ): ?>
	<form action="iframe_edit.php?submit" method="POST" accept-charset="<?php echo $LANG["CHARSET"] ?>">
	<input type="hidden" name="ses" value="<?php echo $ses ?>">
	<input type="hidden" name="action" value="update">
	<input type="hidden" name="sub" value="<?php echo $action ?>">
	<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
	<input type="hidden" name="option" value="<?php echo $option ?>">
	<div style="">
		<div style="">
			<div id="info_greeting" style="display: none;">
				<div style="">The below message will be displayed to the visitor while being connected with an operator.</div>
				<div style="margin-top: 10px;"><b>%%visitor%%</b> = visitor's name</div>
			</div>
			<div id="info_offline" style="display: none;">
				<div style=""><b>Standard Offline Message:</b> Display the following Offline Message when operators are offline.</div>
				<div style="margin-top: 10px;"></div>
			</div>
			<div id="info_temail" style="display: none;">
				<div style="">If <a href="JavaScript:void(0)" onClick="do_redirect('settings')">email transcript</a> is enabled, the following template will be used to generate the outgoing email transcript message sent to the visitor.</div>
			</div>
			<div id="info_smtp" style="display: none;">
				<div style="">As a default, the system will use the standard PHP mail() function with the web server mail settings to send out emails (transcripts, offline messages, etc).  However, if the department SMTP values are provided, emails will be sent using the external SMTP provider.</div>
			</div>
		</div>
		<div style="">
			<?php if ( $action == "temail" ): ?>
			<div style="padding-top: 15px; padding-bottom: 15px;">
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td valign="top" width="300">
						<textarea type="text" cols="50" rows="10" id="message" name="message"><?php echo preg_replace( "/\"/", "&quot;", $message ) ?></textarea>
					</td>
					<td><img src="../pics/space.gif" width="15" height=1></td>
					<td valign="center">
						<ul>
							<li style="margin-top: 5px;"> Dynamically populated variables:
								<ul style="margin-top: 10px;">
									<li> <span class="text_blue"><b>%%transcript%%</b></span> = the chat transcript
									<li> <span class="text_blue"><b>%%visitor%%</b></span> = visitor's name
									<li> <span class="text_blue"><b>%%operator%%</b></span> = operator name
									<li> <span class="text_blue"><b>%%op_email%%</b></span> = operator email
								</ul>
						</ul>
					</td>
				</tr>
				</table>
			</div>
			<?php elseif ( $action == "settings" ): ?>
			<div style="padding-bottom: 15px; text-align: justify;">
				<div style="float: left; min-height: 120px; width: 370px" class="info_info">
					<div>Provide visitors an option to receive the chat transcript by email when chat session ends.</div>
					<div style="margin-top: 10px;">
						<div class="li_op round"><input type="radio" name="temail" id="temail_1" value="1" checked> Yes </div>
						<div class="li_op round"><input type="radio" name="temail" id="temail_0" value="0"> No</div>
						<div style="clear: both;"></div>
					</div>
				</div>
				<div style="float: left; min-height: 120px; width: 370px;" class="info_info">
					<div>Send chat transcript copy to the department email address when chat session ends.</div>
					<div style="margin-top: 10px;">
						<div class="li_op round"><input type="radio" name="temaild" id="temaild_1" value="1" checked> Yes</div>
						<div class="li_op round"><input type="radio" name="temaild" id="temaild_0" value="0"> No</div>
						<div style="clear: both;"></div>
					</div>
				</div>
				<div style="clear: both;"></div>
			</div>
			<script language="JavaScript">
			<!--
				$( "input#temail_"+<?php echo $deptinfo["temail"] ?> ).attr( "checked", true ) ;
				$( "input#temaild_"+<?php echo $deptinfo["temaild"] ?> ).attr( "checked", true ) ;
			//-->
			</script>
			<?php
				elseif ( $action == "custom" ):
				$custom_field = ( $deptinfo["custom"] ) ? unserialize( $deptinfo["custom"] ) : Array() ;
			?>
			<div style="padding-bottom: 15px; text-align: justify;">
				<div style="float: left; min-height: 120px; width: 370px;" class="info_info">
					<div>Should the email address be required before starting a chat session?  Selecting "Yes" will set the email field as required.  Selecting "No" will set the email field as optional.</div>
					<div style="margin-top: 10px;">
						<div class="li_op round"><input type="radio" name="remail" id="remail_1" value="1" checked> Yes</div>
						<div class="li_op round"><input type="radio" name="remail" id="remail_0" value="0"> No</div>
						<div style="clear: both;"></div>
					</div>
				</div>
				<div style="float: left; min-height: 120px; width: 370px;" class="info_info">
					<div>Add a custom field on the chat request window.</div>
					<div style="margin-top: 10px; font-size: 10px;" class="info_box"><i>Current fields are Name, Email and Question.  Additional field to display to the visitor could be "Login", "Phone", "Order Number", etc.</i></div>
					<div style="margin-top: 10px;">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td><input type="text" class="input" size="15" maxlength="20" id="custom_field" name="custom_field" value="<?php echo isset( $custom_field[0] ) ? $custom_field[0] : "" ; ?>"></td>
							<td style="padding-left: 10px;"><select name="custom_field_required" class="select"><option value=1>required to chat</option><option value=0 <?php echo ( isset( $custom_field[1] ) && !$custom_field[1] ) ? "selected" : "" ; ?>>optional</option></select></td>
						</tr>
						</table>
					</div>
				</div>
				<div style="clear: both;"></div>
			</div>
			<script language="JavaScript">
			<!--
				$( "input#remail_"+<?php echo $deptinfo["remail"] ?> ).attr( "checked", true ) ;
			//-->
			</script>
			<?php elseif ( $action == "smtp" ): ?>
			<div style="padding-top: 15px; padding-bottom: 15px; text-align: justify;">
				<?php if ( is_file( "../addons/smtp/smtp.php" ) ): ?>
					<img src="../pics/icons/info.png" width="12" height="12" border="0" alt="enabled"> SMTP settings can be updated from the <a href="../addons/smtp/smtp.php?ses=<?php echo $ses ?>&deptid=<?php echo $deptid ?>" target="_parent">Extras-&gt;SMTP</a> area.
				<?php else: ?>
					<img src="../pics/icons/info.png" width="12" height="12" border="0" alt=""> Currently, the department emails are sent using the standard PHP mail() function with the web server mail settings.  To send emails using an external SMTP server, visit the <a href="http://www.phplivesupport.com/r.php?r=smtp" target="new">SMTP documentations</a> page for more information about the SMTP addon.
				<?php endif ; ?>
			</div>
			<?php else: ?>
			<div style="margin-top: 15px; padding-bottom: 15px;"><input type="text" style="width: 95%" id="message" name="message" maxlength="155" value="<?php echo preg_replace( "/\"/", "&quot;", $message ) ?>"></div>

			<div id="div_offline_busy" style="display: none;">
				<div style="margin-top: 15px;"><b>"Busy" Offline Message:</b> Display the following Offline Message when operators are online but were unable to accept the chat request.</div>
				<div style="margin-top: 15px; padding-bottom: 15px;"><input type="text" style="width: 95%" id="message_busy" name="message_busy" maxlength="155" value="<?php echo preg_replace( "/\"/", "&quot;", $deptinfo["msg_busy"] ) ?>"></div>
			</div>
			<?php endif ; ?>

			<?php if ( ( count( $departments ) > 1 ) && !preg_match( "/(smtp)/", $action ) ) : ?>
			<div style="padding-top: 5px;"><input type="checkbox" id="copy_all" name="copy_all" value=1> copy this update to all departments</div>
			<?php endif ; ?>

			<?php if ( !preg_match( "/(smtp)/", $action ) ): ?>
			<div style="padding-top: 5px;"><input type="submit" value="Update" class="btn"></div>
			<?php endif ; ?>
		</div>
	</div>
	</form>
	<script type="text/javascript">$('#info_<?php echo $action ?>').show();</script>

	<?php
		else:
		include_once( "../API/Canned/get.php" ) ;

		$departments = Depts_get_AllDepts( $dbh ) ;
		$operators = Ops_get_AllOps( $dbh ) ;

		// make hash for quick refrence
		$operators_hash = Array() ;
		$operators_hash[1111111111] = "<img src=\"../pics/icons/lock.png\" width=\"16\" height=\"16\" border=\"0\" title=\"created by setup admin\" title=\"created by setup admin\">" ;
		for ( $c = 0; $c < count( $operators ); ++$c )
		{
			$operator = $operators[$c] ;
			$operators_hash[$operator["opID"]] = $operator["name"] ;
		}

		// make hash for quick refrence
		$dept_hash = Array() ;
		$dept_hash[1111111111] = "All Departments" ;
		for ( $c = 0; $c < count( $departments ); ++$c )
		{
			$department = $departments[$c] ;
			$dept_hash[$department["deptID"]] = $department["name"] ;
		}

		$cans = Canned_get_DeptCanned( $dbh, $deptid, $page, 100 ) ;
	?>
	<div id="canned_list" style="overflow: auto;">
		<div id="div_new_canned_<?php echo $deptid ?>" class="info_box" style="width: 80px; margin-left: 10px; padding-left: 25px; background: url( ../pics/icons/add.png ) no-repeat #FAFAA6; background-position: 5px 5px; border-bottom: 0px solid; cursor: pointer; border-bottom-left-radius: 0px; -moz-border-radius-bottomleft: 0px; border-bottom-right-radius: 0px; -moz-border-radius-bottomright: 0px;" onClick="parent.new_canned( <?php echo $deptid ?> )">new canned</div>
		<table cellspacing=0 cellpadding=0 border=0 width="100%">
		<tr>
			<td width="18" nowrap><div class="td_dept_header">&nbsp;</div></td>
			<td width="120" nowrap><div class="td_dept_header">Title</div></td>
			<td width="80" nowrap><div class="td_dept_header">Operator</div></td>
			<td width="180"><div class="td_dept_header">Department</div></td>
			<td><div class="td_dept_header">Message</div></td>
		</tr>
		<?php
			for ( $c = 0; $c < count( $cans )-1; ++$c )
			{
				$can = $cans[$c] ;
				$title = preg_replace( "/\"/", "&quot;", preg_replace( "/'/", "&-#39;", $can["title"] ) ) ;
				$title_display = preg_replace( "/\"/", "&quot;", $can["title"] ) ;

				$op_name = $operators_hash[$can["opID"]] ;
				$dept_name = $dept_hash[$can["deptID"]] ;
				$message = preg_replace( "/\"/", "&quot;", preg_replace( "/'/", "&-#39;", preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", $can["message"] ) ) ) ;
				$message_display = preg_replace( "/\"/", "&quot;", preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", Util_Format_ConvertTags( $can["message"] ) ) ) ;
				//$message_display = preg_replace( "/url:(.*?)($| |)/", "[url link]", $message_display ) ;
				//$message_display = preg_replace( "/image:(.*?)($| |)/", "[image link]", $message_display ) ;

				$td1 = "td_dept_td" ;

				print "<tr><td class=\"$td1\" nowrap><div onClick=\"do_edit($can[canID], '$title', '$can[deptID]', '$message')\" style=\"cursor: pointer;\"><img src=\"../pics/btn_edit.png\" width=\"45\" height=\"16\" border=\"0\" alt=\"\"></div><div onClick=\"do_delete($can[canID])\" style=\"margin-top: 5px; cursor: pointer;\"><img src=\"../pics/btn_delete.png\" width=\"45\" height=\"16\" border=\"0\" alt=\"\"></div></td><td class=\"$td1\"><b>$title_display</b></td><td class=\"$td1\" nowrap>$op_name</td><td class=\"$td1\">$dept_name</td><td class=\"$td1\">$message_display</td></tr>" ;
			}
			if ( $c == 0 )
				print "<tr><td colspan=7 class=\"td_dept_td\">blank results</td></tr>" ;
		?>
		</table>
		<div class="chat_info_end"></div>
	</div>

	<div id="canned_box_new" style="display: none; padding: 5px; z-Index: 5;">
		<form method="POST" action="iframe_edit.php?<?php echo time() ?>" id="theform" accept-charset="<?php echo $LANG["CHARSET"] ?>">
		<table cellspacing=0 cellpadding=0 border=0 width="100%">
		<tr>
			<td valign="top" width="380" style="padding: 5px;">
				<input type="hidden" name="ses" value="<?php echo $ses ?>">
				<input type="hidden" name="action" value="update">
				<input type="hidden" name="sub" value="<?php echo $action ?>">
				<input type="hidden" name="sub_deptid" value="<?php echo $deptid ?>">
				<input type="hidden" name="option" value="<?php echo $option ?>">
				<input type="hidden" name="canid" id="canid" value="0">
				<div>
					Reference (example: "Welcome greeting", "Just a moment")<br>
					<input type="text" name="title" id="title" class="input" style="width: 98%; margin-bottom: 10px;" maxlength="25">
					Department<br>
					<select name="deptid" id="deptid" style="width: 99%; margin-bottom: 10px;">
					<option value="1111111111">All Departments</option>
					<?php
						for ( $c = 0; $c < count( $departments ); ++$c )
						{
							$department = $departments[$c] ;
							$selected = ( $department["deptID"] == $deptid ) ? "selected" : "" ;

							print "<option value=\"$department[deptID]\" $selected>$department[name]</option>" ;
						}
					?>
					</select>
					Canned Message<br>
					<textarea name="message" id="message" class="input" rows="5" style="width: 98%; margin-bottom: 10px;" wrap="virtual"></textarea>

					<button type="button" onClick="do_submit()" class="btn">Submit</button> &nbsp; <a href="JavaScript:void(0)" onClick="toggle_new(0)">cancel</a>
				</div>
			</td>
			<td><img src="../pics/space.gif" width="15" height=1></td>
			<td valign="center">
				<ul>
					<li> Canned messages should not contain HTML.
					<li style="margin-top: 5px;"> Dynamically populated variables:
						<ul style="margin-top: 10px;">
							<li> <span class="text_blue"><b>%%visitor%%</b></span> = visitor's name
							<li> <span class="text_blue"><b>%%operator%%</b></span> = your name
							<li> <span class="text_blue"><b>%%op_email%%</b></span> = your email

							<li style="padding-top: 5px;"> <span class="text_blue"><b>email:</b><i>someone@somewhere.com</i> - link an email</span>
							<li> <span class="text_blue"><b>image:</b><i>http://www.someurl.com/image.gif</i> - display an image</span>

							<div style="margin-top: 15px;"><img src="../pics/icons/info.png" width="12" height="12" border="0" alt=""> Example:<br>Hi %%visitor%%!  Our contact email address is email:contact@yourdomain.com</div>
						</ul>
				</ul>
			</td>
		</tr>
		</table>
		</form>
	</div>

	<?php endif ; ?>
</div>

</body>
</html>
