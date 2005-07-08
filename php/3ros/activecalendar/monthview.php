<?php
/*
********************************************************************************
This simple example script demontrates how you could create a calendar month
with a date picker control, navigation links and linkable days
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
<head><title>Active Calendar Class Month View Example</title>
<link rel="stylesheet" type="text/css" href="activecalendar.css" />
</head>
<body>
<center>
<?php 
$cal->enableMonthNav(); // this enables the month's navigation controls
$cal->enableDatePicker(2002,2006,$myurl); // this enables the date picker controls
$cal->enableDayLinks($myurl); // this enables the day links
echo $cal->showMonth(); // this displays the month's view
?>
</center>
</body>
</html>
