<?php
/**
 * AuthenticationService class file
 * 
 * @author    {author}
 * @copyright {copyright}
 * @package   {package}
 */

namespace API;

/**
 * authenticate class
 */
require_once 'authenticate.php';
/**
 * authenticateResponse class
 */
require_once 'authenticateResponse.php';
/**
 * authenticateWithIp class
 */
require_once 'authenticateWithIp.php';
/**
 * authenticateWithIpResponse class
 */
require_once 'authenticateWithIpResponse.php';
/**
 * authenticateRadius class
 */
require_once 'authenticateRadius.php';
/**
 * authenticateRadiusResponse class
 */
require_once 'authenticateRadiusResponse.php';
/**
 * authenticateExtended class
 */
require_once 'authenticateExtended.php';
/**
 * authenticateExtendedResponse class
 */
require_once 'authenticateExtendedResponse.php';
/**
 * AuthenticationResult class
 */
require_once 'AuthenticationResult.php';
/**
 * sealVerify class
 */
require_once 'sealVerify.php';
/**
 * sealVerifyResponse class
 */
require_once 'sealVerifyResponse.php';
/**
 * sealDeferredVerify class
 */
require_once 'sealDeferredVerify.php';
/**
 * sealDeferredVerifyResponse class
 */
require_once 'sealDeferredVerifyResponse.php';
/**
 * pushAuthenticate class
 */
require_once 'pushAuthenticate.php';
/**
 * pushAuthenticateResponse class
 */
require_once 'pushAuthenticateResponse.php';
/**
 * PushAuthenticateResult class
 */
require_once 'PushAuthenticateResult.php';
/**
 * checkPushResult class
 */
require_once 'checkPushResult.php';
/**
 * checkPushResultResponse class
 */
require_once 'checkPushResultResponse.php';

/**
 * AuthenticationService class
 * 
 *  
 * 
 * @author    {author}
 * @copyright {copyright}
 * @package   {package}
 */
class AuthenticationService extends \SoapClient {

  public function AuthenticationService($wsdl = "Authentication.wsdl", $options = array()) {
    parent::__construct($wsdl, $options);
  }

  /**
   *  
   *
   * @param authenticate $parameters
   * @return authenticateResponse
   */
  public function authenticate(authenticate $parameters) {
    return $this->__SoapCall('authenticate', array(
            new \SoapParam($parameters, 'parameters')
      ),
      array(
            'uri' => 'http://service.inwebo.com',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param authenticateWithIp $parameters
   * @return authenticateWithIpResponse
   */
  public function authenticateWithIp(authenticateWithIp $parameters) {
    return $this->__SoapCall('authenticateWithIp', array(
            new \SoapParam($parameters, 'parameters')
      ),
      array(
            'uri' => 'http://service.inwebo.com',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param authenticateRadius $parameters
   * @return authenticateRadiusResponse
   */
  public function authenticateRadius(authenticateRadius $parameters) {
    return $this->__SoapCall('authenticateRadius', array(
            new \SoapParam($parameters, 'parameters')
      ),
      array(
            'uri' => 'http://service.inwebo.com',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param authenticateExtended $parameters
   * @return authenticateExtendedResponse
   */
  public function authenticateExtended(authenticateExtended $parameters) {
    return $this->__SoapCall('authenticateExtended', array(
            new \SoapParam($parameters, 'parameters')
      ),
      array(
            'uri' => 'http://service.inwebo.com',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param sealVerify $parameters
   * @return sealVerifyResponse
   */
  public function sealVerify(sealVerify $parameters) {
    return $this->__SoapCall('sealVerify', array(
            new \SoapParam($parameters, 'parameters')
      ),
      array(
            'uri' => 'http://service.inwebo.com',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param sealDeferredVerify $parameters
   * @return sealDeferredVerifyResponse
   */
  public function sealDeferredVerify(sealDeferredVerify $parameters) {
    return $this->__SoapCall('sealDeferredVerify', array(
            new \SoapParam($parameters, 'parameters')
      ),
      array(
            'uri' => 'http://service.inwebo.com',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param pushAuthenticate $parameters
   * @return pushAuthenticateResponse
   */
  public function pushAuthenticate(pushAuthenticate $parameters) {
    return $this->__SoapCall('pushAuthenticate', array(
            new \SoapParam($parameters, 'parameters')
      ),
      array(
            'uri' => 'http://service.inwebo.com',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param checkPushResult $parameters
   * @return checkPushResultResponse
   */
  public function checkPushResult(checkPushResult $parameters) {
    return $this->__SoapCall('checkPushResult', array(
            new \SoapParam($parameters, 'parameters')
      ),
      array(
            'uri' => 'http://service.inwebo.com',
            'soapaction' => ''
           )
      );
  }

}

