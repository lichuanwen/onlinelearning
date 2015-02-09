	</div>

</div>

<div style="padding: 25px; background: url( <?php echo $CONF["BASE_URL"] ?>/pics/bg_fade_lite.png ) no-repeat; background-position: center top;">
	<div style="width: 970px; margin: 0 auto; font-size: 10px; color: #FFFFFF; text-shadow: 1px 1px #779A22;">
		&copy; OSI Codes Inc. - powered by <a href="http://www.phplivesupport.com/?plk=osicodes-5-ykq-m" target="new" style="color: #FFFFFF;">PHP Live! Support</a> v.<?php echo $VERSION ?>
	</div>
</div>

</body>
</html>
<?php
	if ( isset( $dbh ) && $dbh['con'] )
		database_mysql_close( $dbh ) ;
?>