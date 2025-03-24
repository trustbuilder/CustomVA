<?php
/*
Copyright (c)2024 TrustBuilder (formerly inWebo Technologies)
VERSION	 	: v1.3 - 2024.05.29
LICENSE		: Please consult the provided LICENSE file
README 		: Please consult the provided README  file
CHANGELOG	: Please consult the provided README  file
*/

// Start the php session
if(session_status() === PHP_SESSION_NONE) session_start();
include_once './_loader.php'; 

// ------------ Options you can edit ------------
// Logo signin
$logo_signin = '<img src="./acme.png" />' ;
// page title
$page_title = 'Acme Sign-in' ;
// Define default error message
$errormsg = "Le lien utilisé n'est pas valide.<br>Contactez votre support.";
// 1st div : Show VA or error if no code provided in URL (ie no ?code=_CODE_)
$opt_vaifnocode = true ;
// 2nd div : Option to permit the enrolment of a 2nd device
$opt_2nddevice = true ;
// 2nd div : Show Auth6 "magic" qrcode
$opt_qrcodeapp = true ;
// 2nd div : Auth6 "magic" qrcode is a link to the same
$opt_activelink = true ;
// 2nd div : Show the deeplink qrcode for Auth6 activation
$opt_qrcodeactivation = true ;
// 2nd div : Show the Authent link
$opt_authent = true ;
// --------------- End of Options ---------------


// -------- DO NOT MODIFY BELOW --------
// Function to construct current server URL http(s)://<server>:<port>
function getMyUrl() {
	$protocol = (!empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == '1')) ? 'https://' : 'http://';
	$server = $_SERVER['SERVER_NAME'];
	$port = $_SERVER['SERVER_PORT'] ? ':'.$_SERVER['SERVER_PORT'] : '';
	return $protocol.$server.$port;
}

// process URL parameters
// permit to use the php script on cli with : php enroll.php code=123...
if (PHP_SAPI === 'cli' || empty($_SERVER['REMOTE_ADDR'])) {	parse_str(implode('&', array_slice($argv, 1)), $_GET); }
// retrieve and sanitize URL code value
if (!empty($_GET['code'])) { $enteredcode=filter_var($_GET['code'], FILTER_SANITIZE_NUMBER_INT); } else { unset($enteredcode); }


// Define default div show
$showDivEnroll=False ;
$showDivMobile=False ;
$showDivError=False ;

// No code, no session -> Show 1st div (VA) or error depending on $opt_vaifnocode option
if ( (!isset($enteredcode)) && (!isset($_SESSION['id'])) ) {
	$errormsg = "Le lien utilisé n'est pas valide.<br>Contactez votre support.";
	$showDivMobile=False ;
	if ($opt_vaifnocode) {
		// Affiche VA
		$showDivEnroll=True ;
		$showDivError=False;
	} else {
		// Affiche Erreur
		$showDivEnroll=False ;
		$showDivError=True ;
	}
}

