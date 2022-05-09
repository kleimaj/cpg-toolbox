
<?php
    require_once '../resources/app_config.php';
    require_once TEMPLATES_PATH . "/layout_top.php";
	require_once TEMPLATES_PATH . "/check_auth.php";
	$current_page = array("home" => "active", "upcoming" => "", "on-demand" => "");
    require_once TEMPLATES_PATH . "/navigation.php";

?>

    <div id="content-area">


		<h1 class="welcome">Welcome!</h1>
		<h2>You’ve come to the right pace! The CPG Toolbox will provide with access to all of the Toolbox Webcasts and Resources that the Digital Creative Services (DCS) Team creates and launches.<br><br>Updates will be sent whenever new events or resources are added.</h2>
		
		<ul class="main-menu">
			<li>
				<a href=<?php echo '"' ."http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/upcoming.php"'?>><img src="images/calendar.svg"><span>Upcoming<br>Webcasts</span></a>
			</li>
			<li>
				<a href=<?php echo '"' ."http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/on-demand.php"'?>><img src="images/folder.svg"><span>On-Demand<br>Webcast Toolbox</span></a>
			</li>
		</ul>
	</div>

<?php 

require_once 'templates/layout_bottom.php';

?>
