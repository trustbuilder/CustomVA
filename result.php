<?php
/*
CustomVA 2024-2025 - TrustBuilder (formerly inWebo Technologies)
VERSION	 	: v1.4 - 2025.06.10
LICENSE		: Please consult the provided LICENSE file
README 		: Please consult the provided README  file
CHANGELOG	: Please consult the provided README  file
*/

//Main loading
include_once './_loader.php';
include_once './options.php'; 

if (!empty($_GET['login'])) { $login=filter_var($_GET['login'], FILTER_SANITIZE_STRING); } else { $login=""; }
if (!empty($_GET['pwd']))   { $pwd=filter_var($_GET['pwd'],     FILTER_SANITIZE_STRING); } else { $pwd=""; }

try{
	// Call through inwebo php APIs
	$auth = $apiFunctions->Authenticate($login, $pwd);
	}catch(Exception $e){
		$auth = $e->getMessage();
	}
?>

<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="./customva.css" />
<link rel="icon" type="image/x-icon" href="./favicon.ico">
</head>

<?php
// Check response
if ($auth === 'OK' ) {
	echo '<body><div class="result" ><center>'.$logo_signin.'</center><hr><h1 style="color:green;">Votre test d\'authentification est validé !</h1>';
	if ( $opt_redirect ) { echo '<hr><center>Vous allez être redirigé vers : <a href="'.$opt_redirect.'">'.$opt_redirect.'</a></center></div>'; echo "<meta http-equiv=\"refresh\" content=\"4;url=$opt_redirect\"></body>";}
		else { echo '</div></body>'; }
} else {
	echo '<body><div class="result" ><center>'.$logo_signin.'</center><hr><h1 style="color:red;">User NOT Authenticated !</h1>
	<hr><h2>Votre authentification n\'est pas validée.<br>Contactez votre support.<br><br><i><p style="font-size:75%">Erreur : '.$auth.'</p></i></h2>';
	echo '</div></body>';
} 
?>
</html>


