# TrustBuilder MFA custom enrolment process for Virtual Token

## Description
	This php sample website provide a custom TrustBuider Virtual Authenticator (or "Browser Token") enrolment process
	(alternative to standard user MFA enrolment scenario)
	Followed by an enrolment of a second device (ie. mobile/authenticator)
	With some options (logo, title, qrcode, qrcode links, ...)
	And provide also the possibility for the user to test his browser token authentication

## Version / ChangeLog
	Copyright (c)2024 TrustBuilder (formerly inWebo Technologies)
	Versions:	v1.0 - 2023.12.04 - Initial version
				v1.1 - 2023.12.11 - Add an option for 2nd device enrolment
				v1.2 - 2024.02.08 - Add an option for a link to test authentication with VA
				v1.3 - 2024.05.29 - Rename index.html to index.php and use secure site alias from settings.php removing the need to configure it in index.html
									Change enroll.php to redirect test authentication to default current folder (and fix hard link here)
				
## Usage
	Host this on a proper webserver with php
	Do the initial set-up (edit settings.php, see below)
	Use the trustbuilder MFA console email (or others methods) to provide a link to this page to the user with https://server/folder/enroll.php?code=_CODE_
	
## Pre-requisites
	A TrustBuilder MFA service tenant with relevant configuration (users, secure sites, API certificate...)
	A webserver supporting PHP with session, soap and gd (or gd2) extensions activated
	Use of TrustBuilder PHP APIs
		*Modifications currently needed :
		*change SOAP __call (deprecated) to __soapCall in 'API/AuthenticationService.php' & 'API/ConsoleAdminService.php'
		*Add 'loginAddDevice' function in 'resources/apiFunctions.php' :
		*public function loginAddDevice($loginId, $codetype) {
		*	
		*	try { 
		*		$x = new API\loginAddDevice;
		*		$x->userid = $this->uid;
		*		$x->serviceid = $this->serviceId;
		*		$x->loginid = $loginId;
		*		$x->codetype = $codetype;
		*		
		*		
		*		$resp = $this->provisioning->loginAddDevice($x);
		*		$res = $resp->loginAddDeviceReturn;
		*	
		*	} catch (\Exception $error) {   
		*		$this->printError($error);
		*		return "NOK";
		*	}
		*	$this->printError($res);        
		*	return $res;
		*}

	If qrcode used, install the opensource php qrcode library https://phpqrcode.sourceforge.net/ on the webserver
		*Optionnally, to get transparent qrcode background patch the file 'qrimage.php' and add the line after line 90 (before ImageDestroy ...)
		*ImageColorTransparent($target_image, $col[0]);

## Set-up
	### Configure the TrustBuilder MFA PHP APIs settings in 'resources/settings.php' file
		Copy your p12 .crt file obtained from your MFA tenant console within 'API' folder
		Copy 'resources/settings-SAMPLE.php' as 'resources/settings.php' and edit it
		*$serviceId      : your MFA Tenant serviceId,
		*$alias          : alias of the configured secure site,
		*$certFile       : p12 .crt file with provisionning rights,
		*$certPassphrase : p12 .crt file password
		
	### Configure the "Options" part at the start of the enroll.php page
		*$logo_signin          : img html tag for the logo,
		*$page_title           : html page title,
		*$opt_vaifnocode       : if no code or code invalid, show VA in enrolement mode, else error,
		*$opt_qrcodeapp        : show "magic" qr-code for Auth6 download,
		*$opt_activelink       : add html link on "magic" qr-code for Auth6 download,
		*$opt_qrcodeactivation : show the auth6 deeplink activation qrcode
		*$opt_authent          : show the test authenticattion link
	
	### Replace the signin logo file './folder/acme.png'

	