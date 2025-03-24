<?php

if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
	$iPod = stripos( $_SERVER['HTTP_USER_AGENT'], "iPod" );
	$iPhone = stripos( $_SERVER['HTTP_USER_AGENT'], "iPhone" );
	$iPad = stripos( $_SERVER['HTTP_USER_AGENT'], "iPad" );
	$Android = stripos( $_SERVER['HTTP_USER_AGENT'], "Android" );
	$Linux = stripos( $_SERVER['HTTP_USER_AGENT'], "Linux" );
	$Windows = stripos( $_SERVER['HTTP_USER_AGENT'], "Windows" );
	$Macintosh = stripos( $_SERVER['HTTP_USER_AGENT'], "Macintosh" );
	$Mac = stripos( $_SERVER['HTTP_USER_AGENT'], "Mac" );
	
	switch(true) {
		case $iPhone:
		case $iPod:
		case $iPad:
			header( 'Location: itms-apps://itunes.apple.com/app/id1398273229' );
			die();
			break;
		case $Android:
			header( 'Location: market://details?id=com.inwebo.near.prod' );
			die();
			break;
		case $Windows:
			header( 'Location: ms-windows-store://pdp/?ProductId=9PM9780NKQ19' );
			die();
			break;
		case $Linux:
			header( 'Location: https://docs.inwebo.com/__attachments/1903283/Authenticator-linux-6.32.0.AppImage' );
			die();
			break;
		case $Mac:
		case $Macintosh:
			header( 'Location: https://download.inwebo.com/wp-content/uploads/Authenticator-macOS-setup.dmg' );
			die();
			break;
		default:
			header( 'Location: https://www.trustbuilder.com/app-downloads' );
			die();
			break;
	}
} else {
	header( 'Location: https://www.trustbuilder.com/app-downloads' );
	die();
}
?>