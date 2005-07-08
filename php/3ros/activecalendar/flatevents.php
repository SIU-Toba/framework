<?php
/*
********************************************************************************
This example script demontrates how you could create event dates with linkable contents
in the Active Calendar, using records from a flat file.
The event file is included in this package as 'flatevents.txt':
Please make sure that this script has the rights to read it.
********************************************************************************
*/

/*
********************************************************************************
The following function reads the file and returns the elementa of each line as an array
********************************************************************************
*/
function eventsFromFile($filepath,$divideStr) {
	if(file_exists($filepath)){
		$line = file($filepath);
			for($x=0;$x<count($line);$x++){
				$lineArray=split($divideStr,$line[$x]);
				if (is_array($lineArray)) $eventsArray[$x]=$lineArray;
			}
		return $eventsArray;
	}
	else return false;
}

/*
********************************************************************************
Calendar Navigation variables
********************************************************************************
*/
$myurl=$_SERVER['PHP_SELF']; // the links url is this page in this case
$yearID=2005; // GET variable for the year, init 2005 to display the year of the event file
$monthID=4; // GET variable for the month, init 4 to display the month of the event file
$dayID=false; // GET variable for the day, init false to display current day
extract($_GET);
/*
********************************************************************************
Create a calendar object
********************************************************************************
*/
include("activecalendar.php");
$cal=new activeCalendar($yearID,$monthID,$dayID);
/*
********************************************************************************
Read the event file and set the events
********************************************************************************
*/
$filePath="flatevents.txt"; // the path of the events flat file
$divideStr="~"; // the divider between events in the events flat file
$eventID="event"; // sets the name of the generated HTML class on the event day (css layout)
$evts=eventsFromFile($filePath,$divideStr);
	if ($evts){
		for($x=0;$x<count($evts);$x++) {
			//$cal->setEvent(@$evts[$x][0],@$evts[$x][1],@$evts[$x][2],$eventID);
			$cal->setEventContent(@$evts[$x][0],@$evts[$x][1],@$evts[$x][2],@$evts[$x][3],@$evts[$x][4]);
		}
	}
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head><title>Active Calendar Class with Flat File Events</title>
<link rel="stylesheet" type="text/css" href="activecalendar1.css" />
</head>
<body>
<center>
<?php
/*
********************************************************************************
Uncomment the following to display a month calendar with the MySQL events
********************************************************************************
*/
$cal->enableDatePicker(2002,2006); // this enables the month's datepicker (year range 2002 - 2006)
$cal->enableDayLinks($myurl); // this enables the month's day links
$cal->enableMonthNav($myurl); // this enables the month's navigation controls
echo $cal->showMonth(); // this displays the month's view
/*
********************************************************************************
Uncomment the following to display a year calendar with the MySQL events
For better view, please comment or remove the lines above, that generate the month calendar
********************************************************************************
*/
//$cal->enableDatePicker(2002,2006); // this enables the years's datepicker (year range 2002 - 2006)
//$cal->enableYearNav($myurl); // this enables the years's navigation controls
//echo $cal->showYear(); // this displays the year's view
?>
</center>
</body>
</html>

