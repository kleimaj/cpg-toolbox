
<?php
    require_once '../resources/app_config.php';
    require_once TEMPLATES_PATH . "/layout_top.php";
	require_once TEMPLATES_PATH . "/check_auth.php";
	$current_page = array("home" => "", "upcoming" => "", "on-demand" => "active");
	require_once TEMPLATES_PATH . "/navigation.php";


	use resources\library\OnDemand;
	use resources\library\Events;
	$res_data = array();
	$res_data = OnDemand::GetResources();
	$data = array();

	if(!empty($_GET) && isset($_GET['id'])){
		$data = Events::GetEvent($_GET["id"]);	
	}

?>

	<div id="content-area">
		<h1>On-Demand Webcast Toolbox</h1>
		<h3>Stay up-to-date using our archive of resources.</h3>
		
		<ul class="filters" style="margin-bottom:0px;">
			<i class="fas fa-filter clear_filters" title="Clear all filters" id="clearall"></i>
			<?php if(empty($data)){?>
				<input id="search" type="text" placeholder="Search...">
			<?php }else{
				echo "<input id='search' type='text' placeholder='Search...' value='{$data["title"]}'>"; 
			}?>	

		</ul>
		

			
	<table id="on-demand-table" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>

				<?php 
					foreach ($res_data[0] as $drow) {

						if($drow["type"] == 'video'){
						$vidProgress = OnDemand::GetProgress($_SESSION["userid"],$drow['id']);
							$vidProgressPoints="0";
							foreach(VIDEO_MARKERS as $vm){
								if (Floor(($vidProgress['iTimeIndex']/$drow['duration']) * 100) > $vm) {
									$vidProgressPoints.=','.$vm;
								}
							}
						
					
					echo 

				"<tr>
					<td class='webcast'>
						<div class='video-block'>
							<h1>{$drow["title"]}</h1>
							<a 
							id='VidLink_{$drow["id"]}'
							class='video-link video' 
							data-duration='{$drow["duration"]}'
							data-resourceid='{$drow["id"]}'
							data-currtime={$vidProgress['iTimeIndex']}
							href='{$drow["path"]}'>
								<img src='assets/{$drow["img"]}.png'>
							</a>
							<p>{$drow["date"]}</p>
							<span>{$drow["description"]}</span>
						</div>
					</td> ";
						}else{

							$ridProgress = OnDemand::GetProgress($_SESSION["userid"],$drow['id']);

							echo 

				"<tr>
					<td class='webcast'>
						<div>
						<a class='file-pdf slide-deck {$drow["type"]}'
						id = 'res_{$drow['id']}'
						href='pdf/web/viewer.php?rid={$drow['id']}'
						target='_blank'
						data-resourceid={$drow['id']}
						data-page='{$ridProgress['iPageIndex']}'>
						{$drow["title"]}
						<img style='height:18vh;width35vw;' src='assets/{$drow["img"]}'>
						</a>
						<p>{$drow["date"]}</p>
							<span>{$drow["description"]}</span>
						</div>
					</td> ";
						}
					
					echo "<td class='supporting-materials'>
						<div class='flex-container'>";
						foreach($res_data[1][$drow["id"]] as $dsubrow) {

							$ridProgress = OnDemand::GetProgress($_SESSION["userid"],$dsubrow['id']);

							if($dsubrow["type"] == 'video'){


						echo "<div class='video-block'>
							<h1>{$dsubrow["title"]}</h1>
							<a class='video-link video'  
							   data-duration='{$dsubrow["duration"]}' 
							   href='{$dsubrow["path"]}'>
								<img src='assets/{$dsubrow["img"]}.png'>
							</a>
							<p>{$dsubrow["date"]}</p>
							<span>{$dsubrow["description"]}</span>
						</div>";

							}else{

						echo "<div>
						<a class='file-pdf slide-deck {$dsubrow["type"]}'
						id = 'res_{$dsubrow['id']}'
						href='pdf/web/viewer.php?rid={$dsubrow['id']}'
						target='_blank'
						data-resourceid={$dsubrow['id']}
						data-page='{$ridProgress['iPageIndex']}'>
						{$dsubrow["title"]}
						<img style='height:18vh;width35vw;' src='assets/{$dsubrow["img"]}'>
						</a>
						<p>{$dsubrow["date"]}</p>
						<span>{$dsubrow["description"]}</span></div>";
                       
							}
						}
							
						echo "</div>
					</td>
					
				</tr>";

				} ?>
				
			</tbody>
	</table>

	</div>

	<div id="video-container">
		<video id="speaker-video" class="video-js vjs-big-play-centered vjs-fluid" controls preload="auto" data-setup="{}" poster=""></video>
		<a class="video-close" href="#"><img src="images/close.svg"></a>
	</div>
	

<?php 

require_once 'templates/layout_bottom.php';
?>
<script src="js/on-demand.js"></script>