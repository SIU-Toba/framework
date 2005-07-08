<?php
include("activecalendar.php");
$yearID=false;
$monthID=false;
$dayID=false;
$showcal=false;
$suiteurl="testsuite.php";
extract($_GET);
$cal=new activeCalendar($yearID,$monthID,$dayID);
print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head><title>Active Calendar Class Testsuite</title>
<?php
if ($showcal>6) echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"activecalendar1.css\"></link>\n";
else echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"activecalendar.css\"></link>\n";
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
<h2>Active Calendar Class Testsuite v1.0</h2>
<?php
if (!$showcal && !$yearID && !$monthID && !$dayID){
$out="<p>Please click on the following links to check some combinations of the Active Calendar Class functions.</p>\n";
$out.="<table border=\"0\" class=\"mtable\">\n";
$out.="<tr><td class=\"code\">Class method(s)</td>";
$out.="<td class=\"explain2\">Generated calendar(s)</td></tr>\n";
$out.="<tr><td class=\"code\"><a href=\"".$suiteurl."?showcal=1\">[ showMonth() ] </a></td>";
$out.="<td class=\"explain\">Current month static</td></tr>\n";
$out.="<tr><td class=\"code\"><a href=\"".$suiteurl."?showcal=2\">[ enableMonthNav() + enableDatePicker(2000,2010) + showMonth() ] </a></td>";
$out.="<td class=\"explain\">Current month with navigation and date picker (year range:2000-2010)</td></tr>\n";
$out.="<tr><td class=\"code\"><a href=\"".$suiteurl."?showcal=3\">[ enableDayLinks() + showMonth() ] </a></td>";
$out.="<td class=\"explain\">Current month with day links</td></tr>\n";
$out.="<tr><td class=\"code\"><a href=\"".$suiteurl."?showcal=4\">[ showYear(3) ] </a></td>";
$out.="<td class=\"explain\">Current year static (3 months in each row)</td></tr>\n";
$out.="<tr><td class=\"code\"><a href=\"".$suiteurl."?showcal=5\">[ enableYearNav() + showYear() ] </a></td>";
$out.="<td class=\"explain\">Current year with navigation</td></tr>\n";
$out.="<tr><td class=\"code\"><a href=\"".$suiteurl."?showcal=6\">[ setEvent(2007,1,23) + setEvent(2007,8,2) + showYear(2007) ] </a></td>";
$out.="<td class=\"explain\">Year (2007) with 'event days'</td></tr>\n";
$out.="<tr bgcolor=\"#eeeeee\"><td class=\"code\"><a href=\"".$suiteurl."?showcal=7\">[ setEventContent(2007,1,11,\"some content\") + showMonth(2007,1) ] </a></td>";
$out.="<td class=\"explain\">11 January 2007 with an 'event content' as a string *</td></tr>\n";
$out.="<tr bgcolor=\"#eeeeee\"><td class=\"code\"><a href=\"".$suiteurl."?showcal=8\">[ setEventContent(2007,1,11,\"some content\",\"".$suiteurl."\") + showMonth(2007,1) ] </a></td>";
$out.="<td class=\"explain\">11 January 2007 with a linkable 'event content' as a string *</td></tr>\n";
$out.="<tr bgcolor=\"#eeeeee\"><td class=\"code\"><a href=\"".$suiteurl."?showcal=9\">[ \$eventContent=array(\"content1\",\"content2\",\"content3\") <br />setEventContent(2007,1,11,\$eventContent) + showMonth(2007,1) ] </a></td>";
$out.="<td class=\"explain\">11 January 2007 with an 'event content' as an array *</td></tr>\n";
$out.="<tr><td class=\"code\" colspan=\"2\">* New public methods (v1.0)! Please report any bugs or feature enhancements.</td></tr>\n";
$out.="</table>\n";
echo $out;
}
if ($showcal==1){
$out="<a href=\"".$suiteurl."\">Check another function</a>";
$out.="<table><tr><td bgcolor=\"#ffff99\" class=\"code\">";
$out.="Function <b>showMonth()</b> generates the following calendar:";
$out.="</td></tr></table><br />";
$out.=$cal->showMonth();
$out.="<br /><a href=\"http://validator.w3.org/check?uri=referer\" target=\"_blank\">Validate this XHTML <span class=\"small\">(results in a new window)</span></a>";
echo $out;
}
if ($showcal==2 || (!$showcal && $yearID && $monthID && !$dayID)){
$out="<a href=\"".$suiteurl."\">Check another function</a>";
$out.="<table><tr><td bgcolor=\"#ffff99\" class=\"code\">";
$out.="Functions <b>enableMonthNav()</b>, <b>enableDatePicker(2000,2010)</b> and <b>showMonth()</b> generate the following calendar(s):";
$out.="</td></tr></table><br />";
$cal->enableMonthNav($suiteurl);
$cal->enableDatePicker(2000,2010);
$out.= $cal->showMonth();
$out.="<br /><a href=\"http://validator.w3.org/check?uri=referer\" target=\"_blank\">Validate this XHTML <span class=\"small\">(results in a new window)</span></a>";
echo $out;
}
if ($showcal==3 || (!$showcal && $yearID && $monthID && $dayID)){
$out="<a href=\"".$suiteurl."\">Check another function</a>";
$out.="<table><tr><td bgcolor=\"#ffff99\" class=\"code\">";
$out.="Functions <b>enableDayLinks()</b> and <b>showMonth()</b> generate the following calendar:";
$out.="</td></tr></table><br />";
$cal->enableDayLinks($suiteurl);
$out.= $cal->showMonth();
$out.="<br /><a href=\"http://validator.w3.org/check?uri=referer\" target=\"_blank\">Validate this XHTML <span class=\"small\">(results in a new window)</span></a>";
echo $out;
}
if ($showcal==4){
$out="<a href=\"".$suiteurl."\">Check another function</a>";
$out.="<table><tr><td bgcolor=\"#ffff99\" class=\"code\">";
$out.="Function <b>showYear(3)</b> generates the following calendar:";
$out.="</td></tr></table><br />";
$out.=$cal->showYear(3);
$out.="<br /><a href=\"http://validator.w3.org/check?uri=referer\" target=\"_blank\">Validate this XHTML <span class=\"small\">(results in a new window)</span></a>";
echo $out;
}
if ($showcal==5 || (!$showcal && $yearID && !$monthID && !$dayID)){
$out="<a href=\"".$suiteurl."\">Check another function</a>";
$out.="<table><tr><td bgcolor=\"#ffff99\" class=\"code\">";
$out.="Functions <b>enableYearNav()</b> and <b>showYear()</b> generate the following calendar(s):";
$out.="</td></tr></table><br />";
$cal->enableYearNav($suiteurl);
$out.= $cal->showYear();
$out.="<br /><a href=\"http://validator.w3.org/check?uri=referer\" target=\"_blank\">Validate this XHTML <span class=\"small\">(results in a new window)</span></a>";
echo $out;
}
if ($showcal==6){
$out="<a href=\"".$suiteurl."\">Check another function</a>";
$out.="<table><tr><td bgcolor=\"#ffff99\" class=\"code\">";
$out.="Functions <b>setEvent(2007,1,23) + setEvent(2007,8,2) + showYear(2007)</b> generate the following calendar:";
$out.="</td></tr></table><br />";
$cal=new activeCalendar(2007);
$cal->setEvent(2007,1,23);
$cal->setEvent(2007,8,2);
$out.=$cal->showYear();
$out.="<br /><a href=\"http://validator.w3.org/check?uri=referer\" target=\"_blank\">Validate this XHTML <span class=\"small\">(results in a new window)</span></a>";
echo $out;
}
if ($showcal==7){
$out="<a href=\"".$suiteurl."\">Check another function</a>";
$out.="<table><tr><td bgcolor=\"#ffff99\" class=\"code\">";
$out.="Functions <b>setEventContent(2007,1,11,\"some content\") + showMonth(2007,1)</b> generate the following calendar:";
$out.="</td></tr></table><br />";
$cal=new activeCalendar(2007,1);
$cal->setEventContent(2007,1,11,"some content");
$out.=$cal->showMonth();
$out.="<br /><a href=\"http://validator.w3.org/check?uri=referer\" target=\"_blank\">Validate this XHTML <span class=\"small\">(results in a new window)</span></a>";
echo $out;
}
if ($showcal==8){
$out="<a href=\"".$suiteurl."\">Check another function</a>";
$out.="<table><tr><td bgcolor=\"#ffff99\" class=\"code\">";
$out.="Functions <b>setEventContent(2007,1,11,\"some content\",\"".$suiteurl."\") + showMonth(2007,1)</b> generate the following calendar:";
$out.="</td></tr></table><br />";
$cal=new activeCalendar(2007,1);
$cal->setEventContent(2007,1,11,"some content",$suiteurl);
$out.=$cal->showMonth();
$out.="<br /><a href=\"http://validator.w3.org/check?uri=referer\" target=\"_blank\">Validate this XHTML <span class=\"small\">(results in a new window)</span></a>";
echo $out;
}
if ($showcal==9){
$out="<a href=\"".$suiteurl."\">Check another function</a>";
$out.="<table><tr><td bgcolor=\"#ffff99\" class=\"explain2\">";
$out.="\$eventContent=array(\"content1\",\"content2\",\"content3\"); <br />Functions <b>setEventContent(2007,1,11,\$eventContent) + showMonth(2007,1)</b> generate the following calendar:";
$out.="</td></tr></table><br />";
$cal=new activeCalendar(2007,1);
$eventContent=array("content1","content2","content3");
$cal->setEventContent(2007,1,11,$eventContent);
$out.=$cal->showMonth();
$out.="<br /><a href=\"http://validator.w3.org/check?uri=referer\" target=\"_blank\">Validate this XHTML <span class=\"small\">(results in a new window)</span></a>";
echo $out;
}
?>
<hr></hr>
<table width="100%" border="0" bgcolor="#ffffff">
<tr>
<td class="small" align="center">
<a href="http://freshmeat.net/redir/activecalendar/53267/url_demo/index.html" class="small">Active Calendar Class Online (documentation, demo, contact, downloads)</a>
</td>
</tr>
<tr>
<td class="small" align="center">
&copy; Giorgos Tsiledakis, Greece Crete
</td>
</tr>
</table>
<hr></hr>
</center>
</body>
</html>
