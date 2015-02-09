<?php
	$CONF = Array() ;
$CONF['DOCUMENT_ROOT'] = addslashes( '/var/www/websites/www/onlinelearning/livesupport' ) ;
$CONF['BASE_URL'] = '//www.humber.ca/onlinelearning/livesupport' ;
$CONF['SQLTYPE'] = 'mysql' ;
$CONF['SQLHOST'] = 'localhost' ;
$CONF['SQLLOGIN'] = 'aqeel' ;
$CONF['SQLPASS'] = 'fall2004' ;
$CONF['DATABASE'] = 'livesupport' ;
$CONF['THEME'] = 'default' ;
$CONF['TIMEZONE'] = 'America/New_York' ;
$CONF['icon_online'] = '' ;
$CONF['icon_offline'] = '' ;
$CONF['lang'] = 'english' ;
$CONF['logo'] = '' ;
$CONF['geo'] = '' ;
$CONF['SALT'] = 'qz6nwfdef5' ;
$CONF['API_KEY'] = 'psydwa2uwn' ;
$CONF['icon_check'] = 'off' ;
	if ( phpversion() >= '5.1.0' ){ date_default_timezone_set( $CONF['TIMEZONE'] ) ; }
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vars.php" ) ;
?>