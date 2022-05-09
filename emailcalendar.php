<?php

require_once '../resources/app_config.php';
require_once TEMPLATES_PATH . "/check_auth.php";

use resources\library\AppUtility;
use resources\library\Events;

$semail = new AppUtility();
$data = Events::GetEvent($_POST["id"]);

if(empty($data)){
    echo 'fail';
    return;
}

$emailText = "<p>CPG ToolBox Webcast Invite for : " . $data["title"]  . "</p>
<p>Hello " .$_SESSION["UserFullName"]  .",</p>

<p>This is your invitation from CPG Toolbox for a Webcast :
<br>Title : <strong>" . $data["title"]."</strong>
<br>Date : <strong>".$data["date"].", " .$data["day"]."</strong>
<br>Duration : <strong>".$data["duration"]."hrs </strong>
<br>Webcast URL : <a href=" .$data["url"]."  rel='noopener noreferrer' target='_blank'><strong>" . $data["url"]."</strong></a>
<br><br>Please find the attached ical and add it to your calendar. 
<br><br> More Information : " .$data["description"]. "</p>
<br>
<p>Thank you!</p>
Communication Partners Group LLC";



$resultObj = $semail->SendEmail(
$fromEmail= "gourik@communicationpartners.com",
$fromName= "CPG",
$toEmail= $_SESSION["mail"],
$subject="CPG ToolBox Webcast Invite for : " . $data["title"] ,
$body= $emailText, 
$ical=generate_ics($data));


echo $resultObj->Status;

if($resultObj->Status == 'Pass'){
    $result = Events::SaveData($_POST["id"],1);
}else{
    //fail
    $result = Events::SaveData($_POST["id"],0);
}
return;



function dateToCal($dt){
    $timezone = new DateTimeZone('EST') ; 
    $eventdate = new DateTime ( $dt, $timezone);
    return $eventdate->format('Ymd\THis\Z') ;
}

function generate_ics($data){
    $ical_content = 
'BEGIN:VCALENDAR
VERSION:2.0
PRODID:-iCal Event Maker
CALSCALE:GREGORIAN
BEGIN:VTIMEZONE
TZID:America/New_York
TZURL:http://tzurl.org/zoneinfo-outlook/America/New_York
X-LIC-LOCATION:America/New_York
BEGIN:DAYLIGHT
TZNAME:EST
TZOFFSETFROM:-0500
TZOFFSETTO:-0000
DTSTART:19700308T020000
RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=2SU
END:DAYLIGHT
BEGIN:STANDARD
TZNAME:EST
TZOFFSETFROM:-0500
TZOFFSETTO:-0000
DTSTART:19701101T020000
RRULE:FREQ=YEARLY;BYMONTH=11;BYDAY=1SU
END:STANDARD
END:VTIMEZONE
BEGIN:VEVENT
DTSTAMP:' . time() . '
UID:' . md5($data["title"]) . '
DTSTART;TZID=America/New_York:'  . dateToCal(date_format($data["sDate"],"Y-m-d H:i:s")) .'
DTEND;TZID=America/New_York:'  . dateToCal(date_format($data["eDate"],"Y-m-d H:i:s")) .'
SUMMARY:' . $data["title"].'
DESCRIPTION:' .$data["url"] . '
LOCATION:Webcast
END:VEVENT                     
END:VCALENDAR';
    
return  $ical_content;
}
                     


