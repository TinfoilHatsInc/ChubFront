<?php

namespace core\login;

use core\common\ConfigReader;
use core\routing\RoutingController;
use TinfoilHMAC\API\SecureRequest;
use TinfoilHMAC\Exception\InvalidHMACException;
use TinfoilHMAC\Exception\InvalidResponseException;
use TinfoilHMAC\Util\Session;
use TinfoilHMAC\Util\UserSession;

class LoginController
{

  public static function sanitizeInput($data)
  {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  /**
   * @return array
   */
  public function login()
  {
    if(UserSession::isSessionActive()) {
      RoutingController::getInstance()->redirectRoute('home');
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if(!array_key_exists('username', $_POST)) {
        return [
          'emailError' => 'You must fill in an email address.',
        ];
      } elseif (!array_key_exists('password', $_POST)) {
        return [
          'passwordError' => 'You must fill in a password.',
        ];
      }
      $username = self::sanitizeInput($_POST["username"]);
      $password = self::sanitizeInput($_POST['password']);
      if(!UserSession::isSessionActive()) {
        UserSession::open($username, $password);
      }
      $configReader = new ConfigReader('chub');
      $checkCredentialRequest = new SecureRequest('POST', $configReader->requireConfig('chubId'), 'registration');
      try {
        $response = $checkCredentialRequest->send();
        RoutingController::getInstance()->redirectRoute('home');
      } catch (InvalidResponseException $e) {
        return [
          'loginError' => 'Invalid login credentials.',
        ];
      } catch (InvalidHMACException $e) {
        return [
          'loginError' => 'Invalid login credentials.',
        ];
      }
    } else {
      return [];
    }
  }

  public function logout() {
    if(UserSession::isSessionActive()) {
      UserSession::destroy();
    }
    Session::getInstance()->invalidateKnownSharedKey();
    RoutingController::getInstance()->redirectRoute('login');
  }

}




