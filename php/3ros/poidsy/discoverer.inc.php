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

class Server {

 private $url = null;
 private $version = 1;
 private $services = array();

 public function __construct($url, $version) {
  $this->url = $url;
  $this->version = $version;
 }

 public function getURL() {
  return $this->url;
 }

 public function getVersion() {
  return $this->version;
 }

 public function getServices() {
  return $this->services;
 }

 public function addServices($services) {
  foreach ($services as $service) {
   $this->services[] = $service;
  }
 }

 public function hasService($service) {
  return array_search($service, $this->services) !== false;
 }

}

class Discoverer {

 const ID_SELECT_URL = 'http://specs.openid.net/auth/2.0/identifier_select';

 private $version;        // OpenID 2 teminology   || OpenID 1 terminology
                          // --------------------- || ----------------------
 private $userSuppliedId; // User supplied ID      || [Same as Claimed ID]
 private $claimedId;      // Claimed ID            || Claimed ID
 private $endpointUrl;    // OP Endpoint URL       || Identity Provider
 private $opLocalId;      // OP-local ID           || Delegate

 private $servers = array();

 public function __construct($uri, $normalise = true) {
  if ($uri !== null) {
   $this->discover($this->userSuppliedId = ($normalise ? $this->normalise($uri) : $uri));
  }
 }

 public function getEndpointUrl() {
  return $this->endpointUrl;
 }

 public function getUserSuppliedId() {
  return $this->userSuppliedId;
 }

 public function getClaimedId() {
  return $this->claimedId;
 }

 public function getOpLocalId() {
  return $this->opLocalId;
 }

 public function getVersion() {
  return $this->version;
 }

 public function hasServer($endpointUrl) {
  return isset($this->servers[$endpointUrl]);
 }

 public static function normalise($uri) {
  // Strip xri:// prefix
  if (substr($uri, 0, 6) == 'xri://') {
   $uri = substr($uri, 6);
  }

  // If the first char is a global context symbol, treat it as XRI
  if (in_array($uri[0], array('=', '@', '+', '$', '!'))) {
   // TODO: Implement
   throw new Exception('This implementation does not currently support XRI');
  }

  // Add http:// if needed
  if (strpos($uri, '://') === false) {
   $uri = 'http://' . $uri;
  }

  $bits = @parse_url($uri);

  $result = $bits['scheme'] . '://';
  if (defined('OPENID_ALLOWUSER') && isset($bits['user'])) {
   $result .= $bits['user'];
   if (isset($bits['pass'])) {
    $result .= ':' . $bits['pass'];
   }
   $result .= '@';
  }
  $result .= preg_replace('/\.$/', '', $bits['host']);

  if (isset($bits['port']) && !empty($bits['port']) &&
     (($bits['scheme'] == 'http' && $bits['port'] != '80') ||
      ($bits['scheme'] == 'https' && $bits['port'] != '443') ||
      ($bits['scheme'] != 'http' && $bits['scheme'] != 'https'))) {
   $result .= ':' . $bits['port'];
  }

  if (isset($bits['path'])) {
   do {
    $bits['path'] = preg_replace('#/([^/]*)/\.\./#', '/', str_replace('/./', '/', $old = $bits['path']));
   } while ($old != $bits['path']);
   $result .= $bits['path'];
  } else {
   $result .= '/';
  }

  if (defined('OPENID_ALLOWQUERY') && isset($bits['query'])) {
   $result .= '?' . $bits['query'];
  }

  return $result;
 }

 private function discover($uri) {
  Logger::log('Performing discovery for %s', $uri);

  if (!$this->yadisDiscover($uri)) {
   $this->htmlDiscover($uri);
  }
 }

 private function yadisDiscover($uri, $allowLocation = true) {
  Logger::log('Attempting Yadis discovery on %s', $uri);

  if ($allowLocation) {
   $this->claimedId = $uri;
  }

  $ctx = stream_context_create(array(
    'http' => array(
      'header' => "Accept: application/xrds+xml\r\n",
    )
  ));

  $fh = @fopen($uri, 'r', false, $ctx);

  if (!$fh) {
   Logger::log('Unable to open stream');
   return false;
  }

  $details = stream_get_meta_data($fh);

  $data = '';
  while (!feof($fh) && strpos($data, '</head>') === false) {
   $data .= fgets($fh);
  }

  fclose($fh);

  foreach ($details['wrapper_data'] as $line) {
   if ($allowLocation && preg_match('/^X-XRDS-Location:\s*(.*?)$/i', $line, $m)) {
    // TODO: Allow relative URLs?
    $this->handleRedirects($details);
    return $this->yadisDiscover($m[1], false);
   } else if (preg_match('#^Content-type:\s*application/xrds\+xml(;.*?)?$#i', $line)) {
    $this->handleRedirects($details);
    return $this->parseYadis($data);
   }
  }

  if (($url = $this->parseYadisHTML($data)) !== false) {
   $this->handleRedirects($details);
   return $this->yadisDiscover($url, false);
  }
 }

