<?php

include("activecalendar.php");
$yearID=false;
$monthID=false;
$dayID=false;
$weekID=false;
$showcal=false;
extract($_GET);
$cal=new activeCalendar($weekID,$yearID,$monthID,$dayID);
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"activecalendar.css\"></link>\n";
?>
<style type="text/css">
<!--
.code {font-family: "Courier New", Courier, Arial, mono; font-size: 12px; font-weight: bold; color: #000099; text-align: right}
.explain {font-family: Tahoma, Arial, mono; font-size: 12px; font-weight: bold; color: #000000; text-align: left}
.explain2 {font-family: "Courier New", Courier, Arial, mono; font-size: 12px; font-weight: bold; color: #000099; text-align: left}
.mtable {border-width: 2px; border-style:outset; border-color: #eeeeee;}
.small { font-family: Helvetica, Tahoma, Arial, sans-serif; font-size: 10px}
-->
</style>
</head>
<body>
<center>
<h2>Calendario</h2>
<?php
$cal->enableDatePicker(2000,2010);
$cal->enableDayLinks();
$cal->enableWeekLinks();
$cal->enableDatePicker();
$out = $cal->showMonth();
echo $out;
?>