<?php
    require_once '../resources/app_config.php';
    require_once TEMPLATES_PATH . "/layout_top.php";

    use resources\library\Account;
    use resources\library\AppUtility;

    $appUtil = new AppUtility();

    ini_set("xdebug.force_error_reporting", 1);
    ini_set("error_reporting", "true");

    error_reporting(E_ALL|E_STRICT);

    ini_set("display_errors", 1);
    ini_set("xdebug.force_display_errors", 1);
    ini_set('xdebug.var_display_max_depth', '10');
    ini_set('xdebug.var_display_max_children', '256');
    ini_set('xdebug.var_display_max_data', '1024');
        
    $ldapFail = true;
    $hasErrors = false;
    $info = "";

    try {

        if(!empty($_GET) && isset($_GET['lg'])){

            if (isset($_SESSION["LogID"])) {
                $act = $appUtil->LogActivity($_SESSION["LogID"], 'User Logged Out', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],NULL);
            }

            $_SESSION['auth_user'] = false;
            $_SESSION['logged_in'] = false;
            session_unset();
            session_destroy();
            session_write_close();
            session_start();
            setcookie(session_name(),'',0,'/');
            session_regenerate_id(true);
            ?>
            <script>
            $( document ).ready(function() {
                $.when($('#logout-msg').addClass('active').fadeIn('slow').delay(5000).fadeTo( 1000, 0)).done(function(e){
                    newURL = window.location.href.split('?')[0];
                    window.history.pushState('object', document.title, newURL);
                });
            });
         </script>
        <?php
        }
        elseif(!empty($_POST) && isset($_POST['btnLogin'])){

                

                $username = $_POST['username'];
                $password = $_POST['password'];

                $adServer = "ldap://impactgroup1.com";

                
                $ldap = ldap_connect($adServer);
            
                if (!$ldap) {
                    throw new Exception("connect failed", 1);
                }

                $ldaprdn = 'impact' . "\\" . $username;


                ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
                ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
                ldap_set_option($ldap, LDAP_OPT_DEBUG_LEVEL, 7);
                


                $bind = @ldap_bind($ldap, $ldaprdn, $password);
                

                if ($bind) {
                    $filter="(sAMAccountName=$username)";
                    $att = array("company","samaccountname","ou","name","displayname", "sn", "givenname", "mail", "telephonenumber", "mobile","title","department");
                    $result = ldap_search($ldap,"dc=impactgroup1,dc=COM",$filter,$att);

                    
                    $info = ldap_get_entries($ldap, $result);
                    @ldap_close($ldap);
                    $ldapFail = false;

                    //check and get user id
                    $Account = Account::GetNetLogon($username);
                    if(empty($Account)){
                        $info = array();
                    }else{
                        $info[0]["userid"] = $Account["ID"];
                        $info[0]["net"] = $username;
                    }
                    

            } else {
                    $ldapFail = true;
                    $hasErrors = true;

        
            }

        }
    }catch (Exception $e) {
        $hasErrors = true;
    } finally {
        if ($hasErrors === true) {;?>
            <script>
                $( document ).ready(function() {
                    $('#errorMessage').addClass('active').fadeIn('slow').delay(5000).fadeTo( "slow" , 0);
                });
             </script>
            
        <?php }
    }

    if(!empty($info)){
        session_destroy();
        session_set_cookie_params(28800, "/");
        //session_name("CPGToolBox");
        session_start();

        $_SESSION["LogID"] = $appUtil->CreateLog($info[0]["userid"],$appUtil->GetAppValue("SiteName"),AppUtility::getRemoteAddr());
        
        if (isset($_SESSION["LogID"])) {
            $act = $appUtil->LogActivity($_SESSION["LogID"], 'Logged in', 'User session started',NULL);
            $act = $appUtil->LogActivity($_SESSION["LogID"], 'Additional info', "{$info[0]["displayname"][0]} ({$info[0]["userid"]}) session started",NULL);
        }


        $_SESSION['auth_user'] = true;
        $_SESSION['logged_in'] = true;
        $_SESSION['user_verified'] = date("YmdHis");
        $_SESSION['LAST_ACTIVITY'] = $_SERVER['REQUEST_TIME'];
        $_SESSION["UserFullName"] = $info[0]["displayname"][0];
        $_SESSION["samaccountname"] = $info[0]["samaccountname"][0];
        $_SESSION["userid"] =  $info[0]["userid"];
        $_SESSION["net"] =  $info[0]["net"];
        $_SESSION["mail"] =  $info[0]["mail"][0];


        //writing to a json file
        $filename = 'logs/login_'. date("YmdHis") .'.txt';
        $fp = fopen($filename, 'w');
        fwrite($fp, json_encode($_SESSION));
        fclose($fp);    


        $RedirectToURL = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/home.php";
        header("Location: " . $RedirectToURL);
        exit();

    }else{
            $_SESSION['auth_user'] = false;
            $_SESSION['logged_in'] = false;
            session_unset();
            session_destroy();
            session_write_close();
            session_start();
            setcookie(session_name(),'',0,'/');
            session_regenerate_id(true);
    }

    unset($_POST);
    unset($_GET);

    require_once TEMPLATES_PATH . "/navigation.php";

?>
	<div class="login">
        <div class="logout-msg" id="logout-msg" name="logout-msg">
            <i class="fa fa-info-circle"></i>&nbsp;
            Thank you for visiting CPG-Toolbox! You have been logged out.
        </div>
		<h1>Welcome!</h1>
		<h4>Log in using CPG windows username and password to see more</h4>
		<form id="frmValidate" name="frmValidate" method='POST'>
            <div class="messageText" id="errorMessage">
                <i class="fa fa-exclamation-triangle"></i>&nbsp;Invalid username and or password.
            </div>
            <label>User Name</label>
            <div class="inputDiv">
                    <input type="text" class="username form-control" id="username" name="username" value='' required placeholder="Enter User Name" title="Enter User Name" oninvalid="this.setCustomValidity('Enter User Name Here')" oninput="this.setCustomValidity('')" >
            </div>
            <label>Password</label>
            <div class="inputDiv">
                    <input type="password" class="password" id='password' name="password" value='' required placeholder="Enter Password" title="Enter Password" oninvalid="this.setCustomValidity('Enter Password Here')" oninput="this.setCustomValidity('')" >
                    <span toggle="#password-field" class="fa fa-fw fa-eye field_icon toggle-password cursorClass"></span>
            </div>
			<button type="submit" id="btnLogin" name="btnLogin">Login <i class="fas fa-angle-double-right"></i></button>
		</form>
	</div>
	
</body>

<script src="js/index.js"></script>
</html>