 private function parseYadis($data) {
  $sxml = @new SimpleXMLElement($data);

  if (!$sxml) {
   Logger::log('Failed to parse XRDS data as XML');
   // TODO: Die somehow?
   return false;
  }

  // TODO: Better handling of namespaces
  $found = false;
  foreach ($sxml->XRD->Service as $service) {
   $services = array();
   $server = null;

   foreach ($service->Type as $type) {
    Logger::log('Found service of type %s', $type);

    if ((String) $type == 'http://specs.openid.net/auth/2.0/server') {
     $this->version = 2;
     $this->endpointUrl = (String) $service->URI;
     $this->claimedId = $this->opLocalId = self::ID_SELECT_URL;
     Logger::log('OpenID EP found (server). End point: %s, claimed id: %s, op local id: %s', $this->endpointUrl, $this->claimedId, $this->opLocalId);
     $found = true;
     $this->servers[$this->endpointUrl] = $server = new Server($this->endpointUrl, $this->version);
    } else if ((String) $type == 'http://specs.openid.net/auth/2.0/signon') {
     $this->version = 2;
     $this->endpointUrl = (String) $service->URI;

     if (isset($service->LocalID)) {
      $this->opLocalId = (String) $service->LocalID;
     } else {
      $this->opLocalId = self::ID_SELECT_URL;
      $this->claimedId = self::ID_SELECT_URL;
     }

     Logger::log('OpenID EP found (signon). End point: %s, claimed id: %s, op local id: %s', $this->endpointUrl, $this->claimedId, $this->opLocalId);
     $found = true;
     $this->servers[$this->endpointUrl] = $server = new Server($this->endpointUrl, $this->version);
    } else {
     $services[] = (String) $type;
    }
   }

   if ($server != null) {
    $server->addServices($services);
   }
  }

  return $found;
 }

 private function parseYadisHTML($data) {
  $meta = self::getMetaTags($data);

  if (isset($meta['x-xrds-location'])) {
   Logger::log('Found XRDS meta tag: %s', $meta['x-xrds-location']);
   // TODO: Allow relative URLs?
   return $meta['x-xrds-location'];
  }

  return false;
 }

 private function htmlDiscover($uri) {
  Logger::log('Performing HTML discovery on %s', $uri);

  $fh = @fopen($uri, 'r');

  if (!$fh) {
   Logger::log('Unable to open stream');
   return;
  }

  $this->claimedId = $uri;

  $details = stream_get_meta_data($fh);

  $this->handleRedirects($details);

  Logger::log('Claimed identity: %s', $this->claimedId);

  $data = '';
  while (!feof($fh) && strpos($data, '</head>') === false) {
   $data .= fgets($fh);
  }

  fclose($fh);

  $this->parseHtml($data);
 }

 protected function handleRedirects($details) {
  foreach ($details['wrapper_data'] as $line) {
   if (preg_match('/^Location: (.*?)$/i', $line, $m)) {
    if (strpos($m[1], '://') !== false) {
     // Fully qualified URL
     $this->claimedId = $m[1];
     Logger::log('Redirection (full qualified) to ' . $m[1]);
    } else if ($m[1][0] == '/') {
     // Absolute URL
     $this->claimedId = preg_replace('#^(.*?://.*?)/.*$#', '\1', $this->claimedId) . $m[1];
     Logger::log('Redirection (absolute) to ' . $m[1] . ': ' . $this->claimedId);
    } else {
     // Relative URL
     $this->claimedId = preg_replace('#^(.*?://.*/).*?$#', '\1', $this->claimedId) . $m[1];
     Logger::log('Redirection (relative) to ' . $m[1] . ': ' . $this->claimedId);
    }
   }
   $this->claimedId = self::normalise($this->claimedId);
  }
 }

 protected static function getLinks($data) {
  return self::getTags($data, 'link', 'rel', 'href', true);
 }

 protected static function getMetaTags($data) {
  return self::getTags($data, 'meta', 'http-equiv', 'content');
 }

 protected static function getTags($data, $tag, $att1, $att2, $split = false) {
  preg_match_all('#<' . $tag . '\s*(.*?)\s*/?' . '>#is', $data, $matches);

  $links = array();

  foreach ($matches[1] as $link) {
   $rel = $href = null;

   if (preg_match('#' . $att1 . '\s*=\s*(?:([^"\'>\s]*)|"([^">]*)"|\'([^\'>]*)\')(?:\s|$)#is', $link, $m)) {
    array_shift($m);
    $rel = implode('', $m);
   }

   if (preg_match('#' . $att2 . '\s*=\s*(?:([^"\'>\s]*)|"([^">]*)"|\'([^\'>]*)\')(?:\s|$)#is', $link, $m)) {
    array_shift($m);
    $href = implode('', $m);
   }

   if ($split) {
    foreach (explode(' ', strtolower($rel)) as $part) {
     $links[$part] = html_entity_decode($href);
    }
   } else {
    $links[strtolower($rel)] = html_entity_decode($href);
   }
  }

  return $links;
 }

 public function parseHtml($data) {
  $links = self::getLinks($data);

  if (isset($links['openid2.provider'])) {
   $this->version = 2;
   $this->endpointUrl = $links['openid2.provider'];
   //$this->servers[] = new Server($this->server, 2);

   $this->claimedId = $this->userSuppliedId;
   $this->opLocalId = isset($links['openid2.local_id']) ? $links['openid2.local_id'] : $this->claimedId;

   $this->servers[$this->endpointUrl] = $server = new Server($this->endpointUrl, $this->version);
   Logger::log('OpenID EP found. End point: %s, claimed id: %s, op local id: %s', $this->endpointUrl, $this->claimedId, $this->opLocalId);
  } else if (isset($links['openid.server'])) {
   $this->version = 1;
   $this->endpointUrl = $links['openid.server'];
   //$this->servers[] = new Server($this->server, 2);

   $this->claimedId = $this->userSuppliedId;

   if (isset($links['openid.delegate'])) {
    $this->opLocalId = $links['openid.delegate'];
   }

   $this->servers[$this->endpointUrl] = $server = new Server($this->endpointUrl, $this->version);
   Logger::log('OpenID EP found. End point: %s, claimed id: %s, op local id: %s', $this->endpointUrl, $this->claimedId, $this->opLocalId);
  }
 }

}

?>
