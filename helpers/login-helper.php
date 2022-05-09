<?php 

require_once '../../resources/app_config.php';

use resources\library\AppUtility;
use resources\library\DBUtility;
use resources\library\Account;

$appUtil = new AppUtility();

$conn = new DBUtility($appUtil->GetDBMainStr());



if(!empty($_GET)){
    if (isset($_GET["tbUserName"])){
       
        $UInfo = Account::CreateFromUname(trim($_GET["tbUserName"]));
		$hasAccess = (strtolower($UInfo->GetUName()) == strtolower(trim($_GET["tbUserName"]))) ? true : false;
		header('Content-type: application/json');
        echo json_encode(array("hasAccess" => $hasAccess));
        exit();
    
    }
}
else if(!empty($_POST)) {

    if(isset($_POST["tbLoginForm"])){

        $SqlText = 'SELECT U.[iUserID]
                ,[vFName]
                ,[vLName]
                ,[vSecurityLevel]
                ,[vEMail]
                ,[vPassword]
                ,[dLastPWChange]
                ,dbo.udf_Get_User_ProductListIDs(iUserID) AS UserProductIDs
                ,(SELECT count(*) FROM User_ProductGroup UPG WHERE UPG.UserID = U.iUserID AND UPG.GroupID = ?) AS GroupAccess
            FROM [User] U
            WHERE bActive = 1
            AND vEmail = ?
            ORDER BY dCreated';

            $SqlParams = array($_SESSION["ProductGroup"],trim($_POST['tbUserName']));

            $dataSet = $conn->ExecuteDataQuery($SqlText, $SqlParams);
            $LoginTest = sqlsrv_fetch_array($dataSet, SQLSRV_FETCH_ASSOC);
            
            if (empty($LoginTest)) {
                http_response_code(401);
                echo json_encode(array("msg"=>'The user account was not found',"errcode"=>1));
                exit();
            } else if ($_POST['tbPassword'] != $LoginTest["vPassword"]) {
                // (!password_verify($_POST['tbPassword'], $LoginTest["vPassword"]))
                http_response_code(403);
                echo json_encode(array('msg'=>'The password you entered did not match',"errcode"=>2));
                exit();
            } else if ($LoginTest["vSecurityLevel"] != 'ADMIN' && $LoginTest["GroupAccess"] == 0) {
                // (!password_verify($_POST['tbPassword'], $LoginTest["vPassword"]))
                http_response_code(405);
                echo json_encode(array('msg'=>'Sorry, you do not have access to this site',"errcode"=>3));
                exit();
        } else {
            //pass
            session_destroy();
            session_set_cookie_params(14400, "/");
            session_name("SpkrResourceCtr");
            session_start();            

            $_SESSION['auth_user'] = true;
            $_SESSION['Last_Activity'] = time();
            $_SESSION["UserID"] = $LoginTest["iUserID"];
            $_SESSION["UserName"] = $LoginTest["vEMail"];
            $_SESSION["UserEmail"] = $LoginTest["vEMail"];
            $_SESSION["UserFullName"] = $LoginTest["vFName"] . ' ' . $LoginTest["vLName"];
            $_SESSION["UserInitials"] = substr($LoginTest["vFName"],0,1) . substr($LoginTest["vLName"],0,1) ;
            $_SESSION["Role"] = $LoginTest["vSecurityLevel"];
            if ($_SESSION["Role"] != 'ADMIN') {
                $_SESSION["UserProducts"] = explode(',',$LoginTest["UserProductIDs"]);//array(22,26);
            } else {
                $_SESSION["UserProducts"] = array(22,26); 
            }
            $_SESSION['dLastPWChange'] = (!empty($LoginTest["dLastPWChange"])) ? $LoginTest["dLastPWChange"] : -1;
            $_SESSION["LogID"] = $appUtil->CreateLog($LoginTest["iUserID"],$appUtil->GetAppValue("SiteName"),AppUtility::getRemoteAddr());
            session_write_close();
            
            $act = $appUtil->LogActivity($_SESSION["LogID"], 'Logged in', 'User session started',NULL);
            $applogger->addInfo("{$_SESSION["UserFullName"]} ({$_SESSION["UserID"]}) session started");

            $RedirectToURL = "index.php?t=". time();
            //Go to next page
            header('Content-type: application/json');
            echo json_encode(array("gotoURL" => $RedirectToURL));
            exit();
        }
/* 
		$SqlText = 'SELECT [gUserId], [vUserName],[vPassword],[dVerified]
                    FROM [Users]
                    WHERE bActive = 1
                        AND vUsername = ?
                    ORDER BY dCreated';
        $SqlParams = array(trim($_POST['tbUserName']));
        
        $dataSet = $conn->ExecuteDataQuery($SqlText, $SqlParams);
		$LoginTest = sqlsrv_fetch_array($dataSet, SQLSRV_FETCH_ASSOC);
		
		if (empty($LoginTest)) {
			http_response_code(404);
            echo json_encode(array("msg"=>'The user account was not found'));
            exit();
        } else if (!password_verify($_POST['tbPassword'], $LoginTest["vPassword"])) {
			http_response_code(401);
            echo json_encode(array('msg'=>'The password you entered did not match'));
            exit();
        } else {
            //pass
            session_destroy();
            session_set_cookie_params(28800, "/");
            session_start();
            
            $Verified = ($LoginTest["dVerified"] !== null) ? $LoginTest["dVerified"]->format('Y-m-d H:i:s') : false;
			$UInfo = Account::CreateFromUname(trim($_POST['tbUserName']));
			            
            //If the device has been verified (cookie exists) then set session and log in
            if (isset($_COOKIE["CPG-VAB-Ver"])  && (strpos($_COOKIE["CPG-VAB-Ver"], $_POST['tbUserName']) !== false)  && $Verified !== false ) {

                //Get active meetings user is enrolled in
                $SqlText = "SELECT UsersMeetings.iMeetingID 
                    FROM UsersMeetings 
                    JOIN Users ON UsersMeetings.gUserID = Users.gUserID 
                    JOIN Meetings ON Meetings.iMeetingID = UsersMeetings.iMeetingID 
                    WHERE Users.gUserID = ? AND Meetings.bActive = 1";
                $SqlParams = array($LoginTest['gUserId']);
                $dataSet = $conn->ExecuteDataQuery($SqlText, $SqlParams);
                
                $Meetings = array();
                while($row = sqlsrv_fetch_array($dataSet, SQLSRV_FETCH_ASSOC)){
                    array_push($Meetings, $row['iMeetingID']);
                }
                
                $_SESSION['auth_user'] = true;
                $_SESSION['user_verified'] = $LoginTest["dVerified"];
                $_SESSION['Last_Activity'] = $_SERVER['REQUEST_TIME'];
                $_SESSION["UserID"] = $UInfo->getID();
                $_SESSION["UserName"] = $UInfo->GetUName();
                $_SESSION["UserEmail"] = $UInfo->Email;
                $_SESSION["UserFullName"] = $UInfo->Name();
                $_SESSION["UserInitials"] = $UInfo->GetInitials();
                $_SESSION["Role"] = $UInfo->Role;
                
                $applogger->addInfo("{$_SESSION["UserFullName"]} ({$_SESSION["UserID"]}) logged in");
                
                if( sizeof(($Meetings)) == 1){
                    $_SESSION['MeetingID'] = $Meetings[0];
                    if(strtolower($UInfo->Role) == 'participant'){
                        $RedirectToURL = "dashboard.php?t=". time();
                    }else{
                        $RedirectToURL = "daily-digest.php";
                    }
                }else{
                    $_SESSION['Meetings'] = $Meetings;
                    $RedirectToURL = "meetings.php?t=".time();
                }

            } else {
                //go to verification page
                $_SESSION['auth_user'] = false;
                $_SESSION['user_verified'] = null;
                $_SESSION["UserName"] = $UInfo->GetUName();
                $_SESSION["UserEmail"] = $UInfo->Email;
                $_SESSION["UserFullName"] = $UInfo->Name();
                $codeSendResult = $UInfo->SendVerifyCode();
    
                if ($codeSendResult->Status == "fail") {
                    $applogger->addWarning($codeSendResult->Message);
                    http_response_code(404);
                    echo json_encode(array("msg"=>"Send Verify Code Failed"));
                    exit();
                } else {
                    $applogger->addInfo("{$_SESSION["UserFullName"]} sent verification code");
                }
                $RedirectToURL = "login_verify.php?t=". time();
            }
            
			//Go to next page
			header('Content-type: application/json');
            echo json_encode(array("gotoURL" => $RedirectToURL));
            exit();
        }
 */
    }else if(isset($_POST['tbLoginForgotPW'])){
        $SqlText = 'SELECT [iUserID]
                ,[vFName]
                ,[vLName]
                ,[vSecurityLevel]
                ,[vEMail]
                ,[vPassword]
            FROM [User]
            WHERE bActive = 1
            AND vEmail = ?
        ORDER BY dCreated';

        $SqlParams = array(trim($_POST['tbUserName']));

        $dataSet = $conn->ExecuteDataQuery($SqlText, $SqlParams);
        $LoginTest = sqlsrv_fetch_array($dataSet, SQLSRV_FETCH_ASSOC);
 
        if (empty($LoginTest)) {
            http_response_code(401);
            echo json_encode(array("msg"=>'The user account was not found'));
            exit();
        } else {
            $applogger->addInfo("{$LoginTest["vFName"]} {$LoginTest['vLName']} sent password");
            Account::SendPassword($LoginTest['vEMail'], $LoginTest['vPassword'], $LoginTest['vFName'], $LoginTest['vLName']);
            header('Content-type: application/json');
            echo json_encode(array("gotoURL" => "login.php?pwremind=1&t=". time()));
            exit();
        }
    
        
    
    }else if(isset($_POST["tbUpdatePassword"])){
        
        // $UInfo = Account::CreateFromUname(trim($_POST["tbUserName"]));

        $SqlText = 'SELECT [iUserID], [vFName], [vLName], vPassword
                FROM [User]
                WHERE bActive = 1
                    AND iUserID = ?';
        $SqlParams = array(trim($_SESSION["UserID"]));
        $dataSet = $conn->ExecuteDataQuery($SqlText, $SqlParams);
        $LoginTest = sqlsrv_fetch_array($dataSet, SQLSRV_FETCH_ASSOC);

        if (empty($LoginTest)) {
            http_response_code(404);
            echo json_encode(array("msg"=>'The account could not be found'));
            exit();
        } else if ($LoginTest["vPassword"] != $_POST['tbCurrPassword']) {
            http_response_code(401);
            echo json_encode(array("msg"=>'Your current password does not match'));
            exit();
        } else {
            
            //data is good, save it
            $saveResult = Account::SavePW($_SESSION["UserID"], $_POST['tbPassword']);
            
            if ($saveResult->Status == "Pass") {
                $RedirectToURL = "index.php?msg=" . $saveResult->Message;
                $name = $LoginTest["vFName"] . " " . $LoginTest["vLName"];
                //$applogger->addInfo("{$name} ({$LoginTest["iUserID"]}) changed password");
                if (isset($_SESSION["LogID"])) {
                    $act = $appUtil->LogActivity($_SESSION["LogID"], 'Password Update', 'User changed password',NULL);
                    $applogger->addInfo("{$_SESSION["UserFullName"]} ({$_SESSION["UserID"]}) changed password");
                }

                $_SESSION["dLastPWChange"] = date("Y/m/d");
                header('Content-type: application/json');
                echo json_encode(array("gotoURL" => $RedirectToURL));
                exit();
            } else {
                http_response_code(404);
                echo json_encode(array("msg"=>$saveResult));
                exit();
            }
        }
    }

}