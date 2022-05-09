<?php 

require_once '../../resources/app_config.php';

use resources\library\AppUtility;
use resources\library\DBUtility;
use resources\library\Account;

$appUtil = new AppUtility();

$conn = new DBUtility($appUtil->GetDBMainStr());

//get user info
if(isset($_SESSION["UserID"]))
	$UInfo = Account::CreateFromID($_SESSION['UserID']);

if(!$UInfo){
	http_response_code(404);
    echo json_encode(array("msg"=>"User not found."));
    exit();
}

if(!empty($_POST)) {
    if(isset($_POST['tbProfileUpdate'])){
		$UInfo->FirstName = trim($_POST['tbFName']);
		$UInfo->LastName = trim($_POST['tbLName']);
        $UInfo->Email = trim($_POST['tbEmail']);
		$saveResult = $UInfo->Save();
       
		if ($saveResult->Status == "Pass") {
            $name = $UInfo->FirstName. " " .$UInfo->LastName;
			$applogger->addInfo("{$name} ({$_SESSION["UserID"]}) Updated profile");
			header('Content-type: application/json');
			echo json_encode(array("FName" => $UInfo->FirstName, "LName" => $UInfo->LastName, "Email" => $UInfo->Email));
		} else {
			http_response_code(500);
			echo json_encode(array("msg" => $saveResult->Message));
			exit();
		}
	}
}