<?php

namespace core\login;

$name = $password = $rememberMe = "";

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$name = validateInput($_POST["uname"]);
		$password = validateInput($_POST["pw"]);
		$rememberMe = $_POST["checkbox"]);
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
