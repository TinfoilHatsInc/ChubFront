<?php

namespace core\login;

$name = $password = $rememberMe = "";
$emailError = $pwError = "";

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$name = validateInput($_POST["uname"]);
		if (filter_var($name, FILTER_VALIDATE_EMAIL)) {
			$password = validateInput($_POST["pw"]);
			$rememberMe = $_POST["checkbox"]);
		} else {
		  $emailError("Please enter a valid email address.");
		}
	}

	public static function validateInput($data){
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}


  /**
   * @return array
   */
  public static function login() {
    return [];
  }
