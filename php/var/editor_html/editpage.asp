<head>
<script language="Javascript1.2"><!-- // load htmlarea
_editor_url = "";                     // URL to htmlarea files
var win_ie_ver = parseFloat(navigator.appVersion.split("MSIE")[1]);
if (navigator.userAgent.indexOf('Mac')        >= 0) { win_ie_ver = 0; }
if (navigator.userAgent.indexOf('Windows CE') >= 0) { win_ie_ver = 0; }
if (navigator.userAgent.indexOf('Opera')      >= 0) { win_ie_ver = 0; }
if (win_ie_ver >= 5.5) {
 document.write('<scr' + 'ipt src="' +_editor_url+ 'editor.js"');
 document.write(' language="Javascript1.2"></scr' + 'ipt>');  
} else { document.write('<scr'+'ipt>function editor_generate() { return false; }</scr'+'ipt>'); }
// --></script>

<!-- #INCLUDE VIRTUAL="/common/checksession.asp" -->
<%
	ID = request("ID")
	if not isNumeric(id) or id = "" then
		response.redirect("default.asp?not_number")
	end if
%>
<!-- #INCLUDE VIRTUAL="/common/dbconn1.asp" -->
<!-- #INCLUDE VIRTUAL="/common/header.asp" -->
<!--- Type Content Below --->
<%
'*****IF POSTBACK THEN UPDATE********
if request.form("postback") = 1 then

	set updateRS = Server.CreateObject("ADODB.RecordSet")
	updateSQL = "SELECT pagename, pagedata, active, directlink from tblPages where ID = " & ID & ";"
	updateRS.Open updateSQL, whtConn, 1, 3
	'response.write "updatesql= " & updateSQL
		'recordcount = updateRS.recordcount
		'recordcount = mysqlrecordcount2
		'if recordcount <> 1 then
		'	updateRS.Close
		'	Set updateRS = Nothing
		'	whtConn.Close
		'	Set whtConn = Nothing
		'	response.redirect("default.asp?norecord")
		'end if
	updateRS("pagename") = Presubmit2(request.form("pagename"))
	updateRS("pagedata") = Presubmit(request.form("pagedata"))
	updateRS("directlink") = Presubmit2(request.form("directlink"))
	updateRS("active") = request.form("active")
	updateRS.update
	updateRS.Close
	set updateRS = Nothing
	message = "<p><font color='#FF0000'>Update Successful</font></p>"
end if

Set dataRS = Server.CreateObject("ADODB.RecordSet")
ID = request("ID")
whtSQL = "Select ID, pagedata, pagename, active, directlink FROM tblPages where ID = " & ID & ";"
dataRS.Open whtSQL, whtConn, 1
		'recordcount = dataRS.recordcount
		recordcount = mysqlrecordcount
		if recordcount <> 1 then
			dataRS.Close
			Set dataRS = Nothing
			whtConn.Close
			Set whtConn = Nothing
			response.redirect("default.asp?norecord")
		end if
pagename = dataRS("pagename")
ID = dataRS("ID")
pagedata = dataRS("pagedata")
directlink = dataRS("directlink")

active = dataRS("active")
	if active = 1 then
		activeform = "<input type='checkbox' name='active' value='1' checked>"
	else
		activeform =  "<input type='checkbox' name='active' value='1'>"
	end if

'close connections,etc..
dataRS.Close
Set dataRS = Nothing
whtConn.Close
Set whtConn = Nothing
%>
<%=message%>

<form action="editpage.asp" method="post" name="frm" language="javascript">
<table>
	<tr>
		<td>Page Title </td>
		<td> <input type="text" name="pagename" value="<%=pagename%>"></td>
	</tr>
	<tr>
		<td nowrap>Page Direct Link </td>
		<td><input type="text" name="directlink" value="<%=directlink%>" size="55"></td>
	</tr>
	<tr>
		<td>Active </td>
		<td><%=activeform%></td>
	</tr>
</table>
<textarea cols="75" rows="10" name="pagedata"><%=pagedata%></textarea>
<script language="JavaScript1.2" defer>
editor_generate('pagedata');
</script>

<input type="hidden" name="postback" value="1">
<input type="hidden" name="id" value="<%=id%>">
<br>
<input type="submit" name="Update" value="Update" class="cssBorder">
</form>

<p><a href="default.asp">Back to Admin</a></p>
<!-- #INCLUDE VIRTUAL="/common/footer.asp" -->
