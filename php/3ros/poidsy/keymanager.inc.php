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

 require_once(dirname(__FILE__) . '/bigmath.inc.php');
 require_once(dirname(__FILE__) . '/logging.inc.php');
 require_once(dirname(__FILE__) . '/poster.inc.php');
 require_once(dirname(__FILE__) . '/urlbuilder.inc.php');

 class KeyManager {

  /** Diffie-Hellman P value, defined by OpenID specification. */
  const DH_P = '155172898181473697471232257763715539915724801966915404479707795314057629378541917580651227423698188993727816152646631438561595825688188889951272158842675419950341258706556549803580104870537681476726513255747040765857479291291572334510643245094715007229621094194349783925984760375594985848253359305585439638443';
  /** Diffie-Hellman G value. */
  const DH_G = '2';

  private static $header = null;
  private static $data = null;
  private static $bigmath = null;

  /**
   * Loads the KeyManager's data array from disk.
   */
  private static function loadData() {
   if (self::$data == null) {
    $data = file(dirname(__FILE__) . '/keycache.php');
    self::$header = array_shift($data);
    self::$data = unserialize(implode("\n", $data));
   }
  }

  /**
   * Saves the KeyManager's data array to disk.
   */
  private static function saveData() {
   file_put_contents(dirname(__FILE__) . '/keycache.php', self::$header . serialize(self::$data));
  }

  /**
   * Attempts to associate with the specified server.
   *
   * @param String $server The server to associate with
   */
  public static function associate($server, $assocType = null, $sessionType = null) {
   Logger::log('Attempting to associate with %s, assocType: %s, sessionType: %s', $server, $assocType, $sessionType);
   $data = URLBuilder::buildAssociate($server, $_SESSION['openid']['version'], $assocType, $sessionType);

   try {
    $res = Poster::post($server, $data);
   } catch (Exception $ex) {
    Logger::log('Exception while posting: %s', $ex->getMessage());
    return;
   }

   $data = array();

   foreach (explode("\n", $res) as $line) {
    if (preg_match('/^(.*?):(.*)$/', $line, $m)) {
     $data[$m[1]] = $m[2];
    }
   }

   if (isset($data['error_code']) && $data['error_code'] == 'unsupported-type') {
    $cont = false;

    if (isset($data['session_type']) && $data['session_type'] != $sessionType) {
     // TODO: Check we support it before actually trying
     $sessionType = $data['session_type'];
     $cont = true;
    }

    if (isset($data['assoc_type']) && $data['assoc_type'] != $assocType) {
     $assocType = $data['assoc_type'];
     $cont = true;
    }

    if ($cont) {
     self::associate($server, $assocType, $sessionType);
    }

    return;
   }

   try {
    $data = self::decodeKey($server, $data);
   } catch (Exception $ex) {
    return;
   }

   $data['expires_at'] = time() + $data['expires_in'];

   self::$data[$server][$data['assoc_handle']] = $data;
   self::saveData();
  }

  /**
   * Decodes the MAC key specified in the $data array.
   *
   * @param String $server The server which sent the data
   * @param Array $data Array of association data from the server
   * @return A copy of the $data array with the mac_key present
   */
  private static function decodeKey($server, $data) {
   switch (strtolower($data['session_type'])) {
    case 'dh-sha1':
     $algo = 'sha1';
     break;
    case 'dh-sha256':
     $algo = 'sha256';
     break;
    case 'no-encryption':
    case 'blank':
    case '':
     $algo = false;
     break;
    default:
     throw new Exception('Unable to handle session type ' . $data['session_type']);
   }

   if ($algo !== false) {
    // The key is DH'd
    $mac = base64_decode($data['enc_mac_key']);
    $x = self::getDhPrivateKey($server);
    $temp = self::$bigmath->btwoc_undo(base64_decode($data['dh_server_public']));
    $temp = self::$bigmath->powmod($temp, $x, self::DH_P);
    $temp = self::$bigmath->btwoc($temp);
    $temp = hash($algo, $temp, true);
    $mac = $mac ^ $temp;
    $data['mac_key'] = base64_encode($mac);

    unset($data['enc_mac_key'], $data['dh_server_public']);
   }

   return $data;
  }

  /**
   * Retrieves an active assoc_handle for the specified server.
   *
   * @param String $server The server whose handle we're looking for
   * @return An association handle for the server or null on failure
   */
  public static function getHandle($server) {
   self::loadData();

   if (!isset(self::$data[$server])) {
    return null;
   }

   foreach (self::$data[$server] as $handle => $data) {
    if ($handle == '__private') { continue; }

    if ($data['expires_at'] < time()) {
     unset(self::$data[$server][$handle]);
    } else {
     return $handle;
    }
   }

   return null;
  }

  /**
   * Determines if the KeyManager has at least one assoc_handle for the
   * specified server.
   *
   * @param String $server The server to check for
   * @return True if the KeyManager has a handle, false otherwise
   */
  public static function hasHandle($server) {
   return self::getHandle($server) !== null;
  }

  /**
   * Retrieves the association data array for the specified server and assoc
   * handle.
   *
   * @param String $server The server whose data is being requested
   * @param String $handle The current association handle for the server
   * @return Array of association data or null if none was found
   */
  public static function getData($server, $handle) {
   self::loadData();

   if (isset(self::$data[$server][$handle])) {
    if (self::$data[$server][$handle]['expires_at'] < time()) {
     self::revokeHandle($server, $handle);
     return null;
    } else {
     return self::$data[$server][$handle];
    }
   } else {
    return null;
   }
  }

  /**
   * Attempts to authenticate that the specified arguments are a valid query
   * from the specified server. If smart authentication is not available
   * or an error is encountered, throws an exception.
   *
   * @param String $server The server that supposedly sent the request
   * @param Array $args The arguments included in the request
   * @return True if the message was authenticated, false if it's a fake
   */
  public static function authenticate($server, $args) {
   $data = self::getData($server, $args['openid_assoc_handle']);

   if ($data === null) {
    throw new Exception('No key available for that server/handle');
   }

   $contents = '';
   foreach (explode(',', $args['openid_signed']) as $arg) {
    $argn = str_replace('.', '_', $arg);
    $contents .= $arg . ':' . $args['openid_' . $argn] . "\n";
   }

   switch (strtolower($data['assoc_type'])) {
    case 'hmac-sha1':
     $algo = 'sha1';
     break;
    case 'hmac-sha256':
     $algo = 'sha256';
     break;
    default:
     throw new Exception('Unable to handle association type ' . $data['assoc_type']);
   }

   $sig = base64_encode(hash_hmac($algo, $contents, base64_decode($data['mac_key']), true));

   if ($sig == $args['openid_sig']) {
    return true;
   } else {
    return false;
   }
  }

  /**
   * Validates the current request using dumb authentication (a POST to the
   * provider).
   *
   * @return True if the request has been authenticated, false otherwise.
   */
  public static function dumbAuthenticate() {
   $url = URLBuilder::buildAuth($_REQUEST, $_SESSION['openid']['version']);

   try {
    $data = Poster::post($_SESSION['openid']['endpointUrl'], $url);
   } catch (Exception $ex) {
    return false;
   }

   $valid = false;
   foreach (explode("\n", $data) as $line) {
    if (substr($line, 0, 9) == 'is_valid:') {
     $valid = (boolean) substr($line, 9);
    }
   }

   return $valid;
  }

  /**
   * Removes the specified association handle from the specified server's
   * records.
   *
   * @param String $server The server which is revoking the handle
   * @param String $handle The handle which is being revoked
   */
  public static function revokeHandle($server, $handle) {
   self::loadData();
   unset(self::$data[$server][$handle]);
   self::saveData();
  }

  /**
   * Determines if the keymanager is supported by the local environment or not.
   *
   * @return True if the keymanager can be used, false otherwise
   */
  public static function isSupported() {
   return @is_writable(dirname(__FILE__) . '/keycache.php')
	&& function_exists('hash_hmac');
  }

  /**
   * Returns the base64-encoded representation of the dh_modulus parameter.
   *
   * @return Base64-encoded representation of dh_modulus
   */
  public static function getDhModulus() {
   return base64_encode(self::$bigmath->btwoc(self::DH_P));
  }

  /**
   * Returns the base64-encoded representation of the dh_gen parameter.
   *
   * @return Base64-encoded representation of dh_gen
   */
  public static function getDhGen() {
   return base64_encode(self::$bigmath->btwoc(self::DH_G));
  }

  /**
   * Retrieves our private key for the specified server.
   *
   * @param String $server The server which we're communicating with
   * @return Our private key for the specified server, or null if we don't have
   * one
   */
  public static function getDhPrivateKey($server) {
   self::loadData();
   if (isset(self::$data[$server])) {
    return self::$data[$server]['__private'];
   } else {
   	return null;
   }
  }

  /**
   * Retrieves our public key for use with the specified server.
   *
   * @param String $server The server we wish to send the public key to
   * @return Base64-encoded public key for the specified server
   */
  public static function getDhPublicKey($server) {
   self::loadData();
   $key = self::createDhKey($server);
   self::saveData();

   return base64_encode(self::$bigmath->btwoc(self::$bigmath->powmod(self::DH_G, $key, self::DH_P)));
  }

  /**
   * Creates a private key for use when exchanging keys with the specified
   * server.
   *
   * @param String $server The name of the server we're dealing with
   * @return The server's new private key
   */
  private static function createDhKey($server) {
   return self::$data[$server]['__private'] = self::$bigmath->rand(self::DH_P);
  }

  /**
   * Determines whether the keymanager can support Diffie-Hellman key exchange.
   *
   * @return True if D-H exchange is supported, false otherwise.
   */
  public static function supportsDH() {
   return self::$bigmath != null;
  }

  /**
   * Initialises the key manager.
   */
  public static function init() {
   self::$bigmath = BigMath::getBigMath();
  }

 }

 KeyManager::init();

?>