// Code and no Session -> 1st div show mode
if (isset($enteredcode) && (!isset($_SESSION['id'])) ) {
	$errormsg = "";
	$showDivEnroll=True ;
	$showDivMobile=False ;
	$showDivError=False ;
	
	try{
		$response = $apiFunctions->loginGetInfoFromLink($enteredcode);
		
	}catch(Exception $e){
		$showDivEnroll=False ;
		$showDivMobile=False ;
		$showDivError=True ;
		$errormsg = "Erreur de la plateforme MFA :<br>" . $e->getMessage();
	}
	// Long Code not valid and no short code in session
	if (substr($response->err,0,3) === 'NOK') { 
		$showDivEnroll=False ;
		$showDivMobile=False ;
		$showDivError=True ;
		$errormsg = "Le lien utilisé n'est pas valide.<br>Contactez votre support.<br><i><p style='font-size:75%'>Erreur : ".$response->err."</p></i>";
		}
	else {
		$shortcode = $response->code;
		$id = $response->id ;
		$_SESSION['shortcode'] = $shortcode;
		$_SESSION['id'] = $id;
		
		try{
			$response = $apiFunctions->loginQuery($id);
		}catch(Exception $e){
			$showDivEnroll=False ;
			$showDivMobile=False ;
			$showDivError=True ;
			$errormsg = "Erreur de la plateforme MFA :<br>" . $e->getMessage();
		}
		if (substr($response->err,0,3) === 'NOK') { 
			unset($_SESSION['id']);
			$showDivEnroll=False ;
			$showDivMobile=False ;
			$showDivError=True ;
			$errormsg = "Impossible de trouver votre utilisateur.<br>Contactez votre support.<br><i><p style='font-size:75%'>Erreur : ".$response->err."</p></i>";
			}
		else {
			$login = $response->login ;
			$_SESSION['login'] = $login;
			$displayuser = $login;
			if (!empty($response->firstname) || !empty($response->name) ) { 
				$displayuser = $response->firstname . " " .  $response->name;
				$_SESSION['displayuser'] = $displayuser;
				$_SESSION['firstname'] = $response->firstname;
				$_SESSION['name'] = $response->name;;
				}
			}
	}
}

// Code and Session -> 1st div show after above or refresh mode
if (isset($enteredcode) && (isset($_SESSION['id'])) ) {
	$errormsg = "";
	$showDivEnroll=True ;
	$showDivMobile=False ;
	$showDivError=False ;
	
	// Use session values
	$shortcode = $_SESSION['shortcode'];
	$id = $_SESSION['id'];
	$login = $_SESSION['login'];
	$displayuser = $_SESSION['displayuser'];
	
	if (!empty($_SESSION['firstname']) || !empty($_SESSION['name']) ) { 
		$displayuser = $_SESSION['firstname'] . " " .  $_SESSION['name'];
		}
}

// No Code but session : 2nd div show , add device mode
if ((!isset($enteredcode)) && isset($_SESSION['id'])) {
	$errormsg = "";
	$showDivEnroll=False ;
	$showDivMobile=True ;
	$showDivError=False ;
	
	if (isset($_SESSION['displayuser']) ) { $displayuser = $_SESSION['displayuser']; } else { $displayuser = ""; }
	
	// Permit to enroll a 2nd device only if option $opt_2nddevice is set
	if ($opt_2nddevice) {
		// test codeadddevice exist in session : 1st 2nd call or 2nd call refresh
		if (!isset($_SESSION['codeadddevice'])) {
			// Retrieve AddDevice short code for user
			try{
				// Call the MFA API loginAddDevice to get a short activation code (codetype=1) for loginid
				$response = $apiFunctions->loginAddDevice($_SESSION['id'],1);
			}catch(Exception $e){
				$showDivEnroll=False ;
				$showDivMobile=False ;
				$showDivError=True ;
				$errormsg = "Erreur de la plateforme MFA :<br>" . $e->getMessage();
			}
			// Test getting short code response error or not
			if (substr($response, 0, 3) === 'NOK') { 
				$showDivEnroll=False ;
				$showDivMobile=False ;
				$showDivError=True ;
				$errormsg = "Impossible d'ajouter un outil.<br>Contacter votre support.<br><i><p style='font-size:75%'>Erreur : ".$response->err."</p></i>";
				}
			else {
				$codeadddevice = $response ;
				$_SESSION['codeadddevice'] = $codeadddevice;
				}
		}
		else {
			 $codeadddevice = $_SESSION['codeadddevice'];
		}
	}
}
?>
<!-- HMTL page -->
<html>
  <head>
    <title><?php echo $page_title; ?></title>
	<link rel="stylesheet" href="./enroll.css">

	
	<?php
		// VA start for main-block div
		if (isset($enteredcode) || $opt_vaifnocode ) {
		echo '<script type="text/JavaScript" src="https://ult-inwebo.com/va/client.js"></script>';
		echo '<script type="text/JavaScript">';
		echo 'VA_jQuery(document).ready(function(){';
		echo '   iwstart("myStartActivate", function(iw, data) {';
		echo '   if (data.type == "success" && data.code == "ok" && data.action == "activation") {';
		echo '      document.getElementById("main-block").style.display = "none";';
		echo '      window.location = window.location.pathname; }';
		echo '   });})';
		echo '</script>';}?>
  </head>
  <body>

