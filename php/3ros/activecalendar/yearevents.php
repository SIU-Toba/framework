<?php
/*
********************************************************************************
This simple example script demontrates how you could create a calendar year
with event days as well as days with event content (optionally linkable).
Moreover, different month and day names will be set, than the ones used in the configuration.
(The layout may be not the best in this case, but I have chosen the year view for this examples, as it gives a better overview of the methods output)
********************************************************************************
*/
include("activecalendar.php");
$myurl=$_SERVER['PHP_SELF']; // the links url is this page in this case
$yearID=false; // GET variable for the year (set in Active Calendar Class), init false to display current year
$monthID=false; // GET variable for the month (set in Active Calendar Class), init false to display current month
$dayID=false; // GET variable for the day (set in Active Calendar Class), init false to display current day
extract($_GET);
$cal=new activeCalendar($yearID,$monthID,$dayID);
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head><title>Active Calendar Class Year View with Events Example</title>
<link rel="stylesheet" type="text/css" href="activecalendar1.css" />
</head>
<body>
<center>
<?php
$monthName=array("1","2","3","4","5","6","7","8","9","10","11","12"); // The months will not have a name, just a number
$dayName=array("So","Mo","Di","Mi","Do","Fr","Sa"); // The days will be in german language
$cal->monthYearDivider=" / "; // this creates a different divider between month and year in the month`s title
$cal->setMonthNames($monthName);
$cal->setDayNames($dayName);
$cal->enableYearNav($myurl); // this enables the year's navigation controls
$cal->enableDatePicker(2003,2008); // this enables the date picker (year range 2003 - 2008)
$cal->enableDayLinks($myurl); // this enables the day links
$cal->setEvent(2005,5,7); // this sets an event on 7 May 2004
$cal->setEvent(2005,12,20); // this sets an event on 20 December 2004
if (!$yearID) $yearID=date("Y"); // make sure there is an $yearID (for the following functions)
/*
********************************************************************************
set an event on 11 August every year
********************************************************************************
*/
$cal->setEvent($yearID,8,11);
/*
********************************************************************************
set an event on the 4th from January till April every year
********************************************************************************
*/
for ($x=1;$x<=4;$x++){
$cal->setEvent($yearID,$x,4);
}
/*
********************************************************************************
set an event from 14 June till 18 June every year
********************************************************************************
*/
for ($x=14;$x<=18;$x++){
$cal->setEvent($yearID,6,$x);
}
/*
********************************************************************************
set an event content from 5 July till 8 July every year
(in this case Google with link)
********************************************************************************
*/
for ($x=5;$x<=8;$x++){
$cal->setEventContent($yearID,7,$x,"Google","http://www.google.com");
}
/*
********************************************************************************
set an event content on 15 March every year
the method setEventContent() accepts also an array as content parameter (4th)
if you pass an array a content table with multiple (as many as the array length) tr tags will be generated
********************************************************************************
*/
$multipleLinesEvent=array("Title:News","Time:16.00","Status:ok");
$cal->setEventContent($yearID,3,15,$multipleLinesEvent);
/*
********************************************************************************
create the year view
********************************************************************************
*/
echo $cal->showYear(2); // this displays the year's view (parameter '2': 2 months in each row)
?>
</center>
</body>
</html>
