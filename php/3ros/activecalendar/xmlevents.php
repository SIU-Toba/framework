<?php
/*
********************************************************************************
This example script demontrates how you could create event dates with linkable contents
in the Active Calendar, using records from an xml file.
The event file is included in this package as 'xmlevents.xml':
Please make sure that this script has the rights to read it.
Keep in mind that this script does not use any real xml parser (you may not have one).
It just 'parses' the xml file as it is. So do not change its basic structure.
The element names can be changed though and of course more events and contents can be added!
If you have an xml parser just modify this script according to its methods...
********************************************************************************
*/
/*
********************************************************************************
Calendar Navigation variables
********************************************************************************
*/
$myurl=$_SERVER['PHP_SELF']; // the links url is this page in this case
$yearID=2005; // GET variable for the year, init 2005 to display the year of the event file
$monthID=10; // GET variable for the month, init 4 to display the month of the event file
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
Set the path and the tag names, used in the xml file
********************************************************************************
*/
$filePath="xmlevents.xml"; // the path of the events xml file
$eventYearTag="eventyear"; // unique name for the event year
$eventMonthTag="eventmonth"; // unique name for the event month
$eventDayTag="eventday"; // unique name for the event day
$eventStyleTag="eventstyle"; // unique name for the event ID (CSS layout)
$eventLinkTag="eventlink"; // unique name for the event link
$eventContYearTag="contentyear"; // unique name for the event content year
$eventContMonthTag="contentmonth"; // unique name for the event content month
$eventContDayTag="contentday"; // unique name for the event content day
$eventContTag="contents"; // unique name for the event contents
$eventContItemTag="item"; // unique name for the event contents values
$eventContLinkTag="contentlink"; // unique name for the event content link
/*
********************************************************************************
Call the 'parser' functions of this script and set the events
********************************************************************************
*/
$evts=getXMLEvents($filePath,$eventYearTag,$eventMonthTag,$eventDayTag,$eventStyleTag,$eventLinkTag);
	if ($evts){
		for($x=0;$x<count($evts[$eventYearTag]);$x++){
			$cal->setEvent($evts[$eventYearTag][$x],$evts[$eventMonthTag][$x],$evts[$eventDayTag][$x],$evts[$eventStyleTag][$x],$evts[$eventLinkTag][$x]);

		}
	}
$evts=getXMLEventContents($filePath,$eventContYearTag,$eventContMonthTag,$eventContDayTag,$eventContTag,$eventContItemTag,$eventContLinkTag);
	if ($evts){
		for($x=0;$x<count($evts[$eventContYearTag]);$x++){
			$cal->setEventContent($evts[$eventContYearTag][$x],$evts[$eventContMonthTag][$x],$evts[$eventContDayTag][$x],$evts[$eventContTag][$x],$evts[$eventContLinkTag][$x]);
		}
	}
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head><title>Active Calendar Class with XML Events</title>
<link rel="stylesheet" type="text/css" href="activecalendar1.css" />
</head>
<body>
<center>
<?php
if (file_exists($filePath)) echo "<i>This calendar uses events from the following file: <a href=\"".$filePath."\" target=\"_blank\">'".$filePath."'</a></i>\n";
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
<?php
/*
********************************************************************************
The following functions read the xml file and return the required arrays to set the calendar events.
Please keep in mind that they are not really a xml parser! :)
Do not call the functions _getTagContent() and _getTagChildContent() directly.
********************************************************************************
*/
function getXMLEvents($filepath,$yearTag,$monthTag,$dayTag,$styleTag,$linkTag) {
$result=false;
	if (file_exists($filepath)){
		$file=file($filepath);
		$years=_getTagContent($file,$yearTag);
		$months=_getTagContent($file,$monthTag);
		$days=_getTagContent($file,$dayTag);
		$styles=_getTagContent($file,$styleTag);
		$links=_getTagContent($file,$linkTag);
		for ($x=0;$x<count($years);$x++) $result[$yearTag]=$years[$x];
		for ($x=0;$x<count($months);$x++) $result[$monthTag]=$months[$x];
		for ($x=0;$x<count($days);$x++) $result[$dayTag]=$days[$x];
		for ($x=0;$x<count($styles);$x++) $result[$styleTag]=$styles[$x];
		for ($x=0;$x<count($links);$x++) $result[$linkTag]=$links[$x];
	}
return $result;
}
function getXMLEventContents($filepath,$yearTag,$monthTag,$dayTag,$contentTag,$itemTag,$linkTag){
$result=false;
	if (file_exists($filepath)){
		$file=file($filepath);
		$years=_getTagContent($file,$yearTag);
		$months=_getTagContent($file,$monthTag);
		$days=_getTagContent($file,$dayTag);
		$links=_getTagContent($file,$linkTag);
		$contents=_getTagChildContent($file,$contentTag,$itemTag);
		for ($x=0;$x<count($years);$x++) $result[$yearTag]=$years[$x];
		for ($x=0;$x<count($months);$x++) $result[$monthTag]=$months[$x];
		for ($x=0;$x<count($days);$x++) $result[$dayTag]=$days[$x];
		for ($x=0;$x<count($links);$x++) $result[$linkTag]=$links[$x];
		$result[$contentTag]=$contents;
	}
return $result;
}
function _getTagContent($file,$tag){
$openTag="<".$tag.">";
$closeTag="</".$tag.">";
$line = $file;
	for($x=0;$x<count($line);$x++){
		if (strpos($line[$x],$openTag)){
			$temp=str_replace($openTag,"",$line[$x]);
			$end=trim(str_replace($closeTag,"",$temp));
				if ($end=="" || !$end) $end=false;
			$cont[]=$end;
		}
	}
$result=array($cont);
return $result;
}
function _getTagChildContent($file,$parentTag,$childTag){
$openParentTag="<".$parentTag.">";
$closeParentTag="</".$parentTag.">";
$openChildTag="<".$childTag.">";
$closeChildTag="</".$childTag.">";
$line = $file;
	for($x=0;$x<count($line);$x++){
		if (strpos($line[$x],$openParentTag)) $curStartLine[]=$x;
		if (strpos($line[$x],$closeParentTag)) $curStopLine[]=$x;
	}
	for ($i=0;$i<count($curStartLine);$i++){
		for ($x=$curStartLine[$i];$x<$curStopLine[$i];$x++){
			if (strpos($line[$x],$openChildTag)){
				$temp=str_replace($openChildTag,"",$line[$x]);
				$end=trim(str_replace($closeChildTag,"",$temp));
				$end=str_replace("<!--","",$end);
				$end=str_replace("-->","",$end);
					if ($end=="" || !$end) $end=false;
				$cont[$i][]=$end;
			}
		}
	}
return $cont;
}
?>
