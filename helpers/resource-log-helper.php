<?php 
 require_once '../../resources/app_config.php';
 require_once TEMPLATES_PATH . "/check_auth.php";

 use resources\library\AppUtility;
 use resources\library\OnDemand;

 $appUtil = new AppUtility();

 if(!empty($_POST)) {
    if(isset($_POST['resourceID']) && isset($_POST['logAction'])){
        //Log start/view, time update,complete
        
        if ($_POST['logAction'] == 'LogView' || $_POST['logAction'] == 'TimeUpdate' || $_POST['logAction'] == 'Complete') {
            //record update for resource log
            $LogResult = OnDemand::UpdateLog($_SESSION["userid"],$_POST['resourceID'],'Viewed Resource',$_POST['timeIndex'],$_POST['isComplete'], $_POST['pageIndex']);
            
            //record update in session activity log, except for time updates
            if ($_POST['logAction'] == 'LogView') {
                $act = $appUtil->LogActivity($_SESSION["LogID"], 'Resource', 'User viewed resource('.$_POST['resourceID'].')',$_POST['resourceID']);
            } else if ($_POST['logAction'] == 'Complete') {
                $act = $appUtil->LogActivity($_SESSION["LogID"], 'Resource', 'User completed resource('.$_POST['resourceID'].')',$_POST['resourceID']);
            }
            
            if ($LogResult->Status == "Pass") {
                //$name = $UInfo->FirstName. " " .$UInfo->LastName;
                //$applogger->addInfo("{$name} ({$_SESSION["UserID"]}) Updated Resource Log");
                header('Content-type: application/json');
                echo json_encode(array("ResourceID" => $_POST['resourceID'], "Activity" => $_POST['logAction'], "TimeIndex" => $_POST['timeIndex'], "IsComplete"=>$_POST['isComplete'], "PageIndex"=>$_POST['pageIndex']));
            } else {
                http_response_code(500);
                echo json_encode(array("msg" => $LogResult->Message));
                exit();
            }

        } else if ($_POST['logAction'] == 'GetProgress') {
            $LogResult = OnDemand::GetProgress($_SESSION["userid"],$_POST['resourceID']);
            
            header('Content-type: application/json');
            echo json_encode(array("ResourceID" => $LogResult["iResourceID"], "TimeIndex" => $LogResult['iTimeIndex'], "IsComplete"=>$LogResult['bComplete'], "PageIndex"=>$_POST['pageIndex']));
        
        } 
        
        
	} else {
        header('Content-type: application/json');
        echo json_encode($_POST);
    }
}


?>