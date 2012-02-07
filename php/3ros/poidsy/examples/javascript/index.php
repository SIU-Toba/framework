<?PHP

/* Poidsy 0.6 - http://chris.smith.name/projects/poidsy
 * Copyright (c) 2008-2010 Chris Smith
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

 session_start();

 require('../../urlbuilder.inc.php');

 if (isset($_GET['cs'])) {
  unset($_SESSION['openid']);
  header('Location: ' . $_SERVER['SCRIPT_NAME']);
  exit;
 }

 $_SESSION['trustroot'] = URLBuilder::getCurrentURL();

 if (isset($_POST['openid_url']) || isset($_REQUEST['openid_mode'])) {
  // Proxy for non-JS users

  require('../../processor.php');

 } else {

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
                      "http://www.w3.org/TR/html4/strict.dtd">
<html>
 <head>
  <title>OpenID consumer demonstration</title>
  <style type="text/css">
   input#openid_url {
    background: url('../../openid.gif') no-repeat; padding-left: 20px;
   }
   div { margin: 20px; padding: 5px; }
  </style>
  <script type="text/javascript">
   function tryJsLogin() {
    document.getElementById('target').src = 'iframe.php?openid.id=' + document.getElementById('openid_url').value;
   }
   function doSubmit() {
    //alert('Provider is requesting your interaction. Sending you away.');
    document.getElementById('form').submit();
   }
   function doError(msg) {
    document.getElementById('status').innerHTML = msg;
    document.getElementById('status').style.backgroundColor = "#a00";
   }
   function doSuccess(msg) {
    document.getElementById('status').innerHTML = msg;
    document.getElementById('status').style.backgroundColor = "#0a0";
   }
  </script>
 </head>
 <body>
  <h1>OpenID consumer demo</h1>
  <p>
   The login form below uses a hidden iframe to process the form
   (assuming the user has javascript enabled; if they don't, it falls back
   gracefully). If your identity provider implements checkid_immediate
   properly (which several don't appear to), and has enough information to
   authorise you without requiring your input, the entire login process
   should happen without any noticable change except for the status message.
  </p><p>
   If your identity provider requires interaction with you, the form
   will be submitted as usual and you'll leave this page (but, as usual, will
   return when your IdP is done with you). If your identity provider is
   <em>broken</em>, you won't see anything happening after the initial page
   load and redirect. This is either because the identity provider is trying to
   interact with you (via a hidden iframe) when it has been explicitly told
   not to, or because it is sending some kind of non-openID error response,
   such as a HTTP 500 error. This is the identity provider's fault (it's
   violating the OpenID specifications), not Poidsy's. If you were implementing
   this on a live site, you'd probably want to either add a timeout or monitor
   the iframe status to detect if it wasn't working and do a normal login.
  </p>
  <p>
   Note: if you are using Firefox and have the 'Disallow third party cookies'
   preference enabled, Firefox won't send cookies to your provider when it's
   loaded in the iframe. This almost certainly will mean that your provider
   can't validate your identity immediately, and thus you'll be redirected.
   Other browsers (such as IE and Safari) allow these cookies to be sent even
   if they disallow setting of third-party cookies.
  </p>
<?PHP

 echo '<p>Time: ', date('r'), '. <a href="?cs">Clear session info</a></p>';

 if (isset($_SESSION['openid']['error'])) {

  echo '<div id="status" style="background-color: #a00;">An error occured: ', htmlentities($_SESSION['openid']['error']), '</div>';
  unset($_SESSION['openid']['error']);

 } else if (isset($_SESSION['openid']['validated']) && $_SESSION['openid']['validated']) {

  echo '<div id="status" style="background-color: #0a0;">Logged in as ', htmlentities($_SESSION['openid']['identity']), '</div>';

 } else {

  echo '<div id="status">Not logged in</div>';

 }
?>
  <form action="<?PHP echo htmlentities($_SERVER['REQUEST_URI']); ?>"
	method="post" onSubmit="tryJsLogin(); return false;" id="form">
   <input type="text" name="openid_url" id="openid_url">
   <input type="submit" value="Login">
   <iframe id="target" style="display: none;"></iframe>
  </form>
 </body>
</html>
<?PHP
 }
?>
