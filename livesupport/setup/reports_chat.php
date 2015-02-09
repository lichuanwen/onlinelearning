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
	include_once( "../API/Depts/get.php" ) ;
	include_once( "../API/Ops/get.php" ) ;
	include_once( "../API/Chat/get_ext.php" ) ;

	$error = "" ;
	$theme = "default" ;
	$deptinfo = $opinfo = Array() ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "ln" ) ;
	$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "ln" ) ;
	$m = Util_Format_Sanatize( Util_Format_GetVar( "m" ), "ln" ) ;
	$d = Util_Format_Sanatize( Util_Format_GetVar( "d" ), "ln" ) ;
	$y = Util_Format_Sanatize( Util_Format_GetVar( "y" ), "ln" ) ;

	if ( !$m )
		$m = date( "m", time() ) ;
	if ( !$d )
		$d = date( "j", time() ) ;
	if ( !$y )
		$y = date( "Y", time() ) ;

	$today = mktime( 0, 0, 1, $m, $d, $y ) ;
	$stat_start = mktime( 0, 0, 1, $m, 1, $y ) ;
	$stat_end = mktime( 0, 0, 1, $m+1, 0, $y ) ;
	$stat_end_day = date( "j", $stat_end ) ;

	$departments = Depts_get_AllDepts( $dbh ) ;
	$operators = Ops_get_AllOps( $dbh ) ;
	if ( $deptid ) { $deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ; }
	if ( $opid ) { $opinfo = Ops_get_OpInfoByID( $dbh, $opid ) ; }
	
	$rating_none = Util_Functions_Stars(0) ;

	if ( $action == "reset" )
	{
		include_once( "../API/Chat/remove.php" ) ;

		Chat_remove_ResetReports( $dbh ) ;
		database_mysql_close( $dbh ) ;
		HEADER( "location: reports_chat.php?action=success&ses=$ses" ) ; exit ;
	}

	$requests_timespan = Chat_get_ext_RequestsRangeHash( $dbh, $stat_start, $stat_end ) ;
	$month_stats = Array() ;
	$month_total_requests = $month_total_taken = $month_total_declined = $month_total_message = $month_total_initiated = $month_total_initiated_ = 0 ;
	$month_max = $js_stat_depts = $js_stat_ops = "" ;
	foreach ( $requests_timespan as $sdate => $deptop )
	{
		// todo: filter for invalid dates (should be fixed with timezone reset)
		if ( isset( $deptop["depts"] ) )
		{
			foreach ( $deptop["depts"] as $key => $value )
			{
				if ( !isset( $month_stats[$sdate] ) )
				{
					$month_stats[$sdate] = Array() ;
					$month_stats[$sdate]["requests"] = $month_stats[$sdate]["taken"] = $month_stats[$sdate]["declined"] = $month_stats[$sdate]["message"] = $month_stats[$sdate]["initiated"] = $month_stats[$sdate]["initiated_"] = 0 ;
				}

				$month_stats[$sdate]["requests"] += $value["requests"] ;
				$month_stats[$sdate]["taken"] += $value["taken"] ;
				$month_stats[$sdate]["declined"] += $value["declined"] ;
				$month_stats[$sdate]["message"] += $value["message"] ;
				$month_stats[$sdate]["initiated"] += $value["initiated"] ;
				$month_stats[$sdate]["initiated_"] += $value["initiated_"] ;

				if ( $sdate )
				{
					$month_total_requests += $value["requests"] ;
					$month_total_taken += $value["taken"] ;
					$month_total_declined += $value["declined"] ;
					$month_total_initiated += $value["initiated"] ;
					$month_total_initiated_ += $value["initiated_"] ;
					$month_total_message += $value["message"] ;
				}

				$rating = ( $value["rateit"] ) ? round( $value["ratings"]/$value["rateit"] ) : 0 ;

				$js_stat_depts .= "stat_depts[$sdate][$key]['requests'] = $value[requests] ; stat_depts[$sdate][$key]['taken'] = $value[taken] ; stat_depts[$sdate][$key]['declined'] = $value[declined] ; stat_depts[$sdate][$key]['message'] = $value[message] ; stat_depts[$sdate][$key]['initiated'] = $value[initiated] ; stat_depts[$sdate][$key]['initiated_'] = $value[initiated_] ; stat_depts[$sdate][$key]['rating'] = $rating ; " ;
			}
		}
		if ( isset( $deptop["ops"] ) )
		{
			foreach ( $deptop["ops"] as $key => $value )
			{
				$rating = ( $value["rateit"] ) ? round( $value["ratings"]/$value["rateit"] ) : 0 ;

				$js_stat_ops .= "stat_ops[$sdate][$key]['requests'] = $value[requests] ; stat_ops[$sdate][$key]['taken'] = $value[taken] ; stat_ops[$sdate][$key]['declined'] = $value[declined] ; stat_ops[$sdate][$key]['message'] = $value[message] ; stat_ops[$sdate][$key]['initiated'] = $value[initiated] ; stat_ops[$sdate][$key]['initiated_'] = $value[initiated_] ; stat_ops[$sdate][$key]['rating'] = $rating ; " ;
			}
		}

		if ( isset( $month_stats[$sdate]["requests"] ) && ( $month_stats[$sdate]["requests"] > $month_max ) && $sdate )
			$month_max = $month_stats[$sdate]["requests"] ;
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
<script type="text/javascript" src="../js/tooltip.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var stat_depts = new Object ;
	var stat_ops = new Object ;
	var stat_start = <?php echo $stat_start ?> ;

	var stars = new Object ;
	stars[5] = "<?php echo Util_Functions_Stars( 5 ) ; ?>" ;
	stars[4] = "<?php echo Util_Functions_Stars( 4 ) ; ?>" ;
	stars[3] = "<?php echo Util_Functions_Stars( 3 ) ; ?>" ;
	stars[2] = "<?php echo Util_Functions_Stars( 2 ) ; ?>" ;
	stars[1] = "<?php echo Util_Functions_Stars( 1 ) ; ?>" ;

	<?php
		for ( $c = 1; $c <= $stat_end_day; ++$c )
		{
			$stat_day = mktime( 0, 0, 1, $m, $c, $y ) ;
			print "stat_depts[$stat_day] = new Object; stat_ops[$stat_day] = new Object; " ;

			for ( $c2 = 0; $c2 < count( $departments ); ++$c2 )
			{
				$department = $departments[$c2] ;
				print "stat_depts[$stat_day][$department[deptID]] = new Object; " ;
			}
			for ( $c3 = 0; $c3 < count( $operators ); ++$c3 )
			{
				$operator = $operators[$c3] ;
				print "stat_ops[$stat_day][$operator[opID]] = new Object; " ;
			}
		}
	?>

	<?php echo $js_stat_depts ?>
	<?php echo $js_stat_ops ?>

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "rchats" ) ;

		<?php if ( $action && !$error ): ?>do_alert(1, "Success") ;<?php endif ; ?>

		var help_tooltips = $( 'body' ).find('.help_tooltip' ) ;
		help_tooltips.tooltip({
			event: "mouseover",
			track: true,
			delay: 0,
			showURL: false,
			showBody: "- ",
			fade: 0,
			extraClass: "stat"
		});

		reset_date() ;
	});

	function close_iframe()
	{
		$('#iframe_wrapper').hide() ;
	}

	function reset_date()
	{
		select_date( 0, "<?php echo date( "M j, Y", $stat_start ) ?> - <?php echo date( "M j, Y", $stat_end ) ?>" ) ;
	}

	function select_date( theunix, thedayexpand )
	{
		var stat_total_requests = stat_total_taken = stat_total_declined = stat_total_message = stat_total_initiated = stat_total_initiated_ = 0 ;

		$('#stat_day_expand').html( thedayexpand ) ;
		if ( theunix )
		{
			for ( var deptid in stat_depts[theunix] )
			{
				if ( typeof( stat_depts[theunix][deptid]["requests"] ) != "undefined" )
				{
					populate_stats( "dept", deptid, stat_depts[theunix][deptid]["requests"], stat_depts[theunix][deptid]["taken"], stat_depts[theunix][deptid]["declined"], stat_depts[theunix][deptid]["initiated"], stat_depts[theunix][deptid]["initiated_"],  stat_depts[theunix][deptid]["message"], stars[stat_depts[theunix][deptid]["rating"]] ) ;

					stat_total_requests += stat_depts[theunix][deptid]["requests"] ;
					stat_total_taken += stat_depts[theunix][deptid]["taken"] ;
					stat_total_declined += stat_depts[theunix][deptid]["declined"] ;
					stat_total_initiated += stat_depts[theunix][deptid]["initiated"] ;
					stat_total_initiated_ += stat_depts[theunix][deptid]["initiated_"] ;
					stat_total_message += stat_depts[theunix][deptid]["message"] ;
				}
				else
					populate_stats( "dept", deptid, 0, 0, 0, 0, 0, 0, "<?php echo $rating_none ?>" ) ;
			}

			for ( var opid in stat_ops[theunix] )
			{
				if ( typeof( stat_ops[theunix][opid]["requests"] ) != "undefined" )
					populate_stats( "op", opid, stat_ops[theunix][opid]["requests"], stat_ops[theunix][opid]["taken"], stat_ops[theunix][opid]["declined"], stat_ops[theunix][opid]["initiated"], stat_ops[theunix][opid]["initiated_"], stat_ops[theunix][opid]["message"], stars[stat_ops[theunix][opid]["rating"]] ) ;
				else
					populate_stats( "op", opid, 0, 0, 0, 0, 0, 0, "<?php echo $rating_none ?>" ) ;
			}
		}
		else
		{
			var depts = new Object ;
			var ops = new Object ;
			for ( var sdate in stat_depts )
			{
				for ( var deptid in stat_depts[sdate] )
				{
					if ( typeof( depts[deptid] ) == "undefined" )
					{
						depts[deptid] = new Object ;
						depts[deptid]["requests"] = depts[deptid]["taken"] = depts[deptid]["declined"] = depts[deptid]["initiated"] = depts[deptid]["initiated_"] = depts[deptid]["message"] = depts[deptid]["rating"] = depts[deptid]["rating_c"] = 0 ;
					}

					if ( typeof( stat_depts[sdate][deptid]["requests"] ) != "undefined" )
					{
						depts[deptid]["requests"] += stat_depts[sdate][deptid]["requests"] ;
						depts[deptid]["taken"] += stat_depts[sdate][deptid]["taken"] ;
						depts[deptid]["declined"] += stat_depts[sdate][deptid]["declined"] ;
						depts[deptid]["initiated"] += stat_depts[sdate][deptid]["initiated"] ;
						depts[deptid]["initiated_"] += stat_depts[sdate][deptid]["initiated_"] ;
						depts[deptid]["message"] += stat_depts[sdate][deptid]["message"] ;
						depts[deptid]["rating"] += stat_depts[sdate][deptid]["rating"] ;
						depts[deptid]["rating_c"] += ( stat_depts[sdate][deptid]["rating"] ) ? 1 : 0 ;
					}
				}

				for ( var opid in stat_ops[sdate] )
				{
					if ( typeof( ops[opid] ) == "undefined" )
					{
						ops[opid] = new Object ;
						ops[opid]["requests"] = ops[opid]["taken"] = ops[opid]["declined"] = ops[opid]["initiated"] = ops[opid]["initiated_"] = ops[opid]["message"] = ops[opid]["rating"] = ops[opid]["rating_c"] = 0 ;
					}

					if ( typeof( stat_ops[sdate][opid]["requests"] ) != "undefined" )
					{
						ops[opid]["requests"] += stat_ops[sdate][opid]["requests"] ;
						ops[opid]["taken"] += stat_ops[sdate][opid]["taken"] ;
						ops[opid]["declined"] += stat_ops[sdate][opid]["declined"] ;
						ops[opid]["initiated"] += stat_ops[sdate][opid]["initiated"] ;
						ops[opid]["initiated_"] += stat_ops[sdate][opid]["initiated_"] ;
						ops[opid]["message"] += stat_ops[sdate][opid]["message"] ;
						ops[opid]["rating"] += stat_ops[sdate][opid]["rating"] ;
						ops[opid]["rating_c"] += ( stat_ops[sdate][opid]["rating"] ) ? 1 : 0 ;
					}
				}
			}

			for ( var deptid in depts )
			{
				var stars_ = Math.round( depts[deptid]["rating"]/depts[deptid]["rating_c"] ) ;
				populate_stats( "dept", deptid, depts[deptid]["requests"], depts[deptid]["taken"], depts[deptid]["declined"], depts[deptid]["initiated"], depts[deptid]["initiated_"], depts[deptid]["message"], stars[stars_] ) ;

				stat_total_requests += depts[deptid]["requests"] ;
				stat_total_taken += depts[deptid]["taken"] ;
				stat_total_declined += depts[deptid]["declined"] ;
				stat_total_initiated += depts[deptid]["initiated"] ;
				stat_total_initiated_ += depts[deptid]["initiated_"] ;
				stat_total_message += depts[deptid]["message"] ;
			}

			for ( var opid in ops )
			{
				var stars_ = Math.round( ops[opid]["rating"]/ops[opid]["rating_c"] ) ;
				populate_stats( "op", opid, ops[opid]["requests"], ops[opid]["taken"], ops[opid]["declined"], ops[opid]["initiated"], ops[opid]["initiated_"], ops[opid]["message"], stars[stars_] ) ;
			}
		}

		$('#stat_total_requests').html( stat_total_requests ) ;
		$('#stat_total_taken').html( stat_total_taken ) ;
		$('#stat_total_declined').html( stat_total_declined ) ;
		$('#stat_total_message').html( stat_total_message ) ;
		$('#stat_total_initiated').html( stat_total_initiated ) ;
		$('#stat_total_initiated_').html( stat_total_initiated_ ) ;
	}

	function populate_stats( thestat, theid, therequests, thetaken, thedeclined, theinitiated, theinitiated_, themessage, therating )
	{
		$('#stat_req_'+thestat+'_requests_'+theid).html( therequests ) ;
		$('#stat_req_'+thestat+'_taken_'+theid).html( thetaken ) ;
		$('#stat_req_'+thestat+'_declined_'+theid).html( thedeclined ) ;
		$('#stat_req_'+thestat+'_message_'+theid).html( themessage ) ;
		$('#stat_req_'+thestat+'_initiated_'+theid).html( theinitiated ) ;
		$('#stat_req_'+thestat+'_initiated__'+theid).html( theinitiated_ ) ;
		$('#stat_req_'+thestat+'_rating_'+theid).html( therating ) ;
	}

	function show_div( thediv )
	{
		var divs = Array( "depts", "ops", "info" ) ;
		for ( c = 0; c < divs.length; ++c )
		{
			$('#reports_'+divs[c]).hide() ;
			$('#sub_menu_'+divs[c]).removeClass('op_submenu_focus').addClass('op_submenu') ;
		}

		$('#reports_'+thediv).show() ;
		$('#sub_menu_'+thediv).removeClass('op_submenu').addClass('op_submenu_focus') ;
	}

	function do_reset()
	{
		if ( confirm( "Reset Chat Reports data?  This action cannot be undone." ) )
		{
			location.href = "reports_chat.php?action=reset&ses=<?php echo $ses ?>" ;
		}
	}

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu_focus">Chat Reports</div>
			<div class="op_submenu" onClick="location.href='reports_chat_active.php?ses=<?php echo $ses ?>'">Active Chats</div>
			<div class="op_submenu" onClick="location.href='reports_chat_msg.php?ses=<?php echo $ses ?>'">Offline Messages</div>
			<div style="clear: both"></div>
		</div>

		<table cellspacing=0 cellpadding=0 border=0 width="100%" style="margin-top: 25px;">
		<tr>
			<td><div class="td_dept_header">Timeline</div></td>
			<td width="80"><div class="td_dept_header">Requests</div></td>
			<td width="60"><div class="td_dept_header">Accepted</div></td>
			<td width="80"><div class="td_dept_header">Declined</div></td>
			<td width="110"><div class="td_dept_header">Initiated</div></td>
			<td width="120"><div class="td_dept_header">Initiate Accept</div></td>
			<td width="110"><div class="td_dept_header">Email Form</div></td>
		</tr>
		<tr>
			<td class="td_dept_td"><?php include_once( "./inc_select_cal.php" ) ; ?></td>
			<td class="td_dept_td"><?php echo $month_total_requests ?></td>
			<td class="td_dept_td"><?php echo $month_total_taken ?></td>
			<td class="td_dept_td"><?php echo $month_total_declined ?></td>
			<td class="td_dept_td"><?php echo $month_total_initiated ?></td>
			<td class="td_dept_td"><?php echo $month_total_initiated_ ?></td>
			<td class="td_dept_td"><?php echo $month_total_message ?></td>
		</tr>
		</table>

		<div style="margin-top: 25px; width: 100%;">
			<table cellspacing=0 cellpadding=0 border=0 style="height: 100px; width: 100%;">
			<tr>
				<?php
					$tooltips = Array() ;
					for ( $c = 1; $c <= $stat_end_day; ++$c )
					{
						$stat_day = mktime( 0, 0, 1, $m, $c, $y ) ;
						$stat_day_expand = date( "l, M j, Y", $stat_day ) ;

						$h1 = "0px" ; $meter = "meter_v_blue.gif" ;
						$style = "height: $h1; width: 100%;" ;
						$tooltip = "- <b>$stat_day_expand</b>" ;
						$tooltips[$stat_day] = $tooltip ;
						$tooltip_display = "" ;
						if ( isset( $month_stats[$stat_day] ) )
						{
							$tooltip_display = "$stat_day_expand" ;
							if ( $month_max )
								$h1 = round( ( $month_stats[$stat_day]["requests"]/$month_max ) * 100 ) . "px" ;
						}
						else if ( ( $c == $stat_end_day ) && ( !$month_max ) )
						{
							$h1 = "100px" ;
							$meter = "meter_v_clear.gif" ;
						}

						print "
							<td valign=\"bottom\" width=\"2%\"><div id=\"bar_v_requests_$c\" class=\"help_tooltip\" title=\"$tooltip_display - <i>click to select stat day</i>\" style=\"height: $h1; background: url( ../pics/meters/$meter ) repeat-y; border-top-left-radius: 5px 5px; -moz-border-radius-topleft: 5px 5px; border-top-right-radius: 5px 5px; -moz-border-radius-topright: 5px 5px;\" OnMouseOver=\"\" OnClick=\"select_date( $stat_day, '$stat_day_expand' );\"></div></td>
							<td><img src=\"../pics/space.gif\" width=\"5\" height=1></td>
						" ;
					}
				?>
			</tr>
			<tr>
				<?php
					for ( $c = 1; $c <= $stat_end_day; ++$c )
					{
						$stat_day = mktime( 0, 0, 1, $m, $c, $y ) ;
						$stat_day_expand = date( "l, M j, Y", $stat_day ) ;
						print "
							<td align=\"center\"><div id=\"requests_bg_day\" class=\"help_tooltip page_report\" style=\"margin: 0px; font-size: 10px; font-weight: bold;\" title=\"$tooltips[$stat_day] - <i>click to select stat day</i>\" OnMouseOver=\"\" OnClick=\"select_date( $stat_day, '$stat_day_expand' );\">$c</div></td>
							<td><img src=\"../pics/space.gif\" width=\"5\" height=1></td>
						" ;
					}
				?>
			</tr>
			</table>
		</div>

		<div id="overview_day_chart" style="margin-top: 50px;">
			<div id="overview_date_title"><div id="stat_day_expand"></div></div>

			<div class="op_submenu_wrapper">
				<div class="op_submenu_focus" onClick="show_div('depts')" id="sub_menu_depts">Departments</div>
				<div class="op_submenu" onClick="show_div('ops')" id="sub_menu_ops">Operators</div>
				<div class="op_submenu" onClick="show_div('info')" id="sub_menu_info">Stat Info</div>
				<div style="clear: both"></div>
			</div>

			<div id="overview_data_container" style="margin-top: 15px;">
				<div id="reports_depts">
					<table cellspacing=0 cellpadding=0 border=0 width="100%">
					<tr>
						<td><div class="td_dept_header">Department</div></td>
						<td width="100"><div class="td_dept_header" style="text-align: center;">Ave Rating</div></td>
						<td width="80"><div class="td_dept_header" style="text-align: center;">Requests</div></td>
						<td width="60"><div class="td_dept_header" style="text-align: center;">Accepted</div></td>
						<td width="80"><div class="td_dept_header" style="text-align: center;">Declined</div></td>
						<td width="80"><div class="td_dept_header" style="text-align: center;">Initiated</div></td>
						<td width="120"><div class="td_dept_header" style="text-align: center;">Initiate Accept</div></td>
						<td width="110"><div class="td_dept_header" style="text-align: center;"><a href="reports_chat_msg.php?ses=<?php echo $ses ?>">Email Form</a></div></td>
					</tr>
					<?php
						for ( $c = 0; $c < count( $departments ); ++$c )
						{
							$department = $departments[$c] ;
							$td1 = "td_dept_td" ;

							print "
								<tr>
									<td class=\"$td1\" nowrap>$department[name]</td>
									<td class=\"$td1\"><div id=\"stat_req_dept_rating_$department[deptID]\">$rating_none</div></td>
									<td class=\"$td1\"><div id=\"stat_req_dept_requests_$department[deptID]\"  class=\"report_numbox\">0</div></td>
									<td class=\"$td1\"><div id=\"stat_req_dept_taken_$department[deptID]\" class=\"report_numbox\">0</div></td>
									<td class=\"$td1\"><div id=\"stat_req_dept_declined_$department[deptID]\" class=\"report_numbox\">0</div></td>
									<td class=\"$td1\"><div id=\"stat_req_dept_initiated_$department[deptID]\" class=\"report_numbox\">0</div></td>
									<td class=\"$td1\"><div id=\"stat_req_dept_initiated__$department[deptID]\" class=\"report_numbox\">0</div></td>
									<td class=\"$td1\"><div id=\"stat_req_dept_message_$department[deptID]\" class=\"report_numbox\">0</div></td>
								</tr>
							" ;
						}
					?>
					<tr>
						<td class="td_dept_td"><b>Total</b></td>
						<td class="td_dept_td"><div id="stat_total_rating" style="font-weight: bold; text-align: center;"></div></td>
						<td class="td_dept_td"><div id="stat_total_requests" style="font-weight: bold; text-align: center;"></div></td>
						<td class="td_dept_td"><div id="stat_total_taken" style="font-weight: bold; text-align: center;"></div></td>
						<td class="td_dept_td"><div id="stat_total_declined" style="font-weight: bold; text-align: center;"></div></td>
						<td class="td_dept_td"><div id="stat_total_initiated" style="font-weight: bold; text-align: center;"></div></td>
						<td class="td_dept_td"><div id="stat_total_initiated_" style="font-weight: bold; text-align: center;"></div></td>
						<td class="td_dept_td"><div id="stat_total_message" style="font-weight: bold; text-align: center;"></div></td>
					</tr>
					</table>
				</div>

				<div id="reports_ops" style="display: none;">
					<table cellspacing=0 cellpadding=0 border=0 width="100%">
					<tr>
						<td><div class="td_dept_header">Operator</div></td>
						<td width="100"><div class="td_dept_header" style="text-align: center;">Ave Rating</div></td>
						<td width="80"><div class="td_dept_header" style="text-align: center;">Requests</div></td>
						<td width="60"><div class="td_dept_header" style="text-align: center;">Accepted</div></td>
						<td width="80"><div class="td_dept_header" style="text-align: center;">Declined</div></td>
						<td width="80"><div class="td_dept_header" style="text-align: center;">Initiated</div></td>
						<td width="120"><div class="td_dept_header" style="text-align: center;">Initiate Accept</div></td>
						<td width="110"><div class="td_dept_header" style="text-align: center;">&nbsp;</div></td>
					</tr>
					<?php
						for ( $c = 0; $c < count( $operators ); ++$c )
						{
							$operator = $operators[$c] ;
							$td1 = "td_dept_td" ;

							print "
								<tr>
									<td class=\"$td1\">$operator[name]</td>
									<td class=\"$td1\"><div id=\"stat_req_op_rating_$operator[opID]\">$rating_none</div></td>
									<td class=\"$td1\"><div id=\"stat_req_op_requests_$operator[opID]\" class=\"report_numbox\">0</div></td>
									<td class=\"$td1\"><div id=\"stat_req_op_taken_$operator[opID]\" class=\"report_numbox\">0</div></td>
									<td class=\"$td1\"><div id=\"stat_req_op_declined_$operator[opID]\" class=\"report_numbox\">0</div></td>
									<td class=\"$td1\"><div id=\"stat_req_op_initiated_$operator[opID]\" class=\"report_numbox\">0</div></td>
									<td class=\"$td1\"><div id=\"stat_req_op_initiated__$operator[opID]\" class=\"report_numbox\">0</div></td>
								</tr>
							" ;
						}
					?>
					</table>
				</div>

				<div id="reports_info" style="display: none;">
					For 1 visitor chat request, the following report will be stored:
					<ul style="margin-top: 15px;">
						<li> For every operator that receives the request, their <b>"Requests"</b> value will increment by one.
						<li> For every operator that does not accept the request, their <b>"Declined"</b> value will increment by one.
						<li> One chat request to a department could count towards several department <b>"Declined"</b> depending on the numbers of operators assisnged to the department.
						<li> If the request is routed to the "Leave a Message" form, the department <b>"Email Form"</b> value will increment by one.

						<li> If the department <a href="depts.php?ses=<?php echo $ses ?>&div=rloop">"Chat Routing Loop"</a> is set to more then once, it is still considered one chat request.
					</ul>

					<div style="margin-top: 15px;" class="info_neutral">
						<div><img src="../pics/icons/reset.png" width="16" height="16" border="0" alt=""> <b>Chat Reports Reset</b></div>
						<div style="margin-top: 5px;">The chat report values can be reset, clearing the data and setting the values to zero accross all departments and operators.  Chat reports reset does not affect the transcripts, offline messages, or any other portion of the software.  The reset simply sets the department and operator chat reports values to zero.</div>

						<div style="margin-top: 15px;"><button type="button" onClick="do_reset()">Reset Chat Reports</button></div>
					</div>
				</div>

			</div>
		</div>

<?php include_once( "./inc_footer.php" ) ?>

