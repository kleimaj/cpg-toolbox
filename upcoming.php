
<?php
    require_once '../resources/app_config.php';
    require_once TEMPLATES_PATH . "/layout_top.php";
	require_once TEMPLATES_PATH . "/check_auth.php";
	$current_page = array("home" => "", "upcoming" => "active", "on-demand" => "");
	require_once TEMPLATES_PATH . "/navigation.php";


	use resources\library\Events;
	$event_list = Events::GetEvents();

	$hidden = "";
	if (empty($event_list)) {
		$hidden = 'hidden';
	}
?>

<div id="content-area">
	<h1>Upcoming Webcasts</h1>
	<h3>Stay up-to-date with the latest webcasts.</h3>
	
	<ul class="filters" style="margin-bottom:0px;">
		<i class="fas fa-filter clear_filters" title="Clear all filters" id="clearall"></i>
		<input id="search" type="text" placeholder="Search...">
	</ul>
	
	<table id="upcoming-table" cellpadding="0" cellspacing="0">
			<thead <?php echo $hidden ?>>
				<tr>
					<th>Date</th>
					<th>Time</th>
					<th>Duration (Hours)</th>
					<th>Event </th>
					<th>Add to my calendar</th>
				</tr>
			</thead>
			<tbody>
				<?php 
					
					foreach ($event_list as $drow) {
						$addClass = '';
						$disClass = '';
						$pastClass = '';
						if($drow["isAdded"]){
							if($drow["isStatus"]){
								$addClass = 'added';
							}else{
								$addClass = 'failed';
							}
							$disClass = 'active';
						}
						if($drow["isOver"]){
							$pastClass = 'past';
						}

						echo "
						<tr class='{$pastClass}'>
							<td>
								<div>{$drow["date"]}</div>
								<div class='nextrow'>{$drow["day"]}</div>
							</td>
							<td>
								<div>{$drow["time"]}</div>
								<div class='nextrow'>EST</div>
							</td>
							<td>{$drow["duration"]} hrs</td>
							<td>
								<div>
									<div class='alignLeft'>
										Title:&nbsp;&nbsp;{$drow["title"]}
									</div>
									<div class='alignText'>
										Information:&nbsp;&nbsp;{$drow["description"]}
									</div>
									<div class='alignURL'>
										URL:&nbsp;&nbsp;
										<a href='{$drow["url"]}' class='alignLink' rel='noopener noreferrer' target='_blank''>{$drow["url"]}</a>
									</div>
								</div>
							</td>
							<td style='vertical-align: middle';>";
							if($drow["isOver"]){
								echo "<a class='aviewold'  href='#' id='btPs{$drow["id"]}' onclick='openwebcast({$drow["id"]})'>
									<span class='added'><i class='fas fa-video'></i>&nbsp;VIEW PRESENTATION</span>
								</a>";
							}else{
								echo "<a class='asendmail {$addClass}' href='#' id='btn{$drow["id"]}' onclick=sendemail({$drow["id"]})>
								<span class='add'><i class='fas fa-calendar-plus'></i>SEND CAL INVITE</span>
								<span class='added'><i class='fas fa-calendar-check'></i>ADDED!</span>
								<span class='failed' title='Please contact admin'><i class='fas fa-times'></i>FAILED!</span>
							</a>
							<a class='aresendmail {$disClass}'  href='#' id='btnRe{$drow["id"]}' onclick=sendemail({$drow["id"]})>
								<span class='add'><i class='fas fa-calendar-plus'></i>&nbsp;RESEND CAL INVITE</span>
							</a>";
							}
								
								echo "
							</td>
						</tr>";
					}
				
				?>
			</tbody>
	</table>
	
</div>
<?php
	require_once 'templates/layout_bottom.php';
?>
<script src="js/upcoming.js"></script>
