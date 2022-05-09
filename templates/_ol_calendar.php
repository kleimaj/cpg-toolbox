<?php 
require_once '../../resources/app_config.php';

use resources\library\AppUtility;
use resources\library\OLActivity;
use donatj\SimpleCalendar;


if (!empty($_GET["show"])) {
    $showMonth = new DateTime($_GET["show"]);
} else {
    $showMonth = new DateTime();
}

$prevMonth = date("Y-m-01", strtotime($showMonth->format('Y-m-01').' -1 month'));
$nextMonth = date("Y-m-01", strtotime($showMonth->format('Y-m-01').' +1 month'));;

$calendar = new SimpleCalendar($showMonth->format('F j, Y'));
$calendar->wday_names = ['Su','Mo','Tu','We','Th','Fr','Sa'];


$OLActivity = OLActivity::GetActivitiesForOL($_SESSION["OLID"], null, $showMonth->format('Y-m-01'), $showMonth->format('Y-m-t'));

foreach($OLActivity as $activity){
    $actStr = '<div data-eventid="'.$activity['OLActivityID'].'"><img src="images/add-event.svg"><span>'.$activity['MeetingType'].'</span></div>';
    $date = $activity["StartDate"]->format('Y-m-d');
    while($date <= $activity["EndDate"]->format('Y-m-d')){
        $calendar->addDailyHtml($actStr, $date);
        $date = date('Y-m-d', strtotime($date . ' +1 day'));
    }
}


// $firstDay = $showMonth->format("Y-m-01");
// $lastDay = $showMonth->format("Y-m-t");
// $activities = OLActivity::GetActivitiesForDate($_SESSION['ProductGroup'], $firstDay, $lastDay);
// foreach($activities as $activity){
//     echo '<p>' . $activity['FullName'] . " " . $activity['Location'] . " " . $activity["MeetingType"] . " " . $activity['MeetingName'].'</p>';
// }

    echo '<div class="calender-header"><span></span><div><a href="#" class="calendar-nav" data-goto="'. $prevMonth .'"><i class="fas fa-chevron-left"></i></a>';
    echo "<strong id=\"Date-Title\">{$showMonth->format('F, Y')}<i class=\"fas fa-calendar-week\" style=\"padding-left:0.5em;\"></i></strong>
            <input type=\"hidden\" name=\"hdDateSelect\" id=\"hdDateSelect\" class=\"date-picker\" value=\"{$showMonth->format('Y-m-d')}\" />";
    echo '<a href="#" class="calendar-nav" data-goto="'. $nextMonth .'"><i class="fas fa-chevron-right"></i></a></div>';


    if ($_SESSION["Claims"]["AddEvent"]) { 
        require_once TEMPLATES_PATH . "/_activity_form.php";
        require_once TEMPLATES_PATH . "/_OL_locator.php";

        echo '<ul class="calendar-btns">
                <li><a class="add-event" href="#"><img src="images/add-event.svg"><span>Add an Event</span></a></li>
                <li><a class="ol-locator" href="#"><img src="images/location.svg"><span>OL Locator</span></a></li>
            </ul>';
    } else {
        echo '<ul class="calendar-btns"><li></li><li></li></ul>';
    }
?>
</div>
<!-- <div id="datepicker"></div> -->
<div id="CalendarFrame">
<?php $calendar->show();  ?>
</div>    

<!--Event Details Modal -->
<div id="event-details-modal" class="event-details">
    <a class="close-btn" href="#"><i class="fas fa-times"></i>Cancel</a>
    <img src="images/add-event.svg">
    <h1>Event Details</h1>
    <div id="event-details-content"></div>
    <?php if ($_SESSION["Claims"]["AddEvent"]) { 
        echo '<div class="button__container">
                <button id="edit-open" type="submit"><i class="fas fa-edit"></i>Edit</button>
            </div>';   
    }?>
</div>


<?php if ($_SESSION["Claims"]["AddEvent"]) {  ?>
<!--Event edit Modal -->
<div id="event-edit-modal" class="event-details">
    <a class="close-btn" href="#"><i class="fas fa-times"></i>Cancel</a>
    <img src="images/add-event.svg">
    <h1>Edit Event</h1>
    <div id="event-edit-content"></div>
</div>
<?php } ?>


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
    
    $(".add-event").click(function(){
        $("#add-event-modal").show();
    });
    
    $(".ol-locator").click(function(){
        $("#ol-locator-modal").show();
    });

    $("#CalendarFrame").on("click","div.event",function(evt){
        var evtid  = $(this).children("div:first-child").data('eventid');
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

    $("#edit-open").click(function(){
        let evtid = $("#event-id").val();
        $.get('templates/_activity_edit.php',
            {'eventid':evtid},
            function(data) {
                $("#event-details-content").html('');
                $("#event-details-modal").hide();
                $("#event-edit-content").html(data);
                $("#event-edit-modal").show();
            },
            'html'
        );
    });

    $(".close-btn","#event-edit-modal").click(function(){
        $("#event-edit-content").html('');
        $("#event-edit-modal").hide();
    });

    $(".close-btn","#event-details-modal").click(function(){
        $("#event-details-content").html('');
        $("#event-details-modal").hide();
    });
</script>


			