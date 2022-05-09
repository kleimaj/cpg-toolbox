<?php 

use resources\library\AppUtility;
$appUtil = new AppUtility();
?>
<body>

	<header>
		<!-- logo -->
		<a href="index.php"><img class="logo" src="images/cpg-toolbox-logo.svg"></a>

		<ul class="header-nav">
		<?php 
		/* show search bar and account icon if the user is logged in, otherwise just show help link */ 
		if ( isset( $_SESSION['auth_user'] ) && $_SESSION['auth_user'] === true  ) {
		?>
			<li><?php echo  $_SESSION["UserFullName"] ?></li>
		</ul>
		<?php } ?>
	</header>
	
	<?php 


	if ( isset($_SESSION['auth_user']) && $_SESSION['auth_user'] === true ) {
		//If user is logged in then show the nav bar
		//log page activity
		if (isset($_SESSION["LogID"])) {
			$act = $appUtil->LogActivity($_SESSION["LogID"], 'Page view', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],NULL);
		}
	?>
	<nav>
		<ul>
			<li><a class="home-nav <?php echo $current_page["home"] ?>" title="Home" href=<?php echo '"' ."http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/home.php"'?>>Home</a></li>
			<li><a class="upcoming-nav <?php echo $current_page["upcoming"] ?>" title="Upcoming Webcasts" href=<?php echo '"' ."http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/upcoming.php"'?>>Upcoming Webcasts</a></li>
			<li><a class="on-demand-nav <?php echo $current_page["on-demand"] ?>" title="On-Demand Webcast Toolbox" href=<?php echo '"' ."http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/on-demand.php"'?>>On-Demand Webcast Toolbox</a></li>
			<li><a class="logout" title="Log Out" href=<?php echo '"' ."http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/index.php?lg=1"'?>>Log Out</a></li>
		</ul>
	</nav>
	<?php 
	} 
	?>	