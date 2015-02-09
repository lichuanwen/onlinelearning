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

	include_once( "../API/Ops/get.php" ) ;
	include_once( "../API/Chat/get.php" ) ;
	include_once( "../API/Footprints/get_itr.php" ) ;
	include_once( "../API/Footprints/remove.php" ) ;

	$error = "" ;

	$operators = Ops_get_AllOps( $dbh ) ;
	Footprints_remove_Expired_U( $dbh ) ;
	$total_visitors = Footprints_get_itr_TotalFootprints_U( $dbh ) ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> Online Status Monitor </title>

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
	var refresh_counter = 25 ;
	var si_refresh ;

	$(document).ready(function()
	{
		$("body").css({'background': '#F1F1F1'}) ;
		//$('#table_trs tr:nth-child(2n+3)').addClass('td_dept_td2') ;

		var refresh_counter_temp = refresh_counter ;
		si_refresh = setInterval(function(){
			if ( refresh_counter_temp <= 0 )
				location.href = "tools_op_status.php?ses=<?php echo $ses ?>&"+unixtime() ;
			else
			{
				$('#refresh_counter').html( pad( refresh_counter_temp, 2 ) ) ;
				--refresh_counter_temp ;
			}
		}, 1000) ;
	});
	$(window).resize(function() { });

	function open_window( theurl )
	{
		window.open( theurl, "PHP Live! Setup", "scrollbars=yes,menubar=yes,resizable=1,location=yes,status=1" ) ;
	}

	function remote_disconnect( theopid, thelogin )
	{
		if ( confirm( "Remote disconnect operator console?" ) )
		{
			clearInterval( si_refresh ) ;
			$('#op_login').html( thelogin ) ;
			$('#remote_disconnect_notice').show() ;

			$.ajax({
				type: "GET",
				url: "../ajax/setup_actions.php",
				data: "ses=<?php echo $ses ?>&action=remote_disconnect&opid="+theopid+"&"+unixtime(),
				success: function(data){
					eval( data ) ;

					if ( json_data.status )
					{
						// 15 seconds of wait since console checks every 10 seconds
						setTimeout( function(){
							if ( window.opener != null && !window.opener.closed )
								window.opener.location.href = 'index.php?ses=<?php echo $ses ?>' ;

							location.href = 'tools_op_status.php?ses=<?php echo $ses ?>' ;
						}, 15000 ) ;
					}
					else
					{
						$('#remote_disconnect_notice').hide() ;
						do_alert( 0, "Could not remote disconnect console.  Please try again." ) ;
					}
				}
			});
		}
	}
//-->
</script>
</head>
<body style="">

<div id="ops_list" style="padding: 10px;">
	<div style="padding: 5px; color: #A5A5A5; text-shadow: 1px 1px #FFFFFF;">Auto refresh in <span id="refresh_counter">25</span> seconds.</div>
	
	<div class="info_error" style="display: none; margin-top: 10px;" id="div_error_dc">Could not connect to server.  Try reloading the window to re-establish connection.</div>
	<div style="margin-top: 10px;">
		<table cellspacing=0 cellpadding=0 border=0 width="100%" id="table_trs">
		<tr>
			<td width="150"><div class="td_dept_header">Operator</div></td>
			<td width="80"><div class="td_dept_header">Chats</div></td>
			<td><div class="td_dept_header">Status</div></td>
		</tr>
		<?php
			for ( $c = 0; $c < count( $operators ); ++$c )
			{
				$operator = $operators[$c] ;
				$status = ( $operator["status"] ) ? "<b>Online</b>" : "Offline" ;
				$status_img = ( $operator["status"] ) ? "<img src=\"../pics/icons/bulb.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"online\" title=\"online\">" : "<img src=\"../pics/icons/bulb_off.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"offline\" title=\"offline\">" ;
				$tr_style = ( $operator["status"] ) ? "background: #AFFF9F;" : "" ;
				$style = ( $operator["status"] ) ? "cursor: pointer;" : "" ;
				$js = ( $operator["status"] ) ? "onClick='remote_disconnect($operator[opID], \"$operator[login]\")'" : "" ;

				$requests = Chat_get_OpTotalRequests( $dbh, $operator["opID"] ) ;

				print "<tr style=\"$tr_style\"><td class=\"td_dept_td\">$operator[name]</td><td class=\"td_dept_td\"><a href=\"JavaScript:void(0)\" onClick=\"open_window('$CONF[BASE_URL]/setup/reports_chat_active.php?ses=$ses')\">$requests</a></td><td class=\"td_dept_td\" style=\"$style\" $js>$status_img</td></tr>" ;
			}
			if ( $c == 0 )
				print "<tr><td colspan=7 class=\"td_dept_td\">blank results</td></tr>" ;
		?>
		</table>
	</div>
</div>


<div id="remote_disconnect_notice" style="display: none; position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; background: url( ../pics/bg_trans_white.png ) repeat; overflow: hidden; z-index: 20;">
	<div style="padding: 15px;"><div class="info_error" style="">Disconnecting console [ <span id="op_login"></span> ]... <img src="../pics/loading_fb.gif" width="16" height="11" border="0" alt=""></div></div>
</div>

<div id="total_visitors" style="position: absolute; bottom: 10px;" class="info_neutral">Website visitors: <span class="info_box"><?php echo $total_visitors ?></span></div>

</body>
</html>
<?php
	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;
?>
