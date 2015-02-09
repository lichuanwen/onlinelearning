	</div>

</div>

<div style="padding: 25px; background: url( ../pics/bg_fade_lite.png ) no-repeat; background-position: center top;">
	<div style="width: 800px; margin: 0 auto; padding-bottom: 25px; font-size: 10px; color: #FFFFFF; text-shadow: 1px 1px #779A22;">
		<?php if ( isset( $CONF["KEY"] ) && ( $CONF["KEY"] == md5($KEY."-c615") ) ): ?><?php else: ?>&copy; OSI Codes Inc. - powered by <a href="http://www.phplivesupport.com/?plk=osicodes-5-ykq-m" target="new" style="color: #FFFFFF;">PHP Live! Support</a> v.<?php echo $VERSION ?><?php endif ; ?>
	</div>
</div>
<div id="sounds" style="position: absolute; width: 1px; height: 1px; overflow: hidden; opacity:0.0; filter:alpha(opacity=0);">
	<div id="div_sounds_new_request"></div>
</div>

</body>
</html>
<?php
	if ( isset( $dbh ) && $dbh['con'] )
		database_mysql_close( $dbh ) ;
?>