<?php
/*
********************************************************************************
This example script will generate a year calendar with day links that call a javascript function, instead of a page url.
The simple javacript function of this script should demonstrate that.
You can write your own javascript function, to adjust the script to your needs.
********************************************************************************
*/
include("activecalendar.php");
$yearID=false; // GET variable for the year (set in Active Calendar Class), init false to display current year
$monthID=false; // GET variable for the month (set in Active Calendar Class), init false to display current month
$dayID=false; // GET variable for the day (set in Active Calendar Class), init false to display current day
extract($_GET);
$cal=new activeCalendar($yearID,$monthID,$dayID);
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head><title>Active Calendar Class Year View with JavaScript Example</title>
<link rel="stylesheet" type="text/css" href="activecalendar.css" />
</head>
<body>
<script type="text/javascript">
function selectDate(year,month,day){
document.selectform.yearselection.value=year;
document.selectform.monthselection.value=month;
document.selectform.dayselection.value=day;
}
</script>
<center>
<b>Please click on a calendar date (javascript must be enabled!)</b>
<form name="selectform" action="">
Selected Year: <input type="text" name="yearselection" /> Selected Month: <input type="text" name="monthselection" /> Selected Day: <input type="text" name="dayselection" />
</form>
<?php 
$cal->enableYearNav(); // this enables the year's navigation controls
 // the following enables day links, that call the javascript function: selectDate(year,month,day)
$cal->enableDayLinks(false,"selectDate");
echo $cal->showYear();
?>
</center>
</body>
</html>
