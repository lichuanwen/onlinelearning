<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	/****************************************/
	// STANDARD header for Setup
	include_once( "../web/config.php" ) ;
	include_once( "../API/Util_Format.php" ) ;
	include_once( "../API/Util_Error.php" ) ;
	include_once( "../API/SQL.php" ) ;
	include_once( "../API/Util_Security.php" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	$auth = Util_Format_Sanatize( Util_Format_GetVar( "auth" ), "ln" ) ;
	$id = Util_Format_Sanatize( Util_Format_GetVar( "id" ), "ln" ) ;
	$wp = Util_Format_Sanatize( Util_Format_GetVar( "wp" ), "ln" ) ;

	if ( $auth == "setup" )
	{
		if ( !$admininfo = Util_Security_AuthSetup( $dbh, $ses, $id ) ){ ErrorHandler( 602, "Invalid setup session.", $PHPLIVE_FULLURL, 0, Array() ) ; }
		$theme = $CONF["THEME"] ;
	}
	else
	{
		if ( !$opinfo = Util_Security_AuthOp( $dbh, $ses, $id, $wp ) ){ ErrorHandler( 602, "Invalid setup session.", $PHPLIVE_FULLURL, 0, Array() ) ; }
		$theme = $opinfo["theme"] ;
	}
	// STANDARD header end
	/****************************************/

	include_once( "../API/Util_Functions.php" ) ;
	include_once( "../API/Ops/get.php" ) ;
	include_once( "../API/Depts/get.php" ) ;
	include_once( "../API/Chat/get.php" ) ;
	include_once( "../API/Chat/get_ext.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "ln" ) ;
	$back = Util_Format_Sanatize( Util_Format_GetVar( "back" ), "ln" ) ;
	$realtime = Util_Format_Sanatize( Util_Format_GetVar( "realtime" ), "ln" ) ;

	$agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : "&nbsp;" ;
	LIST( $os, $browser ) = Util_Format_GetOS( $agent ) ;
	$mobile = ( $os == 5 ) ? 1 : 0 ;
	$error = "" ;

	$transcript = Chat_ext_get_Transcript( $dbh, $ces ) ;
	$requestinfo = Chat_get_RequestHistCesInfo( $dbh, $ces ) ;
	if ( !isset( $requestinfo["ces"] ) && isset( $transcript["ces"] ) )
	{
		$requestinfo = Array() ;
		$requestinfo["ces"] = $transcript["ces"] ;
		$requestinfo["created"] = $transcript["created"] ;
		$requestinfo["ended"] = $transcript["ended"] ;
		$requestinfo["status"] = 1 ;
		$requestinfo["initiated"] = $transcript["initiated"] ;
		$requestinfo["deptID"] = $transcript["deptID"] ;
		$requestinfo["opID"] = $transcript["opID"] ;
		$requestinfo["op2op"] = $transcript["op2op"] ;
		$requestinfo["marketID"] = 0 ;
		$requestinfo["os"] = 4 ;
		$requestinfo["browser"] = 6 ;
		$requestinfo["resolution"] = "&nbsp;" ;
		$requestinfo["vname"] = $transcript["vname"] ;
		$requestinfo["vemail"] = $transcript["vemail"] ;
		$requestinfo["ip"] = $transcript["ip"] ;
		$requestinfo["hostname"] = "" ;
		$requestinfo["sim_ops"] = "" ;
		$requestinfo["agent"] = "&nbsp;" ;
		$requestinfo["onpage"] = "" ;
		$requestinfo["title"] = "" ;
		$requestinfo["custom"] = "" ;
		$requestinfo["question"] = $transcript["question"] ;
	}

	if ( !$realtime || $requestinfo["ended"] )
	{
		$realtime = 0 ; // set it to zero since it ended, not realtime anymore
		$formatted = preg_replace( "/(\r\n)|(\r)|(\n)/", "<br>", preg_replace( "/\"/", "&quot;", $transcript["formatted"] ) ) ;
		$requestinfo["vname"] = Util_Format_Sanatize( $requestinfo["vname"], "v" ) ;
		$opinfo_ = Ops_get_OpInfoByID( $dbh, $transcript["opID"] ) ;
		$deptinfo = Depts_get_DeptInfo( $dbh, $transcript["deptID"] );
		$duration = $transcript["ended"] - $transcript["created"] ;
		if ( $duration < 60 )
			$duration = 60 ;
		$duration = Util_Format_Duration( $duration ) ;
	}
	else
	{
		$opinfo_ = Ops_get_OpInfoByID( $dbh, $requestinfo["opID"] ) ;
		$deptinfo = Depts_get_DeptInfo( $dbh, $requestinfo["deptID"] );
		$formatted = "<div class='ca' style='font-size: 14px; font-weight: bold;'><img src='../themes/$theme/chats.png' width='16' height='16' border='0' alt=''> Real-time Chat Session View</div>" ;
		$duration = "" ;
	}

	$CONF['lang'] = ( $deptinfo["lang"] ) ? $deptinfo["lang"] : $CONF["lang"] ;
	include_once( "../lang_packs/$CONF[lang].php" ) ;

	if ( isset( $requestinfo["os"] ) )
	{
		include_once( "../API/Footprints/get_ext.php" ) ;

		$os = $VARS_OS[$requestinfo["os"]] ;
		$browser = $VARS_BROWSER[$requestinfo["browser"]] ;

		$onpage_title = preg_replace( "/\"/", "&quot;", $requestinfo["title"] ) ;
		$onpage_title_raw = $onpage_title ;
		$onpage_title_snap = ( strlen( $onpage_title_raw ) > 20 ) ? substr( $onpage_title_raw, 0, 40 ) . "..." : $onpage_title_raw ;
		$onpage_raw = preg_replace( "/hphp/i", "http", $requestinfo["onpage"] ) ;
		$onpage_snap = ( strlen( $onpage_raw ) > 20 ) ? substr( $onpage_raw, 0, 40 ) . "..." : $onpage_raw ;

		$refer_raw = $refer_snap = $refer_hist_string = "" ;
		$referinfo_raw = Footprints_get_IPRefer( $dbh, $requestinfo["ip"], 5 ) ;
		if ( isset( $referinfo_raw[0] ) )
		{
			$referinfo = $referinfo_raw[0] ;

			for ( $c = 0; $c < count( $referinfo_raw ); ++$c )
			{
				$this_referinfo = $referinfo_raw[$c] ;
				$refer_raw = preg_replace( "/((http)|(https)):\/\/(www.)/", "", preg_replace( "/hphp/i", "http", $this_referinfo["refer"] ) ) ;
				$refer_snap = ( strlen( $refer_raw ) > 20 ) ? substr( $refer_raw, 0, 40 ) . "..." : $refer_raw ;

				$refer_hist_string .= "$refer_raw<br>" ;
			}
		}

		if ( $requestinfo["marketID"] )
		{
			include_once( "../API/Marketing/get.php" ) ;

			$marketinfo = Marketing_get_MarketingByID( $dbh, $requestinfo["marketID"] ) ;
		}
	}
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> <?php echo ( $realtime ) ? "Chat Session" : "Transcript" ; ?> </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=<?php echo $LANG["CHARSET"] ?>"> 
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<link rel="Stylesheet" href="../themes/<?php echo $theme ?>/style.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/global_chat.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/tooltip.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/winapp.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var view = 1 ; // flag used in global_chat.js for minor formatting of divs
	var base_url = ".." ; var base_url_full = "<?php echo $CONF["BASE_URL"] ?>" ;
	var ces = "<?php echo $ces ?>" ;
	var wp = <?php echo $wp ?> ;
	var realtime = <?php echo ( $realtime ) ? $realtime : 0 ; ?> ;
	var widget ;
	var mobile = <?php echo $mobile ?> ;

	var st_realtime ; var si_timer ;
	var c_chatting = 0 ;
	var chats = new Object ;
	chats[ces] = new Object ;
	chats[ces]["status"] = <?php echo ( isset( $requestinfo["os"] ) ) ? $requestinfo["status"] : 0 ; ?> ;
	chats[ces]["op2op"] = <?php echo ( isset( $requestinfo["os"] ) ) ? $requestinfo["op2op"] : 0 ; ?> ;
	chats[ces]["initiated"] = <?php echo ( isset( $requestinfo["os"] ) ) ? $requestinfo["initiated"] : 0 ; ?> ;
	chats[ces]["disconnected"] = 0 ;
	chats[ces]["fline"] = 0 ;
	chats[ces]["cid"] = "realtime" ;
	chats[ces]["trans"] = "" ;
	chats[ces]["timer"] = <?php echo ( isset( $requestinfo["os"] ) ) ? $requestinfo["created"] : 0 ; ?> ;

	$(document).ready(function()
	{
		init_divs(0) ;
		init_tooltips() ;
		$('#chat_body').html( init_timestamps( "<?php echo $formatted ?>" ) ) ;

		$('#btn_email').attr( "disabled", false ) ; // reset it... firebox caches disabled
		init_req() ;

		if ( realtime )
		{
			init_timer() ;
			chatting() ;
		}

		window.focus() ;

		//$('#table_info tr:nth-child(1n)').addClass('chat_info_tr_traffic_row') ;
	});
	$(window).resize(function() { init_divs(1) ; });

	function init_req()
	{
		if ( !realtime )
			$('#chat_vtimer').html( "<?php echo $duration ; ?>" ) ;
	}

	function init_tooltips()
	{
		var help_tooltips = $( '#chat_input' ).find( '.help_tooltip' ) ;
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

	function toggle_info()
	{
		$('#table_email').fadeOut(100, function() { $('#table_info').fadeIn("fast") ; }) ;
	}

	function toggle_email()
	{
		$('#table_info').fadeOut(100, function() { $('#table_email').fadeIn("fast") ; }) ;
		$('#btn_email').attr( "disabled", false ) ;
	}

	function send_email()
	{
		if ( !$('#vemail').val() )
			do_alert( 0, "Blank Email is invalid." ) ;
		else if ( !$('#message').val() )
			do_alert( 0, "Blank Message is invalid." ) ;
		else
		{
			$('#btn_email').attr( "disabled", true ) ;

			var unique = unixtime() ;
			var deptid = $('#deptid').val() ;
			var vname = "<?php echo ( isset( $requestinfo["os"] ) ) ? $requestinfo["vname"] : "" ; ?>" ;
			var vemail = $('#vemail').val() ;
			var subject = encodeURIComponent( "Chat Transcript: "+vname ) ;
			var message =  encodeURIComponent( $('#message').val() ) ;

			$('#chat_button_start').blur() ;
			$.get("../phplive_m.php", { action: "send_email", ces: '<?php echo $ces ?>', trans: 1, opid: <?php echo isset( $opinfo_["opID"] ) ? $opinfo_["opID"] : 0 ; ?>, deptid: deptid, vname: vname, vemail: vemail, vsubject: subject, vquestion: message, unique: unique },  function(data){
				eval( data ) ;

				if ( json_data.status )
				{
					toggle_info() ;
					do_alert( 1, "Transcript emailed to: "+vemail ) ;
				}
				else
				{
					do_alert( 0, json_data.error ) ;
					$('#btn_email').attr( "disabled", false ) ;
				}
			});
		}
	}

	function chatting()
	{
		var json_data = new Object ;
		var start = 0 ;
		var q_ces = q_chattings = q_cids = "" ;

		q_ces += "q_ces[]="+ces+"&" ;
		q_chattings += "q_chattings[]=0&" ;
		q_cids += "q_cids[]="+chats[ces]["cid"]+"&" ;

		if ( typeof( st_chatting ) != "undefined" )
		{
			clearTimeout( st_chatting ) ;
			st_chatting = this.undefined ;
		}

		if ( !chats[ces]["disconnected"] )
		{
			var unique = unixtime() ;
			$.ajax({
			type: "GET",
			url: "../ajax/chat_chatting.php",
			data: "action=chatting&ces="+ces+"&c_chatting="+c_chatting+"&realtime="+realtime+"&"+q_ces+q_chattings+q_cids+"&fline="+chats[ces]["fline"]+"&"+unique,
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
				{
					for ( c = 0; c < json_data.chats.length; ++c )
						realtime_update_ces( json_data.chats[c] ) ;

					if ( typeof( st_chatting ) == "undefined" )
						st_realtime = setTimeout(function(){ chatting() ; }, <?php echo ( $VARS_JS_REQUESTING * 1000 ) ?>) ;
				}
			},
			error:function (xhr, ajaxOptions, thrownError){
			} });
			++c_chatting ;
		}
		else
		{
			if ( typeof( st_chatting ) != "undefined" )
			{
				clearTimeout( st_chatting ) ;
				st_chatting = this.undefined ;
			}
		}
	}

	function realtime_update_ces( thejson_data )
	{
		var thisces = thejson_data["ces"] ;
		var append_text = thejson_data["text"] ;

		if ( ( typeof( chats[thisces] ) != "undefined" ) && ( parseInt( chats[thisces]["fline"] ) != parseInt( thejson_data["fline"] ) ) )
		{
			chats[thisces]["fline"] = thejson_data["fline"] ;
			chats[thisces]["trans"] += append_text ;

			// parse for flags before doing functions
			if ( ( thejson_data["text"].indexOf( "<disconnected>" ) != -1 ) )
			{
				chats[thisces]["disconnected"] = unixtime() ;
				$('#req_rating').html( "<img src='../themes/<?php echo $theme ?>/loading_fb.gif' width='16' height='11' border='0' alt=''> disconnecting..." ) ;
				setTimeout(function(){ $('#req_rating').html( "[ <a href=\"JavaScript:void(0)\" onClick=\"location.reload()\">view rating</a> ]" ) ; }, 10000) ;
			}

			$('#chat_body').append( init_timestamps( append_text ) ) ;
			init_scrolling() ;

			//if ( chat_sound )
			//	play_sound( "new_text", "new_text" ) ;
		}
	}

//-->
</script>
</head>
<body>

<div id="chat_canvas" style="min-height: 100%; width: 100%;"></div>
<div style="position: absolute; top: 2px; padding: 10px; z-Index: 2; overflow: auto;">
	<div id="chat_body" style="overflow: auto;"></div>
	<div id="chat_input" style="margin-top: 8px; padding: 5px; -moz-border-radius: 5px; border-radius: 5px;">
		
		<?php if ( isset( $requestinfo["os"] ) ): ?>
		<table cellspacing=0 cellpadding=0 border=0 width="100%" id="table_info">
		<tr>
			<td width="160" valign="top">
				<div class="chat_info_td_traffic" style="font-weight: bold;"><table cellspacing=0 cellpadding=0 border=0><tr><td style="padding-right: 5px;"><span id="chat_vtimer"></span></td><td><span id="req_rating"><?php echo isset( $transcript["rating"] ) ? Util_Functions_Stars( $transcript["rating"] ) : "" ; ?></span></td></tr></table></div>
				<div class="chat_info_td_traffic"><b><?php echo ( $requestinfo["resolution"] ) ? $requestinfo["resolution"] : "" ; ?></b> &nbsp; <img src="../themes/<?php echo $theme ?>/os/<?php echo $os ?>.png" width="14" height="14" border="0"  class="help_tooltip" title="- <?php echo $os ?>" style="cursor: help;"> &nbsp; <img src="../themes/<?php echo $theme ?>/browsers/<?php echo $browser ?>.png" width="14" height="14" border="0" class="help_tooltip" title="- <?php echo $browser ?>" style="cursor: help;"></div>
				
				<?php if ( ( ( isset( $opinfo ) && $opinfo["viewip"] ) && !$requestinfo["op2op"] ) || isset( $admininfo ) ): ?>
				<div class="chat_info_td_traffic"><b><span class="help_tooltip" title="- <?php echo $requestinfo["hostname"] ?>"><?php echo $requestinfo["ip"] ?></span></b></div>
				<?php endif ; ?>
				
				<div style="max-height: 70px; overflow: auto;">
				<?php
					if ( $requestinfo["custom"] )
					{
						// Crystal-_-blue-cus-Login-_-My Login-cus-
						$customs = explode( "-cus-", $requestinfo["custom"] ) ;
						for ( $c = 0; $c < count( $customs ); ++$c )
						{
							$custom_var = $customs[$c] ;
							if ( $custom_var && preg_match( "/-_-/", $custom_var ) )
							{
								LIST( $cus_name, $cus_var ) = explode( "-_-", $custom_var ) ;
								print "<div class=\"chat_info_td_traffic\"><b>$cus_name:</b> $cus_var</div>" ;
							}
						}
					}
				?>
				</div>
			</td>
			<td valign="top">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td width="50" nowrap><div class="chat_info_td_traffic" style="padding-left: 10px;"><span class="text_trans_view_td">Visitor:</span></div></td>
					<td><div class="chat_info_td_traffic"><b><?php echo $requestinfo["vname"] ?></b> <span style="font-size: 10px;"><?php echo ( $requestinfo["vemail"] && ( $requestinfo["vemail"] != "null" ) ) ? "<a href='mailto:$requestinfo[vemail]' target='new'>$requestinfo[vemail]</a>" : "" ; ?></span></div></td>
				</tr>
				<tr>
					<td><div class="chat_info_td_traffic" style="padding-left: 10px;"><span class="text_trans_view_td">Operator:</span></div></td>
					<td><div class="chat_info_td_traffic"><?php echo ( $requestinfo["initiated"] ) ? "<img src=\"../themes/$CONF[THEME]/info_initiate.gif\" width=\"10\" height=\"10\" border=\"0\" alt=\"\" class=\"help_tooltip\" title=\"- Operator Initiated Chat\" style=\"cursor: help;\"> " : "" ; ?><b><?php echo $opinfo_["name"] ; ?></b> <span style="font-size: 10px;"><?php echo "<a href='mailto:$opinfo_[email]' target='new'>$opinfo_[email]</a>" ?></span></div></td>
				</tr>
				<tr>
					<td><div class="chat_info_td_traffic" style="padding-left: 10px;"><span class="text_trans_view_td">Department:</span></div></td>
					<td><div class="chat_info_td_traffic"><b><?php echo $deptinfo["name"] ; ?></b></div></td>
				</tr>
				<tr>
					<td><div class="chat_info_td_traffic" style="padding-left: 10px;"><span class="text_trans_view_td">On Page:</span></div></td>
					<td><div class="chat_info_td_traffic"><b><a href="<?php echo ( $onpage_raw == "livechatimagelink" ) ? "JavaScript:void(0)" : $onpage_raw ; ?>" target="_blank"><?php echo $onpage_title_snap ?></a></b></div></td>
				</tr>
				<tr>
					<td><div class="chat_info_td_traffic" style="padding-left: 10px;"><span class="text_trans_view_td">Refer:</span></div></td>
					<td><div class="chat_info_td_traffic"><b><a href="<?php echo $refer_raw ?>" target="_blank"><?php echo $refer_snap ?></a></b></div></td>
				</tr>
				</table>
			</td>
		</tr>
		<?php if ( !$realtime ): ?>
		<tr>
			<td colspan="2"><div class="info_neutral" style="margin-top: 10px;">
				<div style="float: left;">Options: </div>
				<div style="float: left; margin-left: 15px; cursor: pointer" onClick="toggle_email()"><img src="../themes/<?php echo $theme ?>/email.png" width="16" height="16" border="0" alt=""></div>

				<?php if ( !$back ): ?>
				<div style="float: left; margin-left: 15px; cursor: pointer;" onClick="do_print('<?php echo $ces ?>', <?php echo $requestinfo["deptID"] ?>, <?php echo $requestinfo["opID"] ?>, <?php echo $VARS_CHAT_WIDTH ?>, <?php echo $VARS_CHAT_HEIGHT ?> )"><img src="../themes/<?php echo $theme ?>/printer.png" width="16" height="16" border="0" alt=""></div>
				<?php endif ; ?>

				<div style="float: right; text-align: right;"><?php echo date( "M j, Y, g:i a", $requestinfo["created"] ) ; ?></div>
				<div style="clear: both;"></div>
			</div></td>
		</tr>
		<?php endif ; ?>
		</table>

		<div id="table_email" style="display: none; position: relative; top: -108px; padding: 10px;" class="info_content">
			<form>
			<input type="hidden" name="deptid" id="deptid" value="<?php echo $requestinfo["deptID"] ?>">
			<table cellspacing=0 cellpadding=0 border=0 width="100%">
			<tr>
				<td style="">To Email:<br><input type="text" class="input_text" name="vmail" id="vemail" size="38" maxlength="160" value="<?php echo ( $requestinfo["vemail"] != "null" ) ? $requestinfo["vemail"] : "" ; ?>"></td>
			</tr>
			<tr><td style="height: 5px;"></td></tr>
			<tr>
				<td colspan=2>Message:<br><textarea class="input_text" rows="9" style="width: 99%; resize: none;" wrap="virtual" id="message">Hello <?php echo $requestinfo["vname"] ?>,

Please reference the chat transcript below:

%%transcript%%

Thank you,
<?php echo $opinfo_["name"] ?>

</textarea></td>
			</tr>
			<tr><td style="height: 15px;"></td></tr>
			<tr><td><input type="button" id="btn_email" value="Email Transcript" onClick="send_email()" class="input_button"> or <a href="JavaScript:void(0)" onClick="toggle_info()">cancel</a></td></tr>
			</table>
			</form>
		</div>
		<?php else: ?>

		<div class="info_box">Transcript does not exist or is no longer available.</a>.</div>

		<?php endif ; ?>

	</div>
</div>

<?php if ( $back ): ?>
<div class="info_disconnect" style="position: absolute; top: 0px; right: 0px; z-Index: 101;" onClick="history.go(-1)"><img src="../themes/<?php echo $theme ?>/close_extra.png" width="14" height="14" border="0" alt=""> back to transcript list</div>
<?php endif ; ?>

</body>
</html>
<?php database_mysql_close( $dbh ) ; ?>