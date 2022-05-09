<?php
    require_once '../resources/app_config.php';

    use resources\library\AppUtility;
    use resources\library\OL;
	use resources\library\OLFilters;

    $appUtil = new AppUtility();


    require_once TEMPLATES_PATH . "/layout_top.php";
	require_once TEMPLATES_PATH . "/check_auth.php";
	$current_page = array("home" => "", "upcoming" => "active", "on-demand" => "");
	require_once TEMPLATES_PATH . "/navigation.php";
?>
	
	<div id="profile">
		<div class="content-box">
			<div class="header">
				<h1>Upcoming Webcasts</h1>
				<h3>Stay up-to-date with the latest webcasts.</h3>
		
				<ul class="filters" style="margin-bottom:0px;">
					<i class="fas fa-filter clear_filters" title="Clear all filters" id="clearall"></i>
					<input id="search" type="text" placeholder="Search...">
				</ul>
			</div>
			
			<div class="ol-calendar"></div>
		</div>
	</div>
	

	<script>
		$(function() {
			$('.ol-calendar','#profile').toggleClass('visible');
			$.get("templates/_ol_master_calendar.php", {},
				function(data) {
					$("div.ol-calendar","#profile").html(data);
				}
			);

			$("#profile > div.content-box").on("click","a.calendar-nav",function(e) {
				var showMonth = $(this).data("goto");
				JumpToMonth(showMonth);
			});
			$("#profile > div.content-box").on("change","input.date-picker",function(e) {
				var showMonth = $(this).val();
				console.log('Jump:'+showMonth);
				JumpToMonth(showMonth);
			});
			var JumpToMonth = function(showMonth) {
				console.log(showMonth);
				$.get("templates/_ol_master_calendar.php",{"show":showMonth},
					function(data) {
						$("div.ol-calendar","#profile").html(data);
					}
				);
			};

		});
		
	</script>


<?php
    require_once TEMPLATES_PATH . "/layout_bottom.php";
?>

