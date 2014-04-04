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

 class AttributeExchange {

  const NS = 'http://openid.net/srv/ax/1.0';

  const USERNAME = 'http://axschema.org/namePerson/friendly';
  const NAMESUFFIX = 'http://axschema.org/namePerson/suffix';
  const NAMEPREFIX = 'http://axschema.org/namePerson/prefix';
    
  const EMAIL = 'http://axschema.org/contact/email'; 
  const FULLNAME = 'http://axschema.org/namePerson';
  const DATEOFBIRTH = 'http://axschema.org/birthDate';
  const GENDER = 'http://axschema.org/person/gender';
  const POSTCODE = 'http://axschema.org/contact/postalCode/home';
  const COUNTRY = 'http://axschema.org/contact/country/home';
  const LANGUAGE = 'http://axschema.org/pref/language';
  const TIMEZONE = 'http://axschema.org/pref/timezone';

  const FIRSTNAME = 'http://axschema.org/namePerson/first';
  const LASTNAME = 'http://axschema.org/namePerson/last';
  const MIDDLENAME = 'http://axschema.org/namePerson/middle';
  
  const COMPANYNAME = 'http://axschema.org/company/name';
  const JOBTITLE = 'http://axschema.org/company/title';

  const YEAROFBIRTH = 'http://axschema.org/birthDate/birthYear';
  const MONTHOFBIRTH = 'http://axschema.org/birthDate/birthMonth';
  const DAYOFBIRTH = 'http://axschema.org/birthDate/birthday';

  
  
  
  private static $aliases = array();
  private static $required = array();
  private static $optional = array();
  private static $count = array();

  public static function addRequiredType($alias, $uri, $count = null) {
   self::$required[] = $alias;

   self::addType($alias, $uri, $count);
  }

  public static function addOptionalType($alias, $uri, $count = null) {
   self::$optional[] = $alias;

   self::addType($alias, $uri, $count);
  }

  private static function addType($alias, $uri, $count) {
   self::$aliases[$alias] = $uri;

   if ($count != null && (ctype_digit($count) || is_int($count) || $count == 'unlimited')) {
    self::$count[$alias] = $count;
   }
  }

  public function parseResponse() {
   $ns = false;

   foreach ($_REQUEST as $k => $v) {
    if (substr($k, 0, 10) == 'openid_ns_' && $v == self::NS) {
     $ns = substr($k, 10);
     break;
    }
   }

   if ($ns === false) { return; }

   $_SESSION['openid']['ax'] = array(
    'types' => array(),
    'data' => array(),
    'counts' => array()
   );

   foreach ($_REQUEST as $k => $v) {
    if (substr($k, 0, 8 + strlen($ns)) == "openid_{$ns}_") {
     // TODO: Need to check mode etc

     $rest = substr($k, 8 + strlen($ns));
     if (substr($rest, 0, 5) == 'type_') {
      $_SESSION['openid']['ax']['types'][substr($rest, 5)] = $v;
     } else if (substr($rest, 0, 6) == 'count_') {
      $_SESSION['openid']['ax']['counts'][substr($rest, 6)] = (int) $v;
     }
    }
   }

   foreach ($_SESSION['openid']['ax']['types'] as $alias => $uri) {
    if (!isset($_SESSION['openid']['ax']['counts'][$alias])) {
     $_SESSION['openid']['ax']['counts'][$alias] = 1;
    }

    $count = $_SESSION['openid']['ax']['counts'][$alias];

    if ($count > 1) {
     for ($i = 1; $i < $count + 1; $i++) {
      $_SESSION['openid']['ax']['data'][$alias][] = $_REQUEST["openid_{$ns}_value_{$alias}_$i"];
     }
    } else if ($count == 1) {
		if (isset($_REQUEST["openid_{$ns}_value_$alias"."_1"])) {
			$_SESSION['openid']['ax']['data'][$alias] = $_REQUEST["openid_{$ns}_value_$alias"."_1"];
		} else {
		$_SESSION['openid']['ax']['data'][$alias] = $_REQUEST["openid_{$ns}_value_$alias"];
		}
	}
   }
  }

  public function decorate(&$args, $ns) {
   $args['openid.ns.' . $ns] = self::NS;
   $args['openid.' . $ns . '.mode'] = 'fetch_request';

   foreach (array_merge(self::$optional, self::$required) as $alias) {
    $args['openid.' . $ns . '.type.' . $alias] = self::$aliases[$alias];
   }

   foreach (self::$count as $alias => $count) {
    $args['openid.' . $ns . '.count.' . $alias] = $count;
   }

   if (!empty(self::$optional)) {
    $args['openid.' . $ns . '.if_available'] = implode(',', self::$optional);
   }

   if (!empty(self::$required)) {
    $args['openid.' . $ns . '.required'] = implode(',', self::$required);
   }
  }

 }

 $ax = new AttributeExchange();
 $_POIDSY['decorators'][] = $ax;
 $_POIDSY['handlers'][] = $ax;
 unset($ax);

?>
