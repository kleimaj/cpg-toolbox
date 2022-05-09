<?php

use resources\library\Account;
$isvalid = false;


if ( $_SESSION["logged_in"] && !empty($_SESSION["net"])){
        //check and get user id
		$Account = Account::GetNetLogon($_SESSION["net"]);
        if(!empty($Account) && !empty($Account["ID"])){
            $isvalid = true;
        }

}


if (! $isvalid){

    $RedirectToURL = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/index.php';
    header("Location: " . $RedirectToURL);
    exit();
}

?>