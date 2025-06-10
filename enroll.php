<?php
/*
CustomVA 2024-2025 - TrustBuilder (formerly inWebo Technologies)
VERSION	 	: v1.4 - 2025.06.10
LICENSE		: Please consult the provided LICENSE file
README 		: Please consult the provided README  file
CHANGELOG	: Please consult the provided README  file
*/

// Start the php session
if(session_status() === PHP_SESSION_NONE) session_start();
include_once './_loader.php'; 
include_once './options.php'; 

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

// Define default divs show
$showDivCode=False ;
$showDivEnroll=False ;
$showDivMobile=False ;
$showDivError=False ;

// No code and no session -> Show 1st div VA or entering code or error depending on $opt_nocode and options
if ( (!isset($enteredcode)) && (!isset($_SESSION['id'])) ) {
	$errormsg = "Le lien utilisé n'est pas valide.<br>Contactez votre support.";
	$showDivMobile=False ;
	switch ($opt_nocode) {
		case "VA":
		// Show VA
		$showDivCode=False ;
		$showDivEnroll=True ;
		$showDivError=False;
		break;
		case "Code":
		// Show enter Code
		$showDivCode=True ;
		$showDivEnroll=False ;
		$showDivError=False;
		break;
		default:
		// Show error
		$showDivCode=False ;
		$showDivEnroll=False ;
		$showDivError=True ;
		break;
	}

}
// Code and no Session -> 1st div show mode
if (isset($enteredcode) && (!isset($_SESSION['id'])) ) {
	$errormsg = "";
	$showDivEnroll=True ;
	$showDivMobile=False ;
	$showDivError=False ;
	
	try{
		// use APIs to obtain shortcode from long code and userId
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
		$errormsg = "Le lien ou le code utilisé n'est pas valide.<br>Contactez votre support.<br><br><i><p style='font-size:75%'>Erreur : ".$response->err."</p></i>";
		}
	else {
		$shortcode = $response->code;
		$id = $response->id ;
		$_SESSION['shortcode'] = $shortcode;
		$_SESSION['id'] = $id;
		
		try{
			// use APIs to obtain user infos
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
	if (isset($_SESSION['shortcode']) )   { $shortcode = $_SESSION['shortcode'];     } else { $shortcode = ""; }
	if (isset($_SESSION['id']) )          { $id = $_SESSION['id'];                   } else { $id = ""; }
	if (isset($_SESSION['login']) )       { $login = $_SESSION['login'];             } else { $login = ""; }
	if (isset($_SESSION['displayuser']) ) { $displayuser = $_SESSION['displayuser']; } else { $displayuser = ""; }
	
	if (!empty($_SESSION['firstname']) || !empty($_SESSION['name']) ) { 
		$displayuser = $_SESSION['firstname'] . " " .  $_SESSION['name'];
		}
		elseif (!empty($_SESSION['login'])) { $displayuser = $_SESSION['login']; }
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
	<link rel="stylesheet" href="./customva.css">
	<link rel="icon" type="image/x-icon" href="./favicon.ico">
	
	<?php
		// VA start for main-block div
		if (isset($enteredcode) || $opt_nocode == "VA" ) {
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
<!-- Div for entering code , for 1st call mode -->	
<div id="EnterCode" class="main-block" <?php if ($showDivCode===true){?>style="display:block" <?php } else {?> style="display:none" <?php } ?> >
	<?php echo '<center>'.$logo_signin.'</center>';
		echo '<h1>Bonjour';
		if (isset($displayuser)) {echo ' '.$displayuser;}
		echo ',<br>Enrôlez votre navigateur</h1><hr><br>';
	?>
	<form action="<?php $PHP_SELF ?>" method="GET">
		<table>
		<tr><td>Code d'activation</td></tr>
		<tr><td><input name="code" type="text" id="code" required></td></tr>
		<tr><td><input type="submit" value="Envoyer" /></td></tr>
		</table>
		<!--<label for="code">Code d'activation :</label>
		<input type="text" id="code" name="code" required>
		<button type="submit">Envoyer</button>-->
		
	</form>
</div>
<!-- Main div with VA , for 1st call mode ie. VA user enrolment from short code-->	
<div id="main-block" class="main-block" <?php if ($showDivEnroll===true){?>style="display:block" <?php } else {?> style="display:none" <?php } ?> >
	<?php 
		echo '<center>'.$logo_signin.'</center>';
		echo '<h1>Bonjour';
		if (isset($displayuser)) {echo ' '.$displayuser;}
		echo ',<br>Enrôlez votre navigateur</h1><hr><br>';
	?>
	<div id="inweboactivationcode" style="display:none"><?php if (isset($shortcode)) {echo $shortcode;} ?></div>
	<div id="myStartActivate" data-action="activation" data-container="VA" data-quiet-start="1" data-lang="auto" data-width="460" data-alias="<?php echo $alias?>"></div>
	<div id="VA" class="VA"/></div>
</div>
<!-- 2nd div with link to Auth 6, for 2nd call mode ie. mobile or auth6 VA user enrolment from short code-->	
<div id="ActivateMobile" class="main-block" <?php if ($showDivMobile===true){?>style="display:block"<?php } else {?>style="display:none"<?php } ?>>
	<?php
		echo '<center>'.$logo_signin.'</center>';
		if (isset($displayuser)) {echo '<h1>'.$displayuser.',<br>v' ;} else {echo '<h1>V';}; echo 'otre navigateur est activé.</h1>';
		
		if ($opt_2nddevice) {
			echo '<hr><h3>Vous pouvez maintenant activer <b>Authenticator Mobile</b></h3><hr>';		
			echo '<h3>Scannez le QR Code pour installer l\'application<br><b>Authenticator Mobile</b><br>';
			echo '<a href="'.getMyUrl().'/'.$folder.'/apps.php">';
			echo '<img src="./qrcode.php?code='.getMyUrl().'/'.$folder.'/apps.php">' ;
			echo '</a>';
			
			echo '</h3><hr><h3>Puis scannez le QR Code pour l\'activation sur votre mobile<br><i><p style="font-size:75%">Vous pouvez aussi copier le code manuellement.<br>Ce code est valable 15 minutes</p></i></h3><h1>';
					
			echo '<a href="inwebo://enroll?activationCode='.$codeadddevice.'">';
			echo $codeadddevice;
			echo '<br><img src="./qrcode.php?code=inwebo://enroll?activationCode='.$codeadddevice.'"</a>';
					
			echo '</h1>';
		}
		
		if ($opt_authent) {
			echo '<hr><h3>' ;
			echo '<a href="'.getMyUrl().dirname($_SERVER['PHP_SELF']).'">Tester votre connexion</a></h3>';
		}
	?>
</div>

<!-- div for errors -->
<div id="Error" class="main-block" <?php if ($showDivError===true){?>style="display:block"<?php } else {?>style="display:none"<?php } ?>>
	<?php
	echo '<center>'.$logo_signin.'</center>';
	echo '<hr><h1>ERREUR !!</h1><hr><h2>'.$errormsg.'</h2>';
	session_destroy();
	?>
</div>
</body>
</html>