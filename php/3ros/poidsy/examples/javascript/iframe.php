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

 define('OPENID_TRUSTROOT', $_SESSION['trustroot']);
 define('OPENID_IMMEDIATE', true);

 if (isset($_GET['openid_id'])) {
  define('OPENID_URL', $_GET['openid_id']);
 }

 if (defined('OPENID_URL') || isset($_REQUEST['openid_mode'])) {

  require('../../processor.php');

 } else if (isset($_SESSION['openid']['error'])) {
  if ($_SESSION['openid']['errorcode'] == 'noimmediate') {
   echo '<script type="text/javascript">parent.doSubmit();</script>';
  } else {
   echo '<script type="text/javascript">parent.doError("Error: ' . $_SESSION['openid']['error'] . '");</script>';
  }
  unset($_SESSION['openid']['error']);
 } else if (isset($_SESSION['openid']['validated']) && $_SESSION['openid']['validated']) {
  echo '<script type="text/javascript">parent.doSuccess("Logged in as ' . $_SESSION['openid']['identity'] . '");</script>';
 }
?>
