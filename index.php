<?php
include_once './resources/settings.php';
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Connect</title>
	<link rel="stylesheet" href="./index.css">


	<script type="text/JavaScript" src="https://ult-inwebo.com/va/client.js"></script>
	<script type="text/JavaScript">
		VA_jQuery(document).ready(function(){
			//iwpopup("main-block", "myStart", function() {
				iwstart("myStart", function(iw, data) {
					//Successful authentication
					if (data.action == "authentication" && data.code == "ok") {
						iw.insertFields(data.result);
					} // else document.getElementById("Error").innerHTML = data.result.reason;
					
					//Virtual Authenticator is not activated
					if (data.type == "error" && data.code == "nok" && data.result.reason == "no_profile") {
						//We terminate the previous instance of Virtual Authenticator
						//setTimeout(() => { iwterminate();}, 5000);
						iwterminate();
						//We encapsulate the restart of Virtual Authenticator on action "activation" in a setTimeout
						setTimeout(function() {
							iwstart("myStartActivate", function(iw, data) {
								//handle successful activation here
								if (data.type == "success" && data.code == "ok" && data.action == "activation") {
									window.location.href = window.location.href;
									
								} // else document.getElementById("Error").innerHTML = data.result.reason;
							});
						}, 0);
					}
				});
		   //});
		});
		
	</script>
  </head>
  <body>

	<div class="bg">

		
		<div id="main-block" class="main-block">
			<center><img src="./acme.png"/></center>
			<h1><b></b>Vous connecter</b></h1>
			<hr>
			<div id="myStart" data-action="authentication" data-verbose="1" data-container="main-block" data-quiet-start="1" data-lang="auto" data-width="M" data-alias="<?php echo $alias?>"></div>
			<div id="myStartActivate" data-verbose="1" data-action="activation" data-container="main-block" data-quiet-start="1" data-lang="auto" data-width="M" data-alias="<?php echo $alias?>"></div>

		  <form name="form" action="./result.php" style="display:none;">
			<hr>
			<label id="icon" for="name"><i class="fas fa-user"></i></label>
			<input type="text" name="login" id="login" placeholder="Name" required/>
			<label id="icon" for="name"><i class="fas fa-unlock-alt"></i></label>
			<input type="password" name="pwd" id="pwd" placeholder="Password" required/>
		  </form>
		  
		</div>
	</div>



  </body>
</html>