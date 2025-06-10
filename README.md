# TrustBuilder MFA custom enrolment process for Virtual Authenticator
TrustBuilder Custom enrolment process for Virtual Authenticator Sample\
v1.4 - 2025.06.10 - BRO

# Description
This php sample website provide a custom TrustBuider Virtual Authenticator (or "Browser Token") enrolment process
(alternative to standard user MFA enrolment scenario) \
Followed by an enrolment of a second device (ie. mobile) \
With some options (folder, logo, title, qrcode, qrcode links, ...)\
Provide the possibility for the user to test his browser token authentication\
Redirect to a webpage after the enrolment process

# Versions
|Version|Date   |Changelog|
|--- |---       |---|
|v1.0|2023.12.04|Initial version|
|v1.1|2023.12.11|Add an option for 2nd device enrolment|
|v1.2|2024.02.08|Add an option for a link to test authentication with VA|
|v1.3|2024.05.29|Rename index.html to index.php and use secure site alias from settings.php removing the need to configure it in index.html<br>Change enroll.php to redirect test authentication to default current folder (and fix hard link here)|
|v1.4|2025.06.10|Change options variables from enroll.php to options.php<br>Add the possibility to redirect after enroll or authentication test success<br>Add a form and an option to enter code within a form if not provided on URL ?code=\_CODE\_ <br>Renamed enroll.css to customva.css and used it in enroll.php, index.php and result.php<br>Removed options $opt_qrcodeapp, $opt_activelink, $opt_qrcodeactivation (remain always active)|

# Usage
Host this on a proper webserver with php\
Do the initial set-up (edit settings.php & options.php, see below)\
Use the trustbuilder MFA console email (or other methods) to provide a link to this page to the user with https://<FQDN>/folder/enroll.php?code=\_CODE\_ or use the form option
	
# Pre-requisites
A TrustBuilder MFA service tenant with relevant configuration (users, secure sites, an API certificate for provisionning)\
A webserver supporting PHP with session, soap and gd (or gd2) extensions activated\
Use of TrustBuilder PHP APIs (get them here : https://github.com/trustbuilder/api-development-kit-PHP) \

Modifications currently needed :
+ change SOAP __call (deprecated) to __soapCall in 'API/AuthenticationService.php' & 'API/ConsoleAdminService.php'
+ Add 'loginAddDevice' function in 'resources/apiFunctions.php' :
~~~
public function loginAddDevice($loginId, $codetype) {
	
	try { 
		$x = new API\loginAddDevice;
		$x->userid = $this->uid;
		$x->serviceid = $this->serviceId;
		$x->loginid = $loginId;
		$x->codetype = $codetype;
		
		
		$resp = $this->provisioning->loginAddDevice($x);
		$res = $resp->loginAddDeviceReturn;
	
	} catch (\Exception $error) {   
		$this->printError($error);
		return "NOK";
	}
	$this->printError($res);        
	return $res;
}
~~~

If qrcode used, install the opensource php qrcode library from : https://phpqrcode.sourceforge.net/ on the webserver \
Optionnally, to get transparent qrcode background patch the file 'qrimage.php' and add the line after line 90 (before ImageDestroy ...)
~~~ 
ImageColorTransparent($target_image, $col[0]);
~~~ 

# Set-up

## Configure the TrustBuilder MFA PHP APIs settings in 'resources/settings.php' file
Copy your p12 .crt file with provisionning rights obtained from your MFA tenant console within 'API' folder
Copy 'resources/settings-SAMPLE.php' as 'resources/settings.php' and edit it

|Variable        |Content|
|---             |---|
|$serviceId      |Your MFA Tenant serviceId|
|$alias          |Alias of the configured secure site|
|$certFile       |P12 .crt file with provisionning rights|
|$certPassphrase |P12 .crt file password|

	
## Configure options in "options.php" file
|Variable|Content|
|---|---|
|$folder         |Folder on the server|
|$logo_signin    |Img html tag for the logo|
|$page_title     |Html page title|
|$opt_nocode     |If no code is provided within URL or code invalid, you can choose :<br>"VA": show Virtual Authenticator (for using short code to enroll)<br>"Code": show a form (for using long code to enroll)<br>"Error": show an error|
|$opt_authent    |Show the test authenticattion link|
|$opt_redirect   |After the enrolment process you can redirect to a web page<br>false : no redirection<br>"URL" : redirect to URL|

## Logo
Replace the signin logo file './folder/acme.png'

	
