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

	include_once( "../API/Util_Functions.php" ) ;
	include_once( "../API/Ops/get.php" ) ;
	include_once( "../API/Depts/get.php" ) ;
	include_once( "../API/Chat/get_ext.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$console = Util_Format_Sanatize( Util_Format_GetVar( "console" ), "ln" ) ; $body_width = ( $console ) ? 800 : 900 ;
	$page = ( Util_Format_Sanatize( Util_Format_GetVar( "page" ), "ln" ) ) ? Util_Format_Sanatize( Util_Format_GetVar( "page" ), "ln" ) : 0 ;
	$index = ( Util_Format_Sanatize( Util_Format_GetVar( "index" ), "ln" ) ) ? Util_Format_Sanatize( Util_Format_GetVar( "index" ), "ln" ) : 0 ;

	$menu = "transcripts" ;
	$error = "" ;
	$theme = "default" ;

	$departments = Depts_get_OpDepts( $dbh, $opinfo["opID"] ) ;
	$operators = Ops_get_AllOps( $dbh ) ;

	// make hash for quick refrence
	$operators_hash = Array() ;
	for ( $c = 0; $c < count( $operators ); ++$c )
	{
		$operator = $operators[$c] ;
		$operators_hash[$operator["opID"]] = $operator["name"] ;
	}

	$dept_hash = Array() ;
	for ( $c = 0; $c < count( $departments ); ++$c )
	{
		$department = $departments[$c] ;
		$dept_hash[$department["deptID"]] = $department["name"] ;
	}

	$text = Util_Format_Sanatize( Util_Format_GetVar( "text" ), "" ) ;
	$text = ( $text ) ? $text : "" ;
	$text_query = urlencode( $text ) ;
	$transcripts = Chat_ext_get_OpDeptTrans( $dbh, $opinfo["opID"], $text, $page, 20 ) ;

	$total_index = count($transcripts) - 1 ;
	$pages = Util_Functions_Page( $page, $index, 20, $transcripts[$total_index], "transcripts.php", "ses=$ses&text=$text_query&opid=$opinfo[opID]" ) ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> Transcripts </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8"> 
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<link rel="Stylesheet" href="../css/setup.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/tooltip.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var menu ;
	var reset_url = "transcripts.php?ses=<?php echo $ses ?>" ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		$("body").show() ;
		init_menu_op() ;
		init_tooltips() ;
		toggle_menu_op( "trans", '<?php echo $ses ?>' ) ;

		if ( ( typeof( window.external ) != "undefined" ) && ( typeof( window.external.wp_save_history ) != "undefined" ) ) { $('#menu_dn').hide() ; }
		else { $('#menu_dn').show() ; }

		$('#input_search').focus() ;
		$('#form_search').bind("submit", function() { return false ; }) ;
	});
	
	function open_transcript( theces, theopname )
	{
		var url = "./op_trans_view.php?ses=<?php echo $ses ?>&ces="+theces+"&id=<?php echo $opinfo["opID"] ?>&auth=operator&"+unixtime() ;

		$( '*', '#op_trans' ).each( function(){
			var div_name = $( this ).attr('id') ;
			if ( div_name.indexOf("img_") != -1 )
				$(this).css({ 'opacity': 1 }) ;
		} );

		$('#img_'+theces).css({ 'opacity': '0.4' }) ;

		newwin = window.open( url, theces, "scrollbars=yes,menubar=no,resizable=1,location=no,width=<?php echo $VARS_CHAT_WIDTH ?>,height=<?php echo $VARS_CHAT_HEIGHT ?>,status=0" ) ;

		if ( newwin )
			newwin.focus() ;
	}

	function input_text_listen_search( e )
	{
		var key = -1 ;
		var shift ;

		key = e.keyCode ;
		shift = e.shiftKey ;

		if ( !shift && ( ( key == 13 ) || ( key == 10 ) ) )
			$('#btn_page_search').click() ;
	}

	function init_tooltips()
	{
		var help_tooltips = $( '#op_trans' ).find( '.help_tooltip' ) ;
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
	}

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ; ?>

		<div id="op_trans" style="display: none; margin: 0 auto;">

			<table cellspacing=0 cellpadding=0 border=0 width="100%">
			<tr><td colspan="10"><div class="page_top_wrapper"><?php echo $pages ?></div></td></tr>
			<tr>
				<td width="20" nowrap><div class="td_dept_header">&nbsp;</div></td>
				<td width="130" nowrap><div class="td_dept_header">Operator</div></td>
				<td width="130" nowrap><div class="td_dept_header">Visitor</div></td>
				<td width="40"><div class="td_dept_header">Rating</div></td>
				<td width="140"><div class="td_dept_header">Created</div></td>
				<td width="80"><div class="td_dept_header">Duration</div></td>
				<td><div class="td_dept_header">Question</div></td>
			</tr>
			<?php
				$opinfo["theme"] = "default" ;
				for ( $c = 0; $c < count( $transcripts )-1; ++$c )
				{
					$transcript = $transcripts[$c] ;

					// brute fix of rare bug
					if ( $transcript["opID"] && ( isset( $operators_hash[$transcript["op2op"]] ) || isset( $operators_hash[$transcript["opID"]] ) ) )
					{
						$operator = ( $transcript["op2op"] ) ? $operators_hash[$transcript["op2op"]] : $operators_hash[$transcript["opID"]] ;
						$created = date( "M j (g:i:s a)", $transcript["created"] ) ;
						$duration = $transcript["ended"] - $transcript["created"] ;
						$duration = ( ( $duration - 60 ) < 1 ) ? " 1 min" : Util_Format_Duration( $duration ) ;
						$question = $transcript["question"] ;
						$vname = ( $transcript["op2op"] ) ? $operators_hash[$transcript["opID"]] :  Util_Format_Sanatize( $transcript["vname"], "v" ) ;
						$rating = Util_Functions_Stars( $transcript["rating"] ) ;
						$initiated = ( $transcript["initiated"] ) ?  "<img src=\"../pics/icons/info_initiate.gif\" width=\"10\" height=\"10\" border=\"0\" alt=\"\" class=\"help_tooltip\" title=\"- Operator Initiated Chat\" style=\"cursor: help;\"> " : "" ;

						if ( $transcript["op2op"] )
							$question = "<div class=\"text_grey\"><img src=\"../pics/icons/agent.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"Op-2-Op Chat\" class=\"help_tooltip\" title=\"- Operator to Operator Chat\" style=\"cursor: help;\"></div>" ;
						$btn_view = "<div onClick=\"open_transcript('$transcript[ces]', '$operator')\" style=\"cursor: pointer;\" id=\"img_$transcript[ces]\"><img src=\"../pics/btn_view.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></div>" ;

						$td1 = "td_dept_td" ;

						print "<tr id=\"tr_$transcript[ces]\"><td class=\"$td1\">$btn_view</td><td class=\"$td1\" nowrap><b><div id=\"transcript_$transcript[ces]\">$initiated$operator</div></b></td><td class=\"$td1\" nowrap>$vname</td><td class=\"$td1\" align=\"center\">$rating</td><td class=\"$td1\" nowrap>$created</td><td class=\"$td1\" nowrap>$duration</td><td class=\"$td1\">$question</td></tr>" ;
					}
				}
				if ( $c == 0 )
					print "<tr><td colspan=7 class=\"td_dept_td\">blank results</td></tr>" ;
			?>
			</table>

		</div>

<?php include_once( "./inc_footer.php" ) ; ?>
