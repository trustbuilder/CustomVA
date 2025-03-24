<?php

//inWebo Service ID (as displayed in the inWebo Administration Console)
$serviceId = 0; // Put your Service ID here
$alias = 'abc..def' ; // Put your secure site alias here

//inWebo API certificate
$certFile = "your_cert_in_crt_format.crt"; // Specify here the name of your certificate file.
$certPassphrase = "your_passphrase"; // This is the passphrase of your certificate file

//Activate traces
$withErrorTrace = false; //Display errors
$withRESTResultTrace = false; //Display REST call results

//PHP Soap client wsdl caching
ini_set("soap.wsdl_cache_enabled", True);
ini_set("soap.wsdl_cache_enabled", WSDL_CACHE_MEMORY);
//ini_set("soap.wsdl_cache_enabled", WSDL_CACHE_DISK);

//DO NOT MODIFY SETTINGS BELOW
//Path to the certificate file
$certPath = __DIR__ . "/../API/" . $certFile;

//inWebo API URL
$iwApiBaseUrl = 'https://api.myinwebo.com'; // DO NOT MODIFY

//inWebo WSDL files
$wsdlProvisioningFile = "Provisioning.wsdl";
$wsdlProvisioningPath = __DIR__ . "/../API/" . $wsdlProvisioningFile;

$wsdlAuthenticationFile = "Authentication.wsdl";
$wsdlAuthenticationPath = __DIR__ . "/../API/" . $wsdlAuthenticationFile;