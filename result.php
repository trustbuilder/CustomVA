<?php
//Main loading
include './_loader.php'; 

//Handle data
$postData = $helpers->getPostData($_GET);
$login = $postData['login'];
$pwd = $postData['pwd'];

$params = array (
	"userId" => $_GET['login'],
	"token" => $_GET['pwd']
);

try{
	// Call through inwebo php APIs
	$auth = $apiFunctions->Authenticate($login, $pwd);
	}catch(Exception $e){
		echo $e->getMessage();
	}
?>

<!DOCTYPE html>
<html>
<head>
<style>
html, body {
	display: flex;
	justify-content: center;
	height: 100%;
}
body, div, h1 { 
	padding: 0;
	margin: 20px 20px 20px 20px ;
	outline: none;
	font-family: Poppins, Arial, sans-serif;
	font-size: 16px;
	color: #666;
}
h1 {
	padding: 00px 20px;
	font-size: 32px;
	font-weight: 300;
	text-align: center;
}
.result {
	justify-content: center;
	padding: 10px 20px;
	margin: auto ;
	border-radius: 5px; 
	border: solid 1px #ccc;
	box-shadow: 1px 2px 5px rgba(0,0,0,.31); 
	background: #ebebeb;
}
</style>

</head>

<?php
// Check response
if ($auth === 'OK' ) {
	echo '<body><div class="result" ><center><img src="./acme.png"/></center><hr><h1 style="color:green;">User Authenticated !</h1></div></body>';
} else {
	echo '<body><div class="result" ><center><img src="/acme.png"/></center><hr><h1 style="color:red;">User NOT Authenticated !</h1></div></body>';
} 
?>

</html>