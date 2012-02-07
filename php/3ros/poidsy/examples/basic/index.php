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

 // Poidsy returns its results in a session variable, so we need to start the
 // session here in order to get access to the results
 session_start();

 // OPENID_SREG_OPTIONAL Request some information from the identity provider. Note that providers 
 // don't have to implement the extension that provides this, so you can't
 // rely on getting results back. You can use OPENID_SREG_REQUEST to more
 // strongly request the information (it implies that the user will have to
 // manually enter the data if the provider doesn't supply it).
 //
 // The fields listed here are all the valid SREG fields. Anything else
 // almost certainly won't work (but you can of course omit ones you don't
 // need).
 define('OPENID_SREG_OPTIONAL','email,cn,uid,sn,givenName'); 
 //define('OPENID_SREG_OPTIONAL','email,cn,uid,sn,givenName'); 
 if (isset($_POST['openid_url']) || isset($_REQUEST['openid_mode'])) {
  // There are two cases when poidsy's processor needs to be invoked - firstly,
  // when the user has just submitted an OpenID identifier to be verified, in
  // which case $_POST['openid_url'] will be present (poidsy has special
  // handling for inputs named openid_url. If you want to use a URL from
  // another source, you can define the OPENID_URL constant instead.).
  // Secondly, if the user is being redirected back from their provider, the
  // openid.mode parameter will be present (which PHP translates to openid_mode)

  if (isset($_POST['openid_type']) && $_POST['openid_type'] != 'openid_url') {
   // This allows users to select one of the pre-defined identity providers
   // using the provided radio buttons. The values of the radio buttons specify
   // an URL on which we can perform Yadis discovery to find the OpenID
   // endpoint.
   define('OPENID_URL', $_POST['openid_type']);
  }

  // Include the simple registration extension
  require('../../sreg.ext.php');

  // Include and configure the attribute exchange extension
  require('../../ax.ext.php');
  AttributeExchange::addRequiredType('email', AttributeExchange::EMAIL);
  AttributeExchange::addRequiredType('cn', AttributeExchange::FULLNAME);
  AttributeExchange::addRequiredType('uid', AttributeExchange::USERNAME);
  AttributeExchange::addOptionalType('sn', AttributeExchange::LASTNAME);
  AttributeExchange::addOptionalType('givenName', AttributeExchange::FIRSTNAME);
  AttributeExchange::addOptionalType('cuil', AttributeExchange::NAMESUFFIX);
  AttributeExchange::addOptionalType('legajo', AttributeExchange::NAMEPREFIX);
  
  require('../../processor.php');

 } else {
  // If we don't have any processing to be doing, show them the form and
  // results.

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
                      "http://www.w3.org/TR/html4/strict.dtd">
<html>
 <head>
  <title>OpenID consumer demonstration</title>
  <style type="text/css">
   input#openid_url {
    background: url('../../openid.gif') no-repeat; padding-left: 20px;
    margin: 5px 0px 0px 40px;
   }

   p { padding-left: 10px; }
   p.error { border-left: 10px solid #f00; }
   p.succ { border-left: 10px solid #0f0; }
   caption { text-align: left; } 
   table { margin: 10px; }

   ul { list-style-type: none; }
   input[type="radio"] { width: 20px; }
  </style>
 </head>
 <body>
  <h1>OpenID consumer demo</h1>
<?PHP
print_r($_SESSION);
 if (isset($_SESSION['openid']['error'])) {

  // If the error variable is set, it means that poidsy has encountered an
  // error while trying to validate the identifier. We just tell the user
  // what went wrong, and unset the session vars so the messages don't persist

  echo '<p class="error">An error occured: ', htmlentities($_SESSION['openid']['error']), '</p>';
  unset($_SESSION['openid']['error']);

 } else if (isset($_SESSION['openid']['validated']) && $_SESSION['openid']['validated']) {

  // Upon a successful validation, the validated field will have been set to
  // true. It's important to check the validated field, as the identity
  // will be specified in the array throughout the process, so it would be
  // possible for the user to request the page with an identity specified
  // but before Poidsy had validated it. As above, we unset the session
  // vars so that the details don't persist.

  echo '<p class="succ">Success: your OpenID identifier is <em>', htmlentities($_SESSION['openid']['identity']), '</em></p>';
  unset($_SESSION['openid']['validated']);

  // Show the SREG data returned, if any. SREG data is only present if you
  // defined one of the OPENID_SREG constants before the request was sent,
  // if the user's identity provider supports SREG, and if (depending on the
  // provider) the user gives permission for you to have the data.
  if (isset($_SESSION['openid']['sreg'])) {
   echo '<table>';
   echo '<caption>Simple Registration Extension data</caption>';

   foreach ($_SESSION['openid']['sreg'] as $type => $data) {
    echo '<tr><th>', htmlentities($type), '</th>';
    echo '<td>', htmlentities($data), '</td></tr>';
   }

   echo '</table>';

   unset($_SESSION['openid']['sreg']);
  }

  // Show the attribute exchange data returned, if any.
  if (isset($_SESSION['openid']['ax'])) {
   echo '<table>';
   echo '<caption>Attribute Exchange Extension data</caption>';

   foreach ($_SESSION['openid']['ax']['types'] as $type => $uri) {
    echo '<tr><th>', htmlentities($type), '</th>';
    echo '<td>', htmlentities($uri), '</td>';
    echo '<td>', $count = $_SESSION['openid']['ax']['counts'][$type], '</td>';
    echo '<td>';
    if ($count == 1) {
     echo htmlentities($_SESSION['openid']['ax']['data'][$type]);
    } else if ($count > 1) {
     echo '<ol>';
     foreach ($_SESSION['openid']['ax']['data'][$type] as $value) {
      echo '<li>', htmlentities($value), '</li>';
     }
     echo '</ol>';
    }
    echo '</td>';
    echo '</tr>';
   }

   echo '</table>';

   unset($_SESSION['openid']['sreg']);
  }

 }
?>
  <form action="<?PHP echo htmlentities($_SERVER['REQUEST_URI']); ?>"
	method="post">
   <ul>
    <li><label><input type="radio" name="openid_type" value="https://www.google.com/accounts/o8/id"> <img src="google.png" alt="Google"> Login with my Google account</label></li>
    <li><label><input type="radio" name="openid_type" checked="checked" value="http://openid-dev.unc.edu.ar:8080/openaselect/profiles/openid/"> <img src="unc3_a.jpg" alt="UNC" HEIGHT="15%" WIDTH="15%"> Usuario de Comdoc</label></li>
    <li><label><input type="radio" name="openid_type" value="openid_url" > Login with another OpenID identity:</label> <br>
        <input type="text" name="openid_url" id="openid_url"></li>
   </ul>
   <input type="submit" value="Login">
   <hr/>
   <h6><a href="http://openid-dev.unc.edu.ar:8080/openaselect/sso/user/logout">Cierre de sesion</a></h6>
  </form>
 </body>
</html>
<?PHP
 }
?>
