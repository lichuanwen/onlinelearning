function nospecials(e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();

	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;

	else if ((("abcdefghijklmnopqrstuvwxyz0123456789_-\.\(\) ").indexOf(keychar) > -1))
		return true;
	else
		return false;
}

function logins(e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();

	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;

	else if ((("abcdefghijklmnopqrstuvwxyz0123456789_-\.").indexOf(keychar) > -1))
		return true;
	else
		return false;
}

function justemails(e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();

	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;

	else if ((("abcdefghijklmnopqrstuvwxyz0123456789_-\@\.").indexOf(keychar) > -1))
		return true;
	else
		return false;
}

function numbersonly(e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();

	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;

	else if ((("0123456789.").indexOf(keychar) > -1))
		return true;
	else
		return false;
}

function noquotes(e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();

	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;

	else if ((("\"\'").indexOf(keychar) != -1))
		return false;
	else
		return true;
}

function noquotestags(e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();

	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;

	else if ((("\"\'<>").indexOf(keychar) != -1))
		return false;
	else
		return true;
}

function notags(e)
{
	var key;
	var keychar;

	if (window.event)
	key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;

	keychar = String.fromCharCode(key);
	keychar = keychar.toLowerCase();

	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;

	else if ((("<>").indexOf(keychar) != -1))
		return false;
	else
		return true;
}

function check_email( theemail )
{
	var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i) ;
	return pattern.test( theemail ) ;
}

function do_alert( theflag, thetext )
{
	var message ;

	var div_exists = $('#login_alert_box').length ;
	if ( div_exists )
		$('#login_alert_box').remove() ;

	if ( theflag )
		message = "<div id=\"login_alert_box\" class=\"info_good\" style=\"display: none; position: absolute; top: 0px; left: 0px; text-align: center; padding: 6px; font-size: 14px; font-weight: bold; z-Index: 200;\">"+thetext+"</div>" ;
	else
		message = "<div id=\"login_alert_box\" class=\"info_error\" style=\"display: none; position: absolute; top: 0px; left: 0px; text-align: center; padding: 6px; font-size: 14px; font-weight: bold; z-Index: 200;\">"+thetext+"</div>" ;

	$('body').append( message ) ;
	$('#login_alert_box').center().show().fadeOut("slow").fadeIn("fast").delay(3000).fadeOut("slow").hide() ;
}

function do_alert_div( theflag, thetext )
{
	var message ;

	if ( theflag )
		$('#div_alert').removeClass("info_good").removeClass("info_error").addClass("info_good").html( thetext ) ;
	else
		$('#div_alert').removeClass("info_good").removeClass("info_error").addClass("info_error").html( thetext ) ;

	$('#div_alert').fadeIn("fast") ;
}

function do_search( theurl )
{
	var input_search = encodeURIComponent( $('#input_search').val() ) ;

	if ( input_search == "" )
		do_alert( 0, "Please provide a search string." ) ;
	else
		location.href = theurl+'&action=search&text='+input_search ;
}

unixtime = function() { return parseInt(new Date().getTime().toString().substring(0, 10)) ; }
function microtime( get_as_float )
{
	var now = new Date().getTime() / 1000 ;
	var s = parseInt(now, 10) ;

	return (get_as_float) ? now : (Math.round((now - s) * 1000) / 1000) + ' ' + s ;
}

function pad( number, length )
{
	var str = '' + number ;
	while ( str.length < length )
		str = '0' + str ;
	return str;
}

function autoURL(message){
	var regex = /\b((https?|telnet|gopher|file|wais|ftp):[\w\/\#~:.?+=&%@!\-]+?)*?(?=[.:?\-]*(?:[^\w\/\#~:.?+=&%@!\-]|$))/gi;
	
	return message.replace(regex, "<a href=\"$1\" target=\"_blank\" title=\"Link opens in a new window\" class=\"notranslate\">$1</a>");
}

function regmatch(s,r){
	var myString = new String(s) ;
	var myRE = new RegExp(r, "gi") ;
	var results = myString.match(myRE) ;
	return (results[1]) ;
}

function new_win_default( theurl )
{
	var unique = unixtime() ;
	window.open(theurl, unique, 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1')
}

function parse_marquee( themessage )
{
	if ( themessage.indexOf("[email") != -1 )
		themessage = themessage.replace(/\[email\](.*?)\[\/email\]/g, "<a href=mailto:$1>$1</a>") ;

	if ( themessage.indexOf("[url=") != -1 )
		themessage = themessage.replace(/\[url=(.*?)\](.*?)\[\/url\]/g, "<a href=$1 target=new>$2</a>") ;

	return themessage;
};

/***** variable replacing *****/
String.prototype.trimreturn = function(){
	if ( this.substr((this.length-2), this.length) == "\r\n" ) { return this.substr(0, (this.length-2)) ; }
	else if ( this.substr((this.length-1), this.length) == "\n" ) { return this.substr(0, (this.length-1)) ; }
	else { return this ; }
};
String.prototype.noreturns = function(){
	return this.replace( /(\r\n)/g, ' p_br ' ).replace( /(\r)/g, ' p_br ' ).replace( /(\n)/g, ' p_br ' ) ;
};
String.prototype.nl2br = function(){
	// minor thing perhaps add " p_br " spaces for above
	return this.replace( /(p_br)/g, '<br>' ) ;
};
String.prototype.stripv = function(){
	return this.replace( /(<v>)/g, '' ) ;
};

String.prototype.tags = function(){ var string = this.replace(/>/g, "&gt;"); return string.replace(/</g, "&lt;"); };
String.prototype.c615 = function(){ var string = this.replace(/<c615>(.*?)<\/c615>/g, ""); return string; };
String.prototype.vars = function(){
	var string = this ;
	var url_prefix = "http:" ;
	if ( string.indexOf("https:" ) != -1 )
		url_prefix = "https:" ;

	string = string.replace( /((%%user%%)|(%%visitor%%))/g, "<span class='notranslate'>"+chats[ces]["vname"]+"</span>" );
	string = string.replace( /%%operator%%/g, "<span class='notranslate'>"+cname+"</span>" );
	string = string.replace( /%%op_email%%/g, "<a href='mailto:"+cemail+"' class='notranslate'>"+cemail+"</a>" );

	if ( string.indexOf("image:") != -1 )
		string = string.replace( /image:(.*?)($| |<br>)/g, "<img src=$1 > " ) ;
	else
		string = string.autoLink({ target: "new" }) ;

	if ( string.indexOf("email:") != -1 )
		string = string.replace( /email:(.*?)($| |<)/g, "<a href=mailto:$1 class='notranslate'>$1</a> " ) ;

	/* disabled for now until embeded chat feature is introduced
	if ( string.indexOf("push:") != -1 )
	{
		var url_prefix = "http:" ;
		if ( string.indexOf("https:" ) != -1 )
			url_prefix = "https:" ;

		temp_string = regmatch( string, "push:(.*?)($| |<br>)" ) ;
		if ( temp_string.indexOf( url_prefix ) != -1 )
			temp_string = "[ PUSHING webpage <a href="+temp_string+" target=new >"+temp_string+"</a> ]" ;
		else
			temp_string = "[ PUSHING webpage <a href="+url_prefix+"//"+temp_string+" target=new >"+url_prefix+"//"+temp_string+"</a> ]" ;
		string = string.replace( /push:(.*?)($| |<br>)/, temp_string ) ;
	}
	*/

	return string;
};

function activate_theme( title ) {
	document.getElementById('stylesheet').href = '../themes/'+title+'/style.css' ;
}
