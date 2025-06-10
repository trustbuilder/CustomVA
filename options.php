<?php
/*
CustomVA 2024-2025 - TrustBuilder (formerly inWebo Technologies)
VERSION	 	: v1.4 - 2025.06.10
LICENSE		: Please consult the provided LICENSE file
README 		: Please consult the provided README  file
CHANGELOG	: Please consult the provided README  file
*/

// ------------ Options you can edit ------------

// Server folder
$folder = "CustomVA" ;

// Logo signin
$logo_signin = "<img src='./acme.png' />" ;

// page title
$page_title = "ACME Connect" ;

// Define default error message
$errormsg = "Le lien utilisé n'est pas valide.<br>Contactez votre support.";

// If no code provided in URL during initial call (ie no ?code=_CODE_), choose to show virtual authenticator (for using short code) or enter code (for using long code) or an error
// use "VA" for using Virtual Authenticator, "Code" for entering code form or "Error" for showing an error
$opt_nocode = "Code" ;

// 2nd call : Option to permit the enrolment of a 2nd device
$opt_2nddevice = true ;

// 2nd call : Show the Authent link
$opt_authent = true ;

// Add redirect link on authentication success
// false : no redirection
// Other : URL to redirect to
//$opt_redirect = false ;
$opt_redirect = "https://myinwebo.com" ;

//Activate traces
$withErrorTrace = false; //Display errors
$withRESTResultTrace = false; //Display REST call results

// --------------- End of Options ---------------
?>