<!-- Main div with VA , for 1st call mode ie. VA user enrolment from long code-->	
<div id="main-block" class="main-block" <?php if ($showDivEnroll===true){?>style="display:block" <?php } else {?> style="display:none" <?php } ?> >
	<center><?php echo $logo_signin; ?></center>
	<h1>Bonjour<?php if (isset($displayuser)) {echo " ".$displayuser;} ?>,<br>
	Enrôlez votre navigateur</h1>
	<hr>

	<div id="inweboactivationcode" style="display:none"><?php if (isset($shortcode)) {echo $shortcode;} ?></div>
	<div id="myStartActivate" data-action="activation" data-container="VA" data-quiet-start="1" data-lang="auto" data-width="460" data-alias="<?php echo $alias?>"></div>
	<div id="VA" class="VA"/></div>
</div>

<!-- 2nd div with link to Auth 6, for 2nd call mode ie. mobile or auth6 VA user enrolment from short code-->	
<div id="ActivateMobile" class="main-block" <?php if ($showDivMobile===true){?>style="display:block"<?php } else {?>style="display:none"<?php } ?>>
	<center><?php echo $logo_signin; ?></center>
	<h1><?php if (isset($displayuser)) {echo $displayuser.",<br>v" ;} else {echo "V";} ?>otre navigateur est activé.</h1>
	<?php if (!$opt_2nddevice) {echo "<!--" ;} ?>
		<hr>
		<h3>Vous pouvez maintenant activer votre mobile</h3><hr>
		<h3>Téléchargez l'application<br><b>TrustBuilder Authenticator</b> sur votre mobile<br>
		
		<?php if ($opt_qrcodeapp && $opt_activelink) {echo '<a href="'.getMyUrl().'/acme/apps.php">';}?>		
		<?php if ($opt_qrcodeapp) {echo '<img src="./qrcode.php?code='.getMyUrl().'/acme/apps.php">' ;} ?>
		<?php if ($opt_qrcodeapp && $opt_activelink) {echo '</a>';}?>
		</h3>
		<hr>
		<h3>	
		Utilisez <?php if ($opt_qrcodeactivation) {echo "ou scannez " ;} ?>le code suivant pour l'activation<br>
		<i><p style="font-size:75%">Ce code est valable 15 minutes</p></i></h3>
		<h1><?php if (isset($codeadddevice)) {echo $codeadddevice;} ?>
		<?php if (!$opt_qrcodeactivation) {echo "<!--" ;} ?>	
			<br><img src="./qrcode.php?code=<?php if ($opt_2nddevice) { echo 'inwebo://enroll?activationCode='.$codeadddevice;} ?>">
		<?php if (!$opt_qrcodeactivation) {echo "-->" ;} ?></h1>
	<?php if (!$opt_2nddevice) {echo "-->" ;} ?>
	<?php if (!$opt_authent) {echo "<!--" ;} ?>
		<hr>
		<h3><?php echo '<a href="'.getMyUrl().dirname($_SERVER['PHP_SELF']).'">Tester votre connexion</a> '; ?></h3>

	<?php if (!$opt_authent) {echo "-->" ;} ?>
</div>

<!-- div for errors -->
<div id="Error" class="main-block" <?php if ($showDivError===true){?>style="display:block"<?php } else {?>style="display:none"<?php } ?>>
	<center><?php echo $logo_signin; ?></center>
	<h1>ERREUR !!</h1>
	<hr>
	<h2><?php echo $errormsg; ?></h2>
</div>

</body>
</html>