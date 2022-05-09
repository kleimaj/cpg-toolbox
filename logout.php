<?php
require_once '../resources/app_config.php';

use resources\library\AppUtility;

	$appUtil = new AppUtility();

if (isset($_SESSION["LogID"])) {
    $act = $appUtil->LogActivity($_SESSION["LogID"], 'Logged out', 'User session ended',NULL);
	$applogger->addInfo("{$_SESSION["UserFullName"]} ({$_SESSION["UserID"]}) session ended");
}

// Destroying the session clears the $_SESSION variable, thus "logging" the user
// out. This also happens automatically when the browser is closed
session_unset();
session_destroy();
session_write_close();
session_start();
setcookie(session_name(),'',0,'/');
session_regenerate_id(true);

require_once TEMPLATES_PATH . "/layout_top.php";
?>


<div class="login">
<h1>Thank you for visiting the<br><?php echo $appUtil->GetAppValue('SiteName') ?>!</h1>
		<form id="loginForm">
	<div style="text-align: center;">
		
		You have been logged out.<br><a href="login.php" style="text-decoration: underline;">Click here to log back in.</a>
	</div>
		</form>
</div>


<?php 
require_once TEMPLATES_PATH . "/layout_bottom.php";
?>