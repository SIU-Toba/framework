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

 require_once(dirname(__FILE__) . '/keymanager.inc.php');

 class URLBuilder {

  const MIN_VERSION_FOR_NS = 2;

  private static $namespace = array(
   1 => 'http://openid.net/signon/1.1',
   2 => 'http://specs.openid.net/auth/2.0'
  );

  public static function addArguments($base, $arguments) {
   $first = true;
   $res = $base === false ? '' : $base;

   if ($base !== false && strrpos($base, '?', -2) === false) {
    if ($base[strlen($base) - 1] != '?') {
     $res .= '?';
    }
   } else if ($base !== false) {
    $res .= '&';
   }

   foreach ($arguments as $key => $value) {
    if ($first) {
     $first = false;
    } else {
     $res .= '&';
    }

    $res .= urlencode($key) . '=' . urlencode($value);
   }

   return $res;
  }

  public static function buildRequest($type, $base, $delegate, $identity, $returnURL, $handle, $version = 1) {
   $args = array(
    'openid.mode' => 'checkid_' . $type,
    'openid.identity' => $version == 1 ? $delegate : $identity,
    'openid.claimed_id' => $delegate,
    ($version == 1 ? 'openid.trust_root' : 'openid.realm') => self::getTrustRoot($returnURL),
    'openid.return_to' => self::addArguments($returnURL,
		array('openid.nonce' => $_SESSION['openid']['nonce']))
   );

   if ($version >= self::MIN_VERSION_FOR_NS) {
    $args['openid.ns'] = self::$namespace[$version];
   }

   if ($handle !== null) {
    $args['openid.assoc_handle'] = $handle;
   }

   self::decorateArgs($args);

   return self::addArguments($base, $args);
  }

  public static function getTrustRoot($base = null, $curl = null) {
   $curr = $curl == null ? self::getCurrentURL() : $curl;

   if (defined('OPENID_TRUSTROOT')) {
    $root = OPENID_TRUSTROOT; 
   } else {
    $root = $base == null ? $curr : $base;
   }

   // Note that this may end up going back to 'http:/' if
   // the domains don't match.
   while (substr($curr, 0, strlen($root)) != $root) {
    $root = dirname($root) . '/';
   }

   return $root; 
  }

  private static function decorateArgs(&$args) {
   global $_POIDSY;

   if (empty($_POIDSY['decorators'])) { return; }

   $id = 1;

   foreach ($_POIDSY['decorators'] as $decorator) {
    $decorator->decorate($args, 'ext' . ($id++));
   }
  }

  public static function buildAssociate($server, $version = 1, $assocType = null, $sessionType = null) {
   if ($assocType == null) { $assocType = 'HMAC-SHA1'; }
   if ($sessionType == null) { $sessionType = 'DH-SHA1'; }

   $args = array(
	'openid.mode' => 'associate',
	'openid.assoc_type' => $assocType,
   );

   if ($version >= self::MIN_VERSION_FOR_NS) {
    $args['openid.ns'] = self::$namespace[$version];
   }

   if (KeyManager::supportsDH()) {
    $args['openid.session_type'] = $sessionType;
    $args['openid.dh_modulus'] = KeyManager::getDhModulus();
    $args['openid.dh_gen'] = KeyManager::getDhGen();
    $args['openid.dh_consumer_public'] = KeyManager::getDhPublicKey($server);
   } else {
    $args['openid.session_type'] = '';
   }

   return self::addArguments(false, $args);
  }

  public static function buildAuth($params, $version = 1) {
   $args = array(
	'openid.mode' => 'check_authentication'
   );

   if ($version >= self::MIN_VERSION_FOR_NS) {
    $args['openid.ns'] = self::$namespace[$version];
   }

   $toadd = array('assoc_handle', 'sig', 'signed');
   $toadd = array_merge($toadd, explode(',', $params['openid_signed']));

   foreach ($toadd as $arg) {
    if (!isset($args['openid.' . $arg])) {
     $args['openid.' . $arg] = $params['openid_' . str_replace('.', '_', $arg)];
    }
   }

   return self::addArguments(false, $args);
  }

  public static function isValidReturnToURL($url) {
   // 11.1: The URL scheme, authority, and path MUST be the same between the two URLs.
   //       Any query parameters that are present in the "openid.return_to" URL MUST
   //       also be present with the same values in the URL of the HTTP request the
   //       RP received.

   return self::isSameURL(self::getCurrentURL(true), $url);
  }

  public static function isSameURL($url1, $url2) {
   $actual = parse_url($url1);
   $return = parse_url($url2);

   foreach (array('scheme', 'host', 'port', 'user', 'pass', 'path') as $part) {
    if ($part == 'port') {
     if (!isset($actual['port'])) { $actual['port'] = $actual['scheme'] == 'https' ? 443 : 80; }
     if (!isset($return['port'])) { $return['port'] = $return['scheme'] == 'https' ? 443 : 80; }
    }

    if (isset($actual[$part]) ^ isset($return[$part])) {
     // Present in one but not the other
     return false;
    } else if (isset($actual[$part]) && $actual[$part] != $return[$part]) {
     // Present in both but different
     return false;
    }
   }

   parse_str($actual['query'], $actualVars);
   parse_str($return['query'], $returnVars);

   foreach ($returnVars as $key => $value) {
    if (!isset($actualVars[$key]) || $actualVars[$key] != $value) {
     return false;
    }
   }
 
   return true;
  }

  public static function getCurrentURL($raw = false) {
   $res = 'http';

   if (isset($_SERVER['HTTPS'])) {
    $res = 'https';
   }

   $res .= '://' . $_SERVER['SERVER_NAME'];

   if ($_SERVER['SERVER_PORT'] != (isset($_SERVER['HTTPS']) ? 443 : 80)) {
    $res .= ':' . $_SERVER['SERVER_PORT'];
   }

   $url = $_SERVER['REQUEST_URI'];

   if (!$raw) {
    while (preg_match('/([\?&])openid[\._](.*?)=(.*?)(&|$)/', $url, $m)) {
     $url = str_replace($m[0], $m[1], $url);
    }
   }

   $url = preg_replace('/\??&*$/', '', $url);

   return $res . $url;
  }

  /**
   * Redirects the user back to their original page.
   */
  public static function redirect() {
   if (defined('OPENID_REDIRECTURL')) {
    $url = OPENID_REDIRECTURL;
   } else if (isset($_SESSION['openid']['redirect'])) {
    $url = $_SESSION['openid']['redirect'];
   } else {
    $url = self::getCurrentURL();
   }

   self::doRedirect($url);
  }

  /**
   * Redirects the user to the specified URL.
   *
   * @param $url The URL to redirect the user to
   */
  public static function doRedirect($url) {
   header('Location: ' . $url);
   echo '<html><head><title>Redirecting</title></head><body>';
   echo '<p>Redirecting to <a href="', htmlentities($url), '">';
   echo htmlentities($url), '</a></p></body></html>';
   exit();
  }

 }

?>
