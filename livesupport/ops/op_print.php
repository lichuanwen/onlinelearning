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
	include_once( "../API/Depts/get.php" ) ;
	include_once( "../API/Ops/get.php" ) ;
	include_once( "../API/Chat/Util.php" ) ;
	include_once( "../API/Chat/get.php" ) ;

	$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "ln" ) ;
	$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "ln" ) ;

	$theme = $CONF["THEME"] ;

	$requestinfo = Chat_get_RequestHistCesInfo( $dbh, $ces ) ;
	$operator = Ops_get_OpInfoByID( $dbh, $requestinfo["opID"] ) ;
	$department = Depts_get_DeptInfo( $dbh, $deptid ) ;

	if ( isset( $department["lang"] ) && $department["lang"] )
		$CONF["lang"] = $department["lang"] ;
	include_once( "../lang_packs/$CONF[lang].php" ) ;

	$output = UtilChat_ExportChat( "$ces.txt" ) ;
	if ( count( $output ) <= 0 )
	{
		include_once( "../API/Chat/get_ext.php" ) ;

		$transcript = Chat_ext_get_Transcript( $dbh, $ces ) ;
		$output[] = $transcript["formatted"] ;
		$output[] = $transcript["plain"] ;
	}

	if ( isset( $output[0] ) )
		$output[0] = preg_replace( "/\"/", "&quot;", $output[0] ) ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> Print Chat Transcript </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=<?php echo $LANG["CHARSET"] ?>"> 

<link rel="Stylesheet" href="../themes/<?php echo $theme ?>/style.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/global_chat.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/tooltip.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var view = 2 ; // flag used in global_chat.js for minor formatting of divs
	var base_url = ".." ;
	var widget ;

	$(document).ready(function()
	{
		$('#chat_body').append( init_timestamps( "<?php echo $output[0] ?>" ) ) ;
		window.focus() ;
	});

	function do_print()
	{
		$('#chat_body').focus() ;
		window.print() ;
	}
//-->
</script>
</head>
<body id="chat_body" style="overflow: auto; padding: 0px;">
<div id="chat_options">
	<div style="margin-bottonm: 10px;" class="info_box">
		<div id="options_print" style="cursor: pointer; font-size: 16px; font-weight: bold;" onClick="do_print()"><img src="../themes/<?php echo $CONF["THEME"] ?>/printer.png" width="16" height="16" border="0" alt=""> <?php echo $LANG["CHAT_PRINT"] ?></div>
	</div>
	<div class="cn"><?php echo $LANG["CHAT_CHAT_WITH"] ?> <span class="text_operator" style="font-weight: bold;"><?php echo $operator["name"] ?></span> - <span class="text_department" style="font-weight: bold;"><?php echo $department["name"] ?></span></div>
</div>

</body>
</html>
<?php database_mysql_close( $dbh ) ; ?>