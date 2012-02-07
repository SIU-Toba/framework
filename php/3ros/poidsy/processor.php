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

 require_once(dirname(__FILE__) . '/logging.inc.php');
 require_once(dirname(__FILE__) . '/discoverer.inc.php');
 require_once(dirname(__FILE__) . '/poster.inc.php');
 require_once(dirname(__FILE__) . '/urlbuilder.inc.php');
 require_once(dirname(__FILE__) . '/keymanager.inc.php');

 if (session_id() == '') {
  // No session - testing maybe?
  session_start();
 }

 // Process any openid_url form fields (compatability with 0.1)
 if (!defined('OPENID_URL') && isset($_POST['openid_url'])) {
  define('OPENID_URL', $_POST['openid_url']);
 } else if (!defined('OPENID_URL') && isset($_POST['openid_identifier'])) {
  define('OPENID_URL', $_POST['openid_identifier']);
 }

 // Maximum number of requests to allow without a OPENID_THROTTLE_GAP second
 // gap between two of them
 if (!defined('OPENID_THROTTLE_NUM')) {
  define('OPENID_THROTTLE_NUM', 3);
 }

 // Time to require between requests before the request counter is reset
 if (!defined('OPENID_THROTTLE_GAP')) {
  define('OPENID_THROTTLE_GAP', 10);
 }

 // Whether or not to use the key manager
 define('KEYMANAGER', !defined('OPENID_NOKEYMANAGER') && KeyManager::isSupported());

 /**
  * Processes the current request.
  */
 function process() {
  if (defined('OPENID_URL')) {
   // Initial authentication attempt (they just entered their identifier)
   Logger::log('Processing authentication attempt for %s', OPENID_URL);

   $reqs = checkRequests();
   $disc = tryDiscovery(OPENID_URL);

   $_SESSION['openid'] = array(
	'identity' => $disc->getClaimedId(),
	'claimedId' => $disc->getClaimedId(),
	'endpointUrl' => $disc->getEndpointUrl(),
	'opLocalId' => $disc->getOpLocalId(),
	'userSuppliedId' => $disc->getUserSuppliedId(),
	'version' => $disc->getVersion(),
	'validated' => false,
	'nonce' => uniqid(microtime(true), true),
	'requests' => $reqs,
   );

   $handle = getHandle($disc->getEndpointUrl());

   if(!defined('OPENID_RETURN_URL')) define('OPENID_RETURN_URL', URLBuilder::getCurrentURL());
   
   $url = URLBuilder::buildRequest(defined('OPENID_IMMEDIATE') ? 'immediate' : 'setup',
              $disc->getEndpointUrl(), $disc->getOpLocalId(),
              $disc->getClaimedId(), OPENID_RETURN_URL, $handle, $disc->getVersion());
   URLBuilder::doRedirect($url);
  } else if (isset($_REQUEST['openid_mode'])) {
   checkNonce();

   $func = 'process' . str_replace(' ', '', ucwords(str_replace('_', ' ',
			strtolower($_REQUEST['openid_mode']))));
   if (function_exists($func)) {
    call_user_func($func, checkHandleRevocation());
   }
  }
 }

 /**
  * Retrieves or creates the 'requests' session array, which tracks the number
  * of authentication attempts the user has made recently.
  *
  * @return An array (by reference) containing details about recent requests
  */
 function &getRequests() {
  if (!isset($_SESSION['openid']['requests'])) {
   $_SESSION['openid']['requests'] = array('lasttime' => 0, 'count' => 0);
  }

  return $_SESSION['openid']['requests'];
 }

 /**
  * Checks that the user isn't making requests too frequently, and redirects
  * them with an appropriate error if they are.
  *
  * @return An array containing details about the requests that have been made
  */
 function &checkRequests() {
  $requests = getRequests();

  if ($requests['lasttime'] < time() - OPENID_THROTTLE_GAP) {

   // Last request was a while ago, reset the timer
   resetRequests(); 

  } else if ($requests['count'] > OPENID_THROTTLE_NUM) {

   Logger::log('Client throttled: %s requests made', $requests['count']);

   // More than the legal number of requests
   error('throttled', 'You are trying to authenticate too often');

  }

  $requests['count']++;
  $requests['lasttime'] = time();

  return $requests;
 }

 /**
  * Resets the recent requests counter (for example, after the required time
  * has ellapsed, or after the user has successfully logged in).
  *
  * @param $decrement If true, the count will be decremented instead of cleared
  * @return A copy of the requests array
  */
 function &resetRequests($decrement = false) {
  $requests = getRequests();

  if ($decrement) {
   $requests['count'] = max($requests['count'] - 1, 0);
  } else {
   $requests['count'] = 0;
  }

  return $requests;
 }

 /**
  * Attempts to perform discovery on the specified URL, redirecting the user
  * with an appropriate error if discovery fails.
  *
  * @param String $url The URL to perform discovery on
  * @return An appropriate Discoverer object
  */
 function tryDiscovery($url) {
  try {
   $disc = new Discoverer($url);

   if ($disc->getEndpointUrl() == null) {
    Logger::log('Couldn\'t perform discovery on %s', $url);
    error('notvalid', 'Claimed identity is not a valid identifier');
   }

   return $disc;
  } catch (Exception $e) {
   Logger::log('Error during discovery on %s: %s', $url, $e->getMessage());
   error('discovery', $e->getMessage());
  }
  
  return null;
 }

 /**
  * Retrieves an association handle for the specified server. If we don't
  * currently have one, attempts to associate with the server.
  *
  * @param String $server The server whose handle we're retrieving
  * @return The association handle of the server or null on failure
  */
 function getHandle($server) {
  if (KEYMANAGER) {
   if (!KeyManager::hasHandle($server)) {
    KeyManager::associate($server);
   }

   return KeyManager::getHandle($server);
  } else {
   return null;
  }
 }

 /**
  * Checks that the nonce specified in the current request equals the one
  * stored in the user's session, and redirects them if it doesn't.
  */
 function checkNonce() {
  if ($_REQUEST['openid_nonce'] != $_SESSION['openid']['nonce']) {
   error('nonce', 'Nonce doesn\'t match - possible replay attack');
  } else {
   $_SESSION['openid']['nonce'] = uniqid(microtime(true), true);
  }
 }

 /**
  * Checks to see if the request contains an instruction to invalidate the
  * handle we used. If it does, the request is authenticated and the handle
  * removed (or the user is redirected with an error if the IdP doesn't
  * authenticate the message).
  *
  * @return True if the message has been authenticated, false otherwise
  */
 function checkHandleRevocation() {
  $valid = false;

  if (KEYMANAGER && isset($_REQUEST['openid_invalidate_handle'])) {
   Logger::log('Request to invalidate handle received');
   $valid = KeyManager::dumbAuth();

   if ($valid) {
    KeyManager::removeKey($_SESSION['openid']['endpointUrl'], $_REQUEST['openid_invalidate_handle']);
   } else {
    error('noauth', 'Provider didn\'t authenticate message');
   }
  }

  return $valid;
 }

 /**
  * Processes id_res requests.
  *
  * @param Boolean $valid True if the request has already been authenticated
  */
 function processIdRes($valid) {
  if (isset($_REQUEST['openid_identity'])) {
   processPositiveResponse($valid);
  } else if (isset($_REQUEST['openid_user_setup_url'])) {
   processSetupRequest();
  }
 }
 
 /**
  * Processes a response where the provider is requesting to interact with the
  * user in order to confirm their identity.
  */
 function processSetupRequest() {
  if (defined('OPENID_IMMEDIATE') && OPENID_IMMEDIATE) {
   error('noimmediate', 'Couldn\'t perform immediate auth');
  }

  $handle = getHandle($_SESSION['openid']['endpointUrl']);

  $url = URLBuilder::buildRequest('setup', $_REQUEST['openid_user_setup_url'],
                                $_SESSION['openid']['opLocalId'],
                                $_SESSION['openid']['claimedId'],
                                URLBuilder::getCurrentURL(), $handle);

  URLBuilder::doRedirect($url); 	
 }
 
 /**
  * Processes a positive authentication response.
  *
  * @param Boolean $valid True if the request has already been authenticated
  */
 function processPositiveResponse($valid) {
  Logger::log('Positive response: identity = %s, expected = %s', $_REQUEST['openid_identity'], $_SESSION['openid']['claimedId']);

  if (!URLBuilder::isValidReturnToURL($_REQUEST['openid_return_to'])) {
   Logger::log('Return_to check failed: %s, URL: %s', $_REQUEST['openid_return_to'], URLBuilder::getCurrentURL(true));
   error('diffreturnto', 'The identity provider stated return URL was '
                         . $_REQUEST['openid_return_to'] . ' but it actually seems to be '
                         . URLBuilder::getCurrentURL());
  }

  $id = $_REQUEST[isset($_REQUEST['openid_claimed_id']) ? 'openid_claimed_id' : 'openid_identity'];

  if (!URLBuilder::isSameURL($id, $_SESSION['openid']['claimedId']) && !URLBuilder::isSameURL($id, $_SESSION['openid']['opLocalId'])) {
   if ($_SESSION['openid']['claimedId'] == 'http://specs.openid.net/auth/2.0/identifier_select') {
    $disc = new Discoverer($_REQUEST['openid_claimed_id'], false);

    if ($disc->hasServer($_SESSION['openid']['endpointUrl'])) {
     $_SESSION['openid']['identity'] = $_REQUEST['openid_identity']; 
     $_SESSION['openid']['opLocalId'] = $_REQUEST['openid_claimed_id'];
    } else {
     error('diffid', 'The OP at ' . $_SESSION['openid']['endpointUrl'] . ' is attmpting to claim ' . $_REQUEST['openid_claimed_id'] . ' but ' . ($disc->getEndpointUrl() == null ? 'that isn\'t a valid identifier' : 'that identifier only authorises ' . $disc->getEndpointUrl()));
    }
   } else {
     error('diffid', 'Identity provider validated wrong identity. Expected it to '
	             . 'validate ' . $_SESSION['openid']['claimedId'] . ' but it '
  	             . 'validated ' . $id);
   }
  }

  resetRequests(true);

  if (!$valid) {
   $dumbauth = true;

   if (KEYMANAGER) {
    try {
     Logger::log('Attempting to authenticate using association...');
     $valid = KeyManager::authenticate($_SESSION['openid']['endpointUrl'], $_REQUEST);
     $dumbauth = false;
    } catch (Exception $ex) {
     // Ignore it - try dumb auth
    }
   }

   if ($dumbauth) {
    Logger::log('Attempting to authenticate using dumb auth...');
    $valid = KeyManager::dumbAuthenticate();
   }
  }

  $_SESSION['openid']['validated'] = $valid;

  if (!$valid) {
   Logger::log('Validation failed!');
   error('noauth', 'Provider didn\'t authenticate response');
  }

  Processor::callHandlers();
  URLBuilder::redirect(); 
 }

 /**
  * Processes cancel modes.
  *
  * @param Boolean $valid True if the request has already been authenticated
  */
 function processCancel($valid) {
  error('cancelled', 'Provider cancelled the authentication attempt');
 }

 /**
  * Processes error modes.
  *
  * @param Boolean $valid True if the request has already been authenticated
  */
 function processError($valid) {
  error('perror', 'Provider error: ' . $_REQUEST['openid_error']);
 }

 /**
  * Populates the session array with the details of the specified error and
  * redirects the user appropriately.
  *
  * @param String $code The error code that occured
  * @param String $message A description of the error
  */
 function error($code, $message) {
  $_SESSION['openid']['error'] = $message;
  $_SESSION['openid']['errorcode'] = $code;
  URLBuilder::redirect();
 }

 class Processor {

  public static function callHandlers() {
   global $_POIDSY;

   if (empty($_POIDSY['handlers'])) { return; }

   foreach ($_POIDSY['handlers'] as $handler) {
    $handler->parseResponse();
   }
  }

 }

 // Here we go!
 process();
?>
