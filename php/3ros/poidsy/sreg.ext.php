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

 define('SREG_NICKNAME', 'nickname');
 define('SREG_EMAIL', 'email');
 define('SREG_FULLNAME', 'fullname');
 define('SREG_DOB', 'dob');
 define('SREG_GENDER', 'gender');
 define('SREG_POSTCODE', 'postcode');
 define('SREG_COUNTRY', 'country');
 define('SREG_LANGUAGE', 'language');
 define('SREG_TIMEZONE', 'timezone');

 define('SREG_ALL', SREG_NICKNAME . ',' . SREG_EMAIL . ',' . SREG_FULLNAME
             . ',' . SREG_DOB . ',' . SREG_GENDER . ',' . SREG_POSTCODE . ','
             . SREG_COUNTRY . ',' . SREG_LANGUAGE . ', ' . SREG_TIMEZONE);

 class SReg {

  public function parseResponse() {
   foreach (explode(',', SREG_ALL) as $reg) {
    $reg = 'openid_sreg_' . $reg;
    if (isset($_REQUEST[$reg])) {
     $_SESSION['openid']['sreg'][substr($reg, 12)] = $_REQUEST[$reg];
    }
   }
  }

  public function decorate(&$args, $ns) {
   if (defined('OPENID_SREG_REQUEST')) {
    $args['openid.sreg.required'] = OPENID_SREG_REQUEST;
   }

   if (defined('OPENID_SREG_OPTIONAL')) {
    $args['openid.sreg.optional'] = OPENID_SREG_OPTIONAL;
   }

   if (defined('OPENID_SREG_POLICY')) {
    $args['openid.sreg.policy_url'] = OPENID_SREG_POLICY;
   }
  }

 }

 $sreg = new SReg();
 $_POIDSY['decorators'][] = $sreg;
 $_POIDSY['handlers'][] = $sreg;

?>
