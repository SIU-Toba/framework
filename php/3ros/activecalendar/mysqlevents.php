<?php
/*
********************************************************************************
This example script demontrates how you could create event dates with linkable contents
in the Active Calendar, using records from your MySQL database.
It requires 3 MySQL Table fields:
1. A field where a date is stored either as 'datetime' or just as an integrer (unix timestamp),
2. A field where the event content is stored
3. A field where the event link url is stored
This could be a way to develop a real 'Event Calendar', adjusted to your needs.
********************************************************************************
*/
/*
********************************************************************************
Please set the MySQL connection variables below, according to your configuration
********************************************************************************
*/
$dbhost="localhost"; // the url of your MySQL Server
$dbuser="root"; // the username of your MySQL Server
$dbpass=""; // the password of your MySQL Server
$dbname="test"; // the name of your MySQL Database
$tblname="mynews"; // the name of your MySQL Database Table
$tblDateName="newsdate"; // the name of your MySQL Table Date Field
$tblContent="newstitle"; // the name of your MySQL Table Content Field
$tblLink="newslink"; // the name of your MySQL Table Link Field
/*
********************************************************************************
Please uncomment the following lines, if you want this script to create the required MySQL Table for you.
This example table will have 2 sample events of the current day (for better view)
********************************************************************************
*/
//$SETUPSQL="CREATE TABLE `".$tblname."` (`".$tblDateName."` datetime NOT NULL default '0000-00-00 00:00:00', `".$tblContent."` char(250) NOT NULL default '', `".$tblLink."` char(250) NOT NULL default '') TYPE=MyISAM;";
//$SETUPSQL1="INSERT INTO `".$tblname."` VALUES (now(), 'ActiveCalendar', 'http://freshmeat.net/redir/activecalendar/53267/url_demo/index.html');";
//$SETUPSQL2="INSERT INTO `".$tblname."` VALUES (now(), 'Google', 'http://www.google.com/');";
//$conID=@mysql_connect($dbhost,$dbuser,$dbpass);
//@mysql_select_db($dbname, $conID);
//@mysql_query($SETUPSQL);
//@mysql_query($SETUPSQL1);
//@mysql_query($SETUPSQL2);
/*
********************************************************************************
Please use the following SQL Statement if the Date Field has a type such as 'datetime'
********************************************************************************
*/
$SQL="SELECT UNIX_TIMESTAMP(".$tblDateName.") AS ".$tblDateName.", ".$tblContent.", ".$tblLink." FROM ".$tblname;
/*
********************************************************************************
Please uncomment the following SQL Statement if the Date Field saves the date as unix timestamp (integrer)
********************************************************************************
*/
//$SQL="SELECT ".$tblDateName.", ".$tblContent.", ".$tblLink." FROM ".$tblname;
/*
********************************************************************************
Connect to Database and send the query
********************************************************************************
*/
$conID=@mysql_connect($dbhost,$dbuser,$dbpass);
@mysql_select_db($dbname, $conID);
$sqlID=@mysql_query($SQL);
/*
********************************************************************************
Calendar Navigation variables
********************************************************************************
*/
$myurl=$_SERVER['PHP_SELF']; // the links url is this page in this case
$yearID=false; // GET variable for the year (set in Active Calendar Class), init false to display current year
$monthID=false; // GET variable for the month (set in Active Calendar Class), init false to display current month
$dayID=false; // GET variable for the day (set in Active Calendar Class), init false to display current day
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
Gets all dates from your database and set the calendar events html classes (for the layout)
********************************************************************************
*/
$eventID="event"; // sets the name of the generated HTML class on the event day (css layout)
while ($data=@mysql_fetch_array($sqlID, MYSQL_BOTH)){
$mysqlDay=date("j",$data[$tblDateName]); // makes a day out of the database date
$mysqlMonth=date("n",$data[$tblDateName]); // makes a month out of the database date
$mysqlYear=date("Y",$data[$tblDateName]); // makes a year out of the database date
$mysqlContent=$data[$tblContent]; // gets the event content
$mysqlLink=$data[$tblLink]; // gets the event link
$cal->setEvent($mysqlYear,$mysqlMonth,$mysqlDay,$eventID); // set the event, if you want the whole day to be an event
$cal->setEventContent($mysqlYear,$mysqlMonth,$mysqlDay,$mysqlContent,$mysqlLink); // set the event content and link
}
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head><title>Active Calendar Class with MySQL Events</title>
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
