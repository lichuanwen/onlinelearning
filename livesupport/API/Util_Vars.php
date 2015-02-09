<?php
	/************** DO NOT MODIFY */
	if ( defined( 'API_Util_Vars' ) ) { return ; }	
	define( 'API_Util_Vars', true ) ;
	$PHPLIVE_HOST = isset( $_SERVER["HTTP_HOST"] ) ? $_SERVER["HTTP_HOST"] : "unknown_host" ;
	$PHPLIVE_URI = isset( $_SERVER["REQUEST_URI"] ) ? $_SERVER["REQUEST_URI"] : "unknown uri" ;
	$PHPLIVE_FULLURL = "$PHPLIVE_HOST/$PHPLIVE_URI" ;

	include_once( "$CONF[DOCUMENT_ROOT]/web/vals.php" ) ;
	if ( preg_match( "/patch\.php/", $PHPLIVE_URI ) ) { $VERSION = "PATCH-".time() ; }
	else { include_once( "$CONF[DOCUMENT_ROOT]/web/VERSION.php" ) ; }
	include_once( "$CONF[DOCUMENT_ROOT]/setup/KEY.php" ) ;

	/************** DO NOT MODIFY */
	// To change a variable, create a new file API/Util_Extra.php and place
	// the variable changes there as this file will be overridden with each update
	// - detailed at the bottom -
	$patch_v = 84 ; // DO NOT MODIFY or system may skip patches or produce an error

	$geoip = ( isset( $CONF["geo"] ) && $CONF["geo"] ) ? 1 : 0 ;
	$geomap = ( isset( $CONF["geo"] ) && ( strlen( $CONF["geo"] ) == 39 ) ) ? $CONF["geo"] : 0 ;

	// the directories where chat files are temporary stored during chat
	// (MUST be able to read/write permission access by the web - 777)
	$CONF["CHAT_IO_DIR"] = "$CONF[DOCUMENT_ROOT]/web/chat_sessions" ;
	$CONF["TYPE_IO_DIR"] = "$CONF[DOCUMENT_ROOT]/web/chat_initiate" ;

	$VARS_RTYPE = Array( 1=>"Ordered", 2=>"Round-robin", 3=>"Simultaneous" ) ;
	$VARS_BROWSER = Array( 1=>"IE", 2=>"Firefox", 3=>"Chrome", 4=>"Safari", 5=>"Opera", 6=>"Other" ) ;
	$VARS_OS = Array( 1=>"Windows", 2=>"Mac", 3=>"Unix", 4=>"Other", 5=>"Mobile" ) ;

	// the chat function variables
	$VARS_JS_ROUTING = 3 ; // seconds
	$VARS_JS_REQUESTING = 3 ; // seconds (operator.php & p_engine.php) -- used for chatting() interval
	$VARS_JS_ICON_CHECK = 25 ; // seconds -- also used for manual initiate chat display... so not too long
	$VARS_JS_INITIATE_CHECK = 10 ; // seconds - perator initiated chat check (shorter then $VARS_JS_ICON_CHECK )
	$VARS_JS_FOOTPRINT_MAX_CYCLE = 95 ; // cycle - 1 cycle is $VARS_JS_ICON_CHECK
	$VARS_FOOTPRINT_U_EXPIRE = $VARS_JS_ICON_CHECK * 2 ; // 2 cycle should be enough to indicate expired
	$VARS_FOOTPRINT_LOG_EXPIRE = 45 ; // remove footprint data older than x days
	$VARS_REFER_LOG_EXPIRE = 45 ; // remove refer data older than x days
	$VARS_IP_LOG_EXPIRE = 45 ; // remove IP data older than x days of inactivity (default 45)
	$VARS_JS_RATING_FETCH = 25 ; // check for operator rating every x seconds (15-25 recommended)

	$VARS_OP_DC = $VARS_JS_REQUESTING * 4 ; // 4 cycle fails should be plenty

	$VARS_CYCLE_VUPDATE = 4 ;

	$VARS_CYCLE_CLEAN = $VARS_JS_REQUESTING + 5 ; // offset the cycle by few seconds
	$VARS_CYCLE_RESET = $VARS_JS_REQUESTING + 3 ; // offset the cycle by few seconds
	$VARS_EXPIRED_OPS = $VARS_CYCLE_CLEAN * 8 ; // relies on $VARS_CYCLE_CLEAN for cycles
	// max routing time times operators (should pickup by 5 ops) todo: incorporate # of ops in future version
	$VARS_EXPIRED_REQS = $VARS_EXPIRED_OPS * 3 ;
	$VARS_EXPIRED_OP2OP = $VARS_CYCLE_CLEAN * 3 ; // seconds

	$VARS_TRANSFER_BACK = 45 ; // transfer chat back to original operator after x seconds

	$VARS_SMS_BUFFER = 20 ; // seconds added to the normal routing time.  provides few extra time to return to computer

	// default visitor chat window size (620x520)
	$VARS_CHAT_WIDTH = 620 ;
	$VARS_CHAT_HEIGHT = 540 ;

	/*****************************************************************************/
	/* To change a variable, create a new file API/Util_Extra.php and place
	// the variable changes there as this file will be overridden with each update
	// example:
	//	to change the variable $VARS_TRANSFER_BACK, simply place the same variable in the API/Util_extra.php
	//	with your new value.  if we introduce a new variable in this file on future versions, your changes
	//	will not be replace to the default values.
	*/
	if ( is_file( "$CONF[DOCUMENT_ROOT]/API/Util_Extra.php" ) )
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Extra.php" ) ;
?>