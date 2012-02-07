<?php

/**
 * BigMath: A math library wrapper that abstracts out the underlying
 * long integer library.
 *
 * Original code (C) 2005 JanRain <openid@janrain.com>
 * Modifications (C) 2007 Stephen Bounds.
 * Further modifications (C) 2008-2010 Chris Smith.
 *
 * Licensed under the LGPL.
 */

/**
 * Base BigMath class which will be extended by a big-integer math library
 * such as bcmath or gmp. 
 */
abstract class BigMath {

  /** File handle for our random data source. */
  protected $randsource = false;

  /** Duplicate cache for rand(). */
  protected $duplicate_cache = array();

  /** Singleton reference to our bigmath class. */
  private static $me = null;

  /**
   * Converts the specified positive integer to the shortest possible
   * big-endian, two's complement representation.
   * 
   * @param long The integer to be converted
   * @return the btwoc representation of the integer
   */    
  public function btwoc($long) {
    $cmp = $this->cmp($long, 0);

    if ($cmp < 0) {
      throw new Exception('$long must be a positive integer.');
    } else if ($cmp == 0) {
      return "\x00";
    }

    $bytes = array();

    while ($this->cmp($long, 0) > 0) {
      array_unshift($bytes, $this->mod($long, 256));
      $long = $this->div($long, 256);
    }

    if ($bytes && ($bytes[0] > 127)) {
      array_unshift($bytes, 0);
    }

    // Convert to \xHH\xHH... format and return
    $string = '';

    foreach ($bytes as $byte) {
      $string .= pack('C', $byte);
    }

    return $string;
  }

  /**
   * Converts the specified btwoc representation of an integer back to the
   * original integer.
   *
   * @param str The btwoc representation to be "undone"
   * @return The corresponding integer 
   */
  public function btwoc_undo($str) {
    if ($str == null) {
      return null;
    }

    $bytes = array_values(unpack('C*', $str));

    $n = $this->init(0);

    if ($bytes && ($bytes[0] > 127)) {
      throw new Exception('$str must represent a positive integer');
    }

    foreach ($bytes as $byte) {
      $n = $this->mul($n, 256);
      $n = $this->add($n, $byte);
    }

    return $n;
  }

  /**
   * Returns a random number up to the specified maximum.
   *
   * @param max The maximum value to return
   * @return A random number between 0 and the specified max 
   */
  public function rand($max) {
    // Used as the key for the duplicate cache
    $rbytes = $this->btwoc($max);

    if (array_key_exists($rbytes, $this->duplicate_cache)) {
      list($duplicate, $nbytes) = $this->duplicate_cache[$rbytes];
    } else {
      if ($rbytes[0] == "\x00") {
        $nbytes = strlen($rbytes) - 1;
      } else {
        $nbytes = strlen($rbytes);
      }

      $mxrand = $this->pow(256, $nbytes);

      // If we get a number less than this, then it is in the
      // duplicated range.
      $duplicate = $this->mod($mxrand, $max);

      if (count($this->duplicate_cache) > 10) {
        $this->duplicate_cache = array();
      }

      $this->duplicate_cache[$rbytes] = array($duplicate, $nbytes);
    }

    do {
      $bytes = "\x00" . $this->getRandomBytes($nbytes);
      $n = $this->btwoc_undo($bytes);
      // Keep looping if this value is in the low duplicated range
    } while ($this->cmp($n, $duplicate) < 0);

    return $this->mod($n, $max);
  }

  /**
   * Get the specified number of random bytes.
   *
   * Attempts to use a cryptographically secure (not predictable)
   * source of randomness. If there is no high-entropy
   * randomness source available, it will fail.

   * @param num_bytes The number of bytes to retrieve
   * @return The specified number of random bytes
   */
  public function getRandomBytes($num_bytes) {
    if (!$this->randsource) {
     $this->randsource = @fopen('/dev/urandom', 'r');
    }

    if ($this->randsource) {
      return fread($this->randsource, $num_bytes);
    } else {
      // pseudorandom used
      $bytes = '';
      for ($i = 0; $i < $num_bytes; $i += 4) {
        $bytes .= pack('L', mt_rand());
      }
      return substr($bytes, 0, $num_bytes);
    }      
  }

  public abstract function init($number, $base = 10);

  public abstract function add($x, $y);
  public abstract function sub($x, $y);
  public abstract function mul($x, $y);
  public abstract function div($x, $y);
  public abstract function cmp($x, $y);

  public abstract function mod($base, $modulus);
  public abstract function pow($base, $exponent);

  public abstract function powmod($base, $exponent, $modulus);

  public abstract function toString($num);

  /**
   * Detect which math library is available
   *
   * @return The extension details of the first available extension,
   * or false if no extensions are available.
   */
  private static function BigMath_Detect() {
    $extensions = array(
  	array('modules' => array('gmp', 'php_gmp'),
              'extension' => 'gmp',
              'class' => 'BigMath_GmpMathWrapper'),
  	array('modules' => array('bcmath', 'php_bcmath'),
              'extension' => 'bcmath',
              'class' => 'BigMath_BcMathWrapper')
    );

    $loaded = false;
    foreach ($extensions as $ext) {
      // See if the extension specified is already loaded.
      if ($ext['extension'] && extension_loaded($ext['extension'])) {
        $loaded = true;
      }

      // Try to load dynamic modules.
      if (!$loaded && function_exists('dl')) {
        foreach ($ext['modules'] as $module) {
          if (@dl($module . "." . PHP_SHLIB_SUFFIX)) {
            $loaded = true;
            break;
          }
        }
      }

      if ($loaded) {
        return $ext;
      }
    }

    return false;
  }

  /**
   * Returns a singleton instance of the best possible BigMath class.
   * 
   * @return A singleton instance to a BigMath class.
   */
  public static function &getBigMath() {
    if (self::$me == null) {
      $ext = self::BigMath_Detect();
      $class = $ext['class'];
      self::$me = new $class();
    }

    return self::$me;
  }

}

/**
 * Exposes BCmath math library functionality.
 */
class BigMath_BcMathWrapper extends BigMath {
  public function init($number, $base = 10) { return $number; }

  public function add($x, $y) { return bcadd($x, $y);  }
  public function sub($x, $y) { return bcsub($x, $y);  }
  public function mul($x, $y) { return bcmul($x, $y);  }
  public function div($x, $y) { return bcdiv($x, $y);  }
  public function cmp($x, $y) { return bccomp($x, $y); }

  public function mod($base, $modulus)    { return bcmod($base, $modulus); }
  public function pow($base, $exponent)   { return bcpow($base, $exponent); }

  public function powmod($base, $exponent, $modulus) { return bcpowmod($base, $exponent, $modulus); }

  public function toString($num) { return $num; }
}

/**
 * Exposes GMP math library functionality.
 */
class BigMath_GmpMathWrapper extends BigMath {
  public function init($number, $base = 10) {  return gmp_init($number, $base); }

  public function add($x, $y) { return gmp_add($x, $y);   }
  public function sub($x, $y) { return gmp_sub($x, $y);   }
  public function mul($x, $y) { return gmp_mul($x, $y);   }
  public function div($x, $y) { return gmp_div_q($x, $y); }
  public function cmp($x, $y) { return gmp_cmp($x, $y);   }

  public function mod($base, $modulus)  { return gmp_mod($base, $modulus);  }
  public function pow($base, $exponent) { return gmp_pow($base, $exponent); }

  public function powmod($base, $exponent, $modulus) { return gmp_powm($base, $exponent, $modulus); }

  public function toString($num) { return gmp_strval($num); }
}

?>
