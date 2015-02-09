/* (c) OSI Codes Inc. */
/* http://www.osicodesinc.com */
/* Dev team: 615 */
/****************************************/
// desktop notification (dn)

var dn_si ;
var dn_his = new Object ;
function dn_pre_request()
{
	dn_request() ;

	if ( typeof( dn_si ) != "undefined" ) ;
		clearInterval( dn_si ) ;

	dn_si = setInterval(function(){ dn_check_si() }, 300) ;
}

function dn_check_browser()
{
	if ( navigator.userAgent.toLowerCase().indexOf('chrome') > -1 )
		return "chrome" ;
	else if ( navigator.userAgent.toLowerCase().indexOf('firefox') > -1 )
		return "firefox" ;
	else
		return "null" ;
}

function dn_check_si()
{
	var dn = dn_check() ;
	if ( dn == 2 )
	{
		if ( typeof( dn_si ) != "undefined" )
			clearInterval( dn_si ) ;

		$('#dn_request').hide() ;
		$('#dn_disabled').show() ;
	}
	else if ( !dn && ( dn != -1 ) )
	{
		if ( typeof( dn_si ) != "undefined" )
			clearInterval( dn_si ) ;

		$('#dn_request').hide() ;
		$('#dn_enabled').show() ;
		if ( dn_enabled )
			$('#dn_enabled_on').show() ;
		else
			$('#dn_enabled_off').show() ;
	}
}

function dn_request()
{
	var dn = dn_check() ;
	if ( dn == 2 )
	{
		$('#dn_request').hide() ;
		$('#dn_disabled').show() ;
	}
	else if ( !dn && ( dn != -1 ) )
		do_alert( 1, "Desktop notification already enabled." ) ;
	else if ( typeof( webkitNotifications ) != "undefined" )
		webkitNotifications.requestPermission() ;
}

function dn_check()
{
	// -1 - not supported, 0 - allowed, 1 - not allowed, 2 - denied (took action)
	if ( typeof( webkitNotifications ) != "undefined" )
		return webkitNotifications.checkPermission() ;
	else
		return -1 ;
}

function dn_show( theforce, theces, thename, thequestion )
{
	var dn = dn_check() ;
	if ( !dn && ( dn != -1 ) )
	{
		if ( dn_enabled || theforce )
		{
			var iconurl = "../pics/icons/dn_notify.png" ;
			dn_his[theces] = webkitNotifications.createNotification( iconurl, thename, thequestion ) ;
			dn_his[theces].show() ;
			setTimeout( function() { dn_close( theces ) ; }, 45000 ) ;
		}
	}
}

function dn_close( theces )
{
	if ( typeof( dn_his[theces] ) != "undefined" )
	{
		dn_his[theces].cancel() ;
		delete dn_his[theces] ;
	}
}
