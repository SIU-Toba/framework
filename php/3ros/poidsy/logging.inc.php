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

class Logger {

 const ENABLE_LOGGING = true;
 const LOGGING_FILENAME = '/poidsy-debug.log';
 const TRUNCATE_ARGS = true;

 private static $fh;

 public static function log($message) {
  if (self::ENABLE_LOGGING) {
	  $dir_base = toba::instancia()->get_path_instalacion_proyecto(toba::proyecto()->get_id()). '/logs/';
   if (self::$fh == null) {
    self::$fh = fopen($dir_base . self::LOGGING_FILENAME, 'a');
   }

   $args = func_get_args();
   $arg = call_user_func_array('sprintf', $args);
   fputs(self::$fh, sprintf("[%s] %s: %s\n", date('r'), self::getCaller(), $arg));
  }
 }

 protected static function getCaller() {
  $traces = debug_backtrace(); // First one will be getCaller, next Log::logger
  $trace = $traces[2];

  array_walk($trace['args'], array('Logger', 'formatArg'));

  $class = isset($trace['class']) ? $trace['class'] : '';
  $type = isset($trace['type']) ? $trace['type'] : '';

  return sprintf('%s:%s %s%s%s(%s)', basename($trace['file']), $traces[1]['line'], $class, $type, $trace['function'], implode(', ', $trace['args']));
 }

 protected static function formatArg(&$value, $key) {
  if (strlen($value) > 30 && self::TRUNCATE_ARGS) {
   $value = substr($value, 0, 27) . '...';
  }
  $value = str_replace("\n", '  ', $value);
 }

}

?>
