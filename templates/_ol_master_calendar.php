<?php 
require_once '../../resources/app_config.php';

use resources\library\Events;
use donatj\SimpleCalendar;

$event_list = Events::GetEvents();


if (!empty($_GET["show"])) {
    $showMonth = new DateTime($_GET["show"]);
} else {
    $showMonth = new DateTime();
}


$prevMonth = date("Y-m-01", strtotime($showMonth->format('Y-m-01').' -1 month'));
$nextMonth = date("Y-m-01", strtotime($showMonth->format('Y-m-01').' +1 month'));;

$calendar = new SimpleCalendar($showMonth->format('F j, Y'));
$calendar->wday_names = ['Su','Mo','Tu','We','Th','Fr','Sa'];


$firstDay = $showMonth->format("Y-m-01");
$lastDay = $showMonth->format("Y-m-t");
$activities = Events::GetEvents($firstDay, $lastDay);
$eventsForDate = [];

foreach($activities as $activity){
    $date = $activity["StartDate"]->format('Y-m-d');
    while($date <= $activity["EndDate"]->format('Y-m-d')){
        if(array_key_exists($date, $eventsForDate)){
            if($activity['Locator'] && array_key_exists('locator', $eventsForDate[$date])){
                $eventsForDate[$date]["locator"]++;
                $eventsForDate[$date]["loc_list"] .= "<li id='{$activity['OLActivityID']}' name='{$activity['FullName']}'><i class='fas fa-map-marker-alt></i>{$activity["FullName"]} ({$activity["Location"]})</li>";
            }else if($activity['Locator']){
                $eventsForDate[$date]["locator"] = 1;
                $eventsForDate[$date]["loc_list"] = "<li id='{$activity['OLActivityID']}' name='{$activity['FullName']}'><i class='fas fa-map-marker-alt'></i>{$activity["FullName"]} ({$activity["Location"]})</li>";
            }else if(!array_key_exists('event', $eventsForDate[$date])){
                $eventsForDate[$date]["event"] = 1;
                $eventsForDate[$date]["evt_list"] = "<li id='{$activity['OLActivityID']}' name='{$activity['FullName']}'><i class='far fa-calendar-alt'></i>{$activity["FullName"]} ({$activity["Location"]})</li>";
            }else{
                $eventsForDate[$date]["event"]++;
                $eventsForDate[$date]["evt_list"] .= "<li id='{$activity['OLActivityID']}' name='{$activity['FullName']}'><i class='far fa-calendar-alt'></i>{$activity["FullName"]} ({$activity["Location"]})</li>";
            }
        }else{
            if($activity['Locator']){
                $eventsForDate[$date]["locator"] = 1;
                $eventsForDate[$date]["loc_list"] = "<li id='{$activity['OLActivityID']}' name='{$activity['FullName']}'><i class='fas fa-map-marker-alt'></i>{$activity["FullName"]} ({$activity["Location"]})</li>";
            }else{
                $eventsForDate[$date]["event"] = 1;
                $eventsForDate[$date]["evt_list"] = "<li id='{$activity['OLActivityID']}' name='{$activity['FullName']}'><i class='far fa-calendar-alt'></i>{$activity["FullName"]} ({$activity["Location"]})</li>";
            }
        }

        $date = date('Y-m-d', strtotime($date . ' +1 day'));
    }
}

foreach($eventsForDate as $date => $event){
    $locator_count = array_key_exists("locator", $event) ? $event['locator'] : '0';
    $event_count = array_key_exists("event", $event) ? $event['event'] : '0';
    $actStr = '<div><i class="far fa-calendar-alt"></i><span class="master">'.$event_count.'</span></div><div><i class="fas fa-map-marker-alt"></i><span class="master">'.$locator_count.'</span></div>';
    $calendar->addDailyHtml($actStr, $date);
}

?>

<?php 
    echo '<div class="calender-header"><span></span><div><a href="#" class="calendar-nav" data-goto="'. $prevMonth .'"><i class="fas fa-chevron-left"></i></a>';
    echo "<strong id=\"Date-Title\">{$showMonth->format('F, Y')}<i class=\"fas fa-calendar-week\" style=\"padding-left:0.5em;\"></i></strong>
            <input type=\"hidden\" name=\"hdDateSelect\" id=\"hdDateSelect\" class=\"date-picker\" value=\"{$showMonth->format('Y-m-d')}\" />";
    echo '<a href="#" class="calendar-nav" data-goto="'. $nextMonth .'"><i class="fas fa-chevron-right"></i></a></div>';
?>

    <ul class="calendar-btns">
        <li></li>
    </ul>
</div>

<!-- <div id="datepicker"></div> -->
<div id="CalendarFrame">
<?php $calendar->show();  ?>
</div>    



<!--Event Details Modal -->

<!--<div id="event-details-modal" class="event-details">
    <a class="close-btn" href="#"><i class="fas fa-times"></i>Cancel</a>
    <img src="images/add-event.svg">
    <h1>Event Details<div></div></h1>
    <div id="event-details-content"></div>
</div>-->


<!--All Events Modal -->
<!--<div id="all-events-modal" class="event-details">
    <a class="close-btn" href="#"><i class="fas fa-times"></i>Cancel</a>
    <h1>All Events</h1>
    <p id="date"></p>
    <div id="all-events-content">
        <hr>
        <ul id="all-events-list">
        </ul>
        <hr>
    </div>
</div>--> 

<script type="text/javascript">
        $(function() {
            $('.date-picker').datepicker( {
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            minDate:"2020-01-01",
            maxDate:"+5y",
            autoSize:true,
            dateFormat : 'yy-mm-dd',
            monthNamesShort: [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ],
            
            onClose: function(dateText, inst) { 
                $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
                $(this).trigger("change");
            }
            });
            $("#Date-Title").on("click",function(e) { $('.date-picker').datepicker("show"); });
        });
    </script>
    
<script>
    var events = <?php echo json_encode($eventsForDate); ?>;
    
    $("#CalendarFrame").on("click","div.event",function(evt){
        $("#all-events-list").html('');
        $("#date").html('');

        let date_string = $(this).parent().find('time').attr('datetime');
        let date = new Date(date_string);
        const month = date.toLocaleString('default', { month: 'long' });
        
        data = {}
        data.evt_list = "evt_list" in events[date_string] ? events[date_string]["evt_list"] : "";
        data.loc_list = "loc_list" in events[date_string] ? events[date_string]["loc_list"] : "";
        
        $("#date").append(month + " " + date.getUTCDate() + ", " + date.getFullYear());
        $("#all-events-list").append(data.evt_list);
        $("#all-events-list").append(data.loc_list);
        $("#all-events-modal").show();
    });
    
    $("#all-events-list").on('click', 'li', function(evt){
        var evtid  = $(this).attr('id');
	$('#event-details-modal > h1 > div').html('').html( $(this).attr('name') );

        var now = new Date();
        $.get('templates/_activity_details.php',
            {'eventid':evtid,'t':now.getTime()},
            function(data) {
                $("#event-details-content").html(data);
                $("#event-details-modal").show();
            },
            'html'
        );
    });

    $(".close-btn","#event-details-modal").click(function(){
        $("#event-details-content").html('');
        $("#event-details-modal").hide();
    });

    $(".close-btn","#all-events-modal").click(function(){
        $("#all-events-list").html('');
        $("#date").html('');
        $("#all-events-modal").hide();
    });
</script>




			