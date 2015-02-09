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

	include_once( "../API/Util_Functions.php" ) ;
	include_once( "../API/Depts/get.php" ) ;
	include_once( "../API/Ops/get.php" ) ;
	include_once( "../API/Messages/get.php" ) ;

	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "ln" ) ;
	$page = ( Util_Format_Sanatize( Util_Format_GetVar( "page" ), "ln" ) ) ? Util_Format_Sanatize( Util_Format_GetVar( "page" ), "ln" ) : 0 ;
	$index = ( Util_Format_Sanatize( Util_Format_GetVar( "index" ), "ln" ) ) ? Util_Format_Sanatize( Util_Format_GetVar( "index" ), "ln" ) : 0 ;

	$departments = Depts_get_AllDepts( $dbh ) ;
	$deptinfo = Array() ;
	if ( $deptid )
		$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
	$operators = Ops_get_AllOps( $dbh ) ;

	// make hash for quick refrence
	$dept_hash = Array() ;
	for ( $c = 0; $c < count( $departments ); ++$c )
	{
		$department = $departments[$c] ;
		$dept_hash[$department["deptID"]] = $department["name"] ;
	}

	$messages = Messages_get_Messages( $dbh, $deptid, $page, 15 ) ;
	$total = Messages_get_TotalMessages( $dbh, $deptid ) ;

	$pages = Util_Functions_Page( $page, $index, 15, $total, "reports_chat_msg.php", "ses=$ses&deptid=$deptid" ) ;
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
	var global_messageid ;
	var global_savem = <?php echo isset( $deptinfo["deptID"] ) ? $deptinfo["savem"] : 0 ; ?> ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "rchats" ) ;
	});

	function open_message( themessageid, thestatus )
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		global_messageid = themessageid ;

		$( '*', '#messages' ).each( function(){
			var div_name = $( this ).attr('id') ;
			if ( div_name.indexOf("img_") != -1 )
				$(this).css({ 'opacity': 1 }) ;
		} );

		$('#img_'+themessageid).css({ 'opacity': '0.4' }) ;

		$('#div_message_body').hide() ;
		$('#div_loading').show() ;
		$('#div_message').fadeIn("fast") ;

		$.post("../ajax/setup_actions.php", { action: "fetch_message", ses: '<?php echo $ses ?>', messageid: themessageid, unique: unique },  function(data){
			eval( data ) ;

			if ( json_data.status )
			{
				$('#flag_'+themessageid).html( "<img src=\"../pics/icons/space.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"\">" ) ;

				$('#msg_subject').html( json_data.subject ) ;
				$('#msg_from_name').html( json_data.name ) ;
				$('#msg_from_email').html( "<a href=\"mailto:"+json_data.email+"\">"+json_data.email+"</a>" ) ;
				$('#msg_to').html( json_data.to ) ;
				$('#msg_created').html( json_data.created ) ;
				$('#msg_message').html( json_data.message ) ;

				$('#div_loading').hide() ;
				$('#div_message_body').show() ;
			}
		});
	}

	function close_message()
	{
		$('#div_message').fadeOut("fast") ;
	}

	function delete_message()
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		if ( global_messageid && confirm( "Really delete this message?" ) )
		{
			$.post("../ajax/setup_actions.php", { action: "delete_message", ses: '<?php echo $ses ?>', messageid: global_messageid, unique: unique },  function(data){
				eval( data ) ;

				if ( json_data.status )
				{
					$('#div_message').fadeOut('fast', function() {
						setTimeout( function() { $('#tr_'+global_messageid).remove() ; }, 500 ) ;
					});
				}
				else
					do_alert( "Message could not be deleted." ) ;
			});
		}
	}

	function lock_message( theobject, themessageid )
	{
		var unique = unixtime() ;
		var json_data = new Object ;
		var lock = ( theobject.checked ) ? 1 : 0 ;

		$.post("../ajax/setup_actions.php", { action: "lock_message", ses: '<?php echo $ses ?>', messageid: themessageid, lock: lock, unique: unique },  function(data){
			eval( data ) ;

			if ( json_data.status )
			{
				if ( lock )
					$('#flag_'+themessageid).html( "<img src=\"../pics/icons/lock.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\">" ) ;
				else
					$('#flag_'+themessageid).html( "<img src=\"../pics/icons/space.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"\">" ) ;
			}
		});
	}

	function switch_dept( theobject )
	{
		location.href = "reports_chat_msg.php?ses=<?php echo $ses ?>&deptid="+theobject.value+"&"+unixtime() ;
	}

	function do_savem( thevalue )
	{
		if ( confirm( "Update automatic delete setting?" ) )
		{
			$.ajax({
				type: "POST",
				url: "../ajax/setup_actions.php",
				data: "ses=<?php echo $ses ?>&action=update_savem&deptid=<?php echo $deptid ?>&savem="+thevalue+"&"+unixtime(),
				success: function(data){
					global_savem = thevalue ;
					do_alert( 1, "Success!" ) ;
				}
			});
		}
		else
			$('#savem_'+global_savem).attr('checked', true) ;
	}

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu" onClick="location.href='reports_chat.php?ses=<?php echo $ses ?>'">Chat Reports</div>
			<div class="op_submenu" onClick="location.href='reports_chat_active.php?ses=<?php echo $ses ?>'">Active Chats</div>
			<div class="op_submenu_focus">Offline Messages</div>
			<div style="clear: both"></div>
		</div>

		<div style="margin-top: 25px;">
			<form method="POST" action="reports_chat_active.php?submit" id="form_theform">
			<table cellspacing=0 cellpadding=0 border=0 width="100%">
			<tr>
				<td>
					<select name="deptid" id="deptid" style="font-size: 16px; background: #D4FFD4; color: #009000;" OnChange="switch_dept( this )">
					<option value="0">All Departments</option>
					<?php
						$ops_assigned = 0 ;
						for ( $c = 0; $c < count( $departments ); ++$c )
						{
							$department = $departments[$c] ;
							$ops = Depts_get_DeptOps( $dbh, $department["deptID"] ) ;
							if ( count( $ops ) )
								$ops_assigned = 1 ;

							if ( $department["name"] != "Archive" )
							{
								$selected = ( $deptid == $department["deptID"] ) ? "selected" : "" ;
								print "<option value=\"$department[deptID]\" $selected>$department[name]</option>" ;
							}
						}
					?>
					</select>
				</td>
				<td style="padding-left: 25px;">
					All messages were automatically emailed to the <a href="depts.php?ses=<?php echo $ses ?>">department</a> email address.  These are the saved copies of the messages.
				</td>
			</tr>
			<tr>
				<td colspan=2 style="padding-top: 15px;">
					<div class="info_box" style="">
						<?php
							if ( isset( $deptinfo["deptID"] ) ):
						?>
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td><img src="../pics/icons/arrow_grey.png" width="12" height="12" border="0" alt=""> Automatically delete department saved offline messages that were created over &nbsp; </td>
							<td>
								<div class="li_op round"><input type="radio" id="savem_1" name="savem" value=1 onClick="do_savem(this.value)" <?php echo ( $deptinfo["savem"] == 1 ) ? "checked" : "" ?>> 1 month</div>
								<div class="li_op round"><input type="radio" id="savem_3" name="savem" value=3 onClick="do_savem(this.value)" <?php echo ( $deptinfo["savem"] == 3 ) ? "checked" : "" ?>> 3 months</div>
								<div class="li_op round"><input type="radio" id="savem_6" name="savem" value=6 onClick="do_savem(this.value)" <?php echo ( $deptinfo["savem"] == 6 ) ? "checked" : "" ?>> 6 months</div>
								<div class="li_op round"><input type="radio" id="savem_12" name="savem" value=12 onClick="do_savem(this.value)" <?php echo ( $deptinfo["savem"] == 12 ) ? "checked" : "" ?>> 1 year</div>
								<div class="li_op round"><input type="radio" id="savem_0" name="savem" value=0 onClick="do_savem(this.value)" <?php echo ( $deptinfo["savem"] == 0 ) ? "checked" : "" ?>> do not delete</div>
								<div style="clear: both;"></div>
							</td>
							<td>ago</td>
						</tr>
						</table>
						<?php else: ?>
						<img src="../pics/icons/arrow_grey_top.png" width="12" height="12" border="0" alt=""> Select a department for automatic offline message delete setting.  This value should be set if conserving disk space is important.
						<?php endif ; ?>
					</div>
				</td>
			</tr>
			</table>
			</form>
		</div>

		<table cellspacing=0 cellpadding=0 border=0 width="100%" style="margin-top: 25px;" id="messages">
		<tr><td colspan="10"><div class="page_top_wrapper"><?php echo $pages ?></div></td></tr>
		<tr>
			<td width="20" nowrap><div class="td_dept_header">&nbsp;</div></td>
			<td width="20" nowrap><div class="td_dept_header">&nbsp;</div></td>
			<td width="80" nowrap><div class="td_dept_header">Name</div></td>
			<td width="80" nowrap><div class="td_dept_header">Email</div></td>
			<td width="140"><div class="td_dept_header">Created</div></td>
			<td width="80" nowrap><div class="td_dept_header">Department</div></td>
			<td width="80"><div class="td_dept_header">Footprints</div></td>
			<td><div class="td_dept_header">Subject</div></td>
		</tr>
		<?php
			for ( $c = 0; $c < count( $messages ); ++$c )
			{
				$message = $messages[$c] ;

				$visitor = $message["vname"] ;
				$department = isset( $dept_hash[$message["deptID"]] ) ? $dept_hash[$message["deptID"]] : "&nbsp;" ;
				$created = date( "M j (g:i:s a)", $message["created"] ) ;
				$ip = $message["ip"] ;
				$subject = $message["subject"] ;

				$flag = ( $message["status"] ) ? "space.gif" : "flag_blue.png" ;
				if ( $message["locked"] ){ $flag = "lock.png" ; }
				$flag_tooltip = ( $message["status"] ) ? "" : "title=\"unread\" alt=\"unread\"" ;
				$chat = ( $message["chat"] ) ? "chat.png" : "space.gif" ;

				$btn_view = "<div onClick=\"open_message('$message[messageID]', $message[status])\" style=\"cursor: pointer;\" id=\"img_$message[messageID]\"><img src=\"../pics/btn_view.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></div>" ;

				$td1 = "td_dept_td" ;

				print "<tr id=\"tr_$message[messageID]\"><td class=\"$td1\"><div id=\"flag_$message[messageID]\"><img src=\"../pics/icons/$flag\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" $flag_tooltip></div></td><td class=\"$td1\">$btn_view</td><td class=\"$td1\">$visitor</td><td class=\"$td1\" nowrap><a href=\"mailto:$message[vemail]\">$message[vemail]</a></td><td class=\"$td1\" nowrap>$created</td><td class=\"$td1\">$department</td><td class=\"$td1\" nowrap>$message[footprints]</td><td class=\"$td1\"><div id=\"div_$message[messageID]\">$message[subject]</div></td></tr>" ;
			}
			if ( $c == 0 )
				print "<tr><td colspan=8 class=\"td_dept_td\">blank results</td></tr>" ;
		?>
		</table>

		<div id="div_message" style="display: none; position: fixed; top: 0px; left: 0px; width: 100%; height: 100%; background: url( ../pics/bg_trans_white.png ) repeat; overflow: hidden; z-index: 1001;">
			<div style="width: 700px; margin: 0 auto; margin-top: 50px;" class="info_info">
				<div id="div_message_body" class="round">
					<div>
						<table cellspacing=2 cellpadding=2 border=0 width="100%">
						<tr>
							<td width="100" align="right"><div style="background: #EAEAEA;" class="td_dept_td_blank round">Subject</div></td>
							<td><div class="td_dept_td_blank" id="msg_subject" style="font-weight: bold;"></div></td>
						</tr>
						<tr>
							<td width="100" align="right"><div style="background: #EAEAEA;" class="td_dept_td_blank round">From</div></td>
							<td><div class="td_dept_td_blank"><span id="msg_from_name"></span> &lt;<span id="msg_from_email"></span>&gt;</div></td>
						</tr>
						<tr>
							<td width="100" align="right"><div style="background: #EAEAEA;" class="td_dept_td_blank round">To</div></td>
							<td><div class="td_dept_td_blank" id="msg_to"></div></td>
						</tr>
						<tr>
							<td width="100" align="right" valign="top">
								<div style="background: #EAEAEA;" class="td_dept_td_blank round">Message</div>
							</td>
							<td valign="top">
								<div class="td_dept_td_blank">
									<div id="msg_created" style="font-size: 10px;"></div>
									<div id="msg_message" style="margin-top: 15px; height: 180px; overflow: auto;"></div>
								</div>
							</td>
						</tr>
						<tr>
							<td><button type="button" onClick="close_message()">close window</button></td>
							<td align="right"><div onClick="delete_message()" style="margin-top: 15px; cursor: pointer;"><img src="../pics/btn_delete.png" width="45" height="16" border="0" alt=""></div></td>
						</tr>
						</table>
					</div>
				</div>
				<div id="div_loading" style="display: none; padding: 25px;" class="round"><img src="../pics/loading_fb.gif" width="16" height="11" border="0" alt=""></div>
			</div>
		</div>

<?php include_once( "./inc_footer.php" ) ?>
