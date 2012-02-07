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

 // This file tests to see if your environment is compatible with Poidsy.

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
                      "http://www.w3.org/TR/html4/strict.dtd">
<html>
 <head>
  <title>Poidsy compatibility test</title>
  <style type="text/css">
   table { border-collapse: collapse; }
   td, th { border: 1px solid #000; padding: 10px; }
   td.error { text-align: center; color: #fff; background-color: #c00; }
   td.succ { text-align: center; color: #fff; background-color: #0c0; }
   td.warn { text-align: center; color: #fff; background-color: #c70; }
   td { max-width: 400px; }
   code { font-size: small; }
  </style>
 </head>
 <body>
  <h1>Poidsy compatibility test</h1>
  <table>
<?PHP

 function doTest($name, $result, $failinfo) {
  echo '<tr><th>', htmlentities($name), '</th>';
  echo '<td class="', $result ? 'succ' : 'error', '">';
  echo $result ? 'Passed' : 'Failed';
  echo '</td>';
  if (!$result) { echo '<td>', $failinfo, '</td>'; }
  echo '</tr>';
 }

 echo '<tr><th colspan="2">Poidsy requirements</th></tr>';
 doTest('PHP Version', version_compare(PHP_VERSION, '5.2.10', '>='), 'Poidsy requires PHP version 5.2.10 or greater to run');
 doTest('Allow_url_fopen', ini_get('allow_url_fopen'), 'Poidsy requires allow_url_fopen to be configured to true');

 echo '<tr><th colspan="2">Associate mode requirements</th></tr>';
 doTest('hash_hmac function', function_exists('hash_hmac'), 'Poidsy requires the hash_hmac function to use associate mode. It should be available in PHP 5.2.0 or greater, unless you\'ve explicitly disabled it when compiling PHP');
 doTest('Keycache writable', is_writable(dirname(__FILE__) . '/keycache.php'), 'Poidsy requires write access to the keycache.php file in its directory. Without it, Poidsy will be unable to use associate mode.');
 echo '<tr><th colspan="2">Diffie-Hellman key exchange requirements</th></tr>';

 $extensions = array(
        array('modules' => array('gmp', 'php_gmp'),
              'extension' => 'gmp'),
        array('modules' => array('bcmath', 'php_bcmath'),
              'extension' => 'bcmath')
    );

 $best = '';
 foreach ($extensions as $ext) {
  if ($ext['extension'] && extension_loaded($ext['extension'])) {
   $loaded = true;
  } elseif (function_exists('dl')) {
   foreach ($ext['modules'] as $module) {
    if (@dl($module . "." . PHP_SHLIB_SUFFIX)) {
     $loaded = true;
     break;
    }
   }
  }

  if ($loaded) {
   $best = $ext['extension'];
   break; 
  }
 }

  echo '<tr><th>Bigmath support</th>';
  echo '<td class="', $best == 'gmp' ? 'succ' : ($best == 'bcmath' ? 'warn' : 'error'), '">';
  echo $best != '' ? $best : 'Failed';
  echo '</td>';
  if ($best == 'bcmath') {
   echo '<td>Your version of PHP has bcmath support, which is good enough for Poidsy to use, but is much slower than gmp.</td>';
  } else if ($best == '') {
   echo '<td>Your version of PHP doesn\'t have support for either gmp (preferred) or bcmath. Poidsy needs one of these libraries to perform D-H key exchange.</td>';
  }
  echo '</tr>';


?>
  </table>
 </body>
</html>
