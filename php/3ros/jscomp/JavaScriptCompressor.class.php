<?php
/**
 * JavaScriptCompressor class,
 *	removes comments or pack JavaScript source[s] code.
 * ______________________________________________________________
 * JavaScriptCompressor (just 2 public methods)
 *    |
 *    |________ getClean(jsSource:mixed [, removeSpaces:bool]):string
 *    |         	return or more JavaScript code without comments,
 *    |         	by default removes some spaces too
 *    |
 *    |________ getPacked(jsSource:mixed):string
 *              	return or more JavaScript code packed,
 *	        	using getClean and remove spaces too
 * --------------------------------------------------------------
 * Note about $jsSource input varible:
 * 	this var should be a string (i.e. $jsSource = file_get_contents("myFile.js");)
 *      should be an array of strings (i.e. array(file_get_contents("1.js"), file_get_contents("2.js"), ... ))
 *      should be an array with 1 or 2 keys:
 *      	(i.e. array('code'=>file_get_contents("mySource.js")))
 *              (i.e. array('code'=>file_get_contents("mySource.js"), 'name'=>'mySource'))
 *      ... and should be an array of arrays created with theese rules
 *      array(
 *		file_get_contents("secret.js"),
 *              array('code'=>$anotherJS),
 *              array('code'=>$myJSapplication, 'name'=>'JSApplication V 1.0')
 *      )
 *
 *      The name used on dedicated key, will be write on parsed source header
 * --------------------------------------------------------------
 * Note about returned strings:
 * 	Your browser should wrap very long strings, then don't use
 *      cut and paste from your browser, save output into your database or directly
 *      in a file or print them only inside <script> and </script> tags
 * --------------------------------------------------------------
 * Note about parser performance:
 * 	"Char by char parser" is probably the best and safest way to remove comments
 *      from a generic language code.
 *      With pure PHP embed class this class should be slow and not really safe
 *      for your server performance then don't parse JavaScript runtime for each
 *      file you need and create some "parsed" caching system
 *      (at least while i've not created a compiled version of theese class functions).
 * --------------------------------------------------------------
 * @Compatibility	>= PHP 4
 * @Author		Andrea Giammarchi
 * @Site		http://www.devpro.it/
 * @Date		2006/05/31
 * @LastMOd		2006/06/27 [fixed wrong JavaScript conversion with a new faster and secure method compatible with IE 5 too]
 * @Version		0.6b [new base62 experimental convertion]
 * @Dependencies	BaseConvert.class.php
 * @Browsers		FireFox, Opera, Kde, Safari, IE and maybe others
 * @Credits		Dean Edwards for his originally idea [dean.edwards.name] and his JavaScript packer*
 * 			[ *that maybe works better than this totally rewrote PHP version ]
 */
class JavaScriptCompressor {
	
	/**
	 * public variables
         * 	stats:string		after every compression has some informations
         *      version:string		version of this class
	 */
	var	$stats = '',
		$version = '0.6b';
	
	/** 'private' variables, any comment sorry */
	var	$__startTime = 0,
		$__sourceLength = 0,
		$__sourceNewLength = 0,
		$__totalSources = 0,
		$__inlineFunction = '',
		$__sources = array(),
		$__BC = null;
	
	/**
	 * public constructor
         * 	creates a new BaseConvert class variable (base 36 or base 62 initialize with true)
         * NOTE: for small files is not recommended to use true on constructor, use it only with sources greater than 60Kb
         * NOTE: remember that base62 convertion will be slower than base 36 convertion on server and on client too
	 */
	function JavaScriptCompressor($bigSource = false) {
		if($bigSource) {
			$this->__BC = new BaseConvert('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
			$this->__inlineFunction = 'function(a,g,c){g=a.length;while(g)c+=(function(d,e){return (d<10?d:d-7<36?d-7:d-13)+(e*61)}(a.charCodeAt(--g)-48,a.length-1-g));return c;}(\\"$1\\",0,0)';
		}
		else {
			$this->__BC = new BaseConvert('0123456789abcdefghijklmnopqrstuvwxyz');
			$this->__inlineFunction = 'parseInt(\\"$1\\",36)';
		}
	}
	
	/**
	 * public method
         * 	getClean(mixed [, bool]):string
         *      compress JavaScript removing comments and somespaces (on by default)
	 */
	function getClean($jsSource, $removeSpaces = true) {
		return $this->__commonInitMethods($jsSource, $removeSpaces, false);
	}
	
	/**
	 * public method
         * 	getPacked(mixed):string
         *      compress JavaScript replaceing words and removing comments and somespaces
         *      NOTE: 2nd and 3rd parameters are just for compatibility with old version
	 */
	function getPacked($jsSource, $cc = true, $rs = true) {
		return $this->__commonInitMethods($jsSource, $rs = true, true);
	}
	
	/** 'private' methods, any comment sorry */
	function __backSlashFilter(&$jsSource, &$a, &$next, &$filter) {
		$next = 1;
		while(($c = $a - $next) > 0 && $jsSource{$c} === '\\')
			++$next;
		if(($next - 1) % 2 === 0)
			$filter = false;
	}
	function __commonInitMethods(&$jsSource, &$removeSpaces, $packed) { 
		$header = '';
		$this->__startTime = $this->__getTime();
		$this->__sourceLength = 0;
		$this->__sourceManager($jsSource);
		$this->__removeAllComments($removeSpaces);
		$header = $this->__getHeader();
		for($a = 0, $b = $this->__totalSources; $a < $b; $a++)
			$this->__sources[$a] = &$this->__sources[$a]['code'];
		$this->__sources = implode(';', $this->__sources);
		if($packed)
			$this->__sources = $this->__pack($this->__sources);
		$this->__sourceNewLength = strlen($this->__sources);
		$this->__setStats();
		return $header.$this->__sources;
	}
	function __getHeader() {
		return implode('', array(
			'/* ',$this->__getScriptNames(),'JSPacker ',$this->version,' [www.devpro.it], ',
			'thanks to Dean Edwards for idea [dean.edwards.name]',
			" */\r\n"		
		));
	}
	function __getScriptNames() {
		$a = 0;
		$result = array();
		for($b = $this->__totalSources; $a < $b; $a++) {
			if($this->__sources[$a]['name'] !== '')
				array_push($result, $this->__sources[$a]['name']);
		}
		$a = count($result);
		if($a-- > 0)
			$result[$a] .= ' with ';
		return $a < 0 ? '' : implode(', ', $result);
	}
	function __getSize($size, $dec = 2) {
		$toEval = '';
		$type = array('bytes', 'Kb', 'Mb', 'Gb');
		$nsize = $size;
		$times = 0;
		while($nsize > 1024) {
			$nsize = $nsize / 1024;
			$toEval .= '/1024';
			$times++;
		}
		if($times === 0)
			$fSize = $size.' '.$type[$times];
		else {
			eval('$size=($size'.$toEval.');');
			$fSize =  number_format($size, $dec, '.', '').' '.$type[$times];
		}
		return $fSize;
	}
	function __getTime($startTime = null) {
		list($usec, $sec) = explode(' ', microtime());
		$newtime = (float)$usec + (float)$sec;
		if($startTime !== null)
			$newtime = number_format(($newtime - $startTime), 3);
		return $newtime;
	}
	function __isRegularExpression(&$jsSource, &$spaces, &$regexp, $a) {
		$result = false;
		$char = '';
		while($a-- > 0) {
			$char = $jsSource{$a};
			if($char === '(' && $a > 7) {
				$char = substr($jsSource, $a - 7, 7);
				if(
					$char === $regexp[0] || (
						($char = substr($char, 1)) === $regexp[1]
					) || (
						($char = substr($char, 1)) === $regexp[2] ||
						$char === $regexp[3]
					)
				)
					$result = true;
			}
			elseif(!in_array($char, $spaces))
				$a = 0;
		}
		return $result;
	}
	function __pack(&$jsSource) {
		$container = array();
		$jsSource = preg_replace("/(\w+)/e", '$this->__BC->toBase($this->__wordsParser("\\1",$container));', $jsSource);
		return implode('', array(
			'eval((function(A,G){return eval(\'["\'+A.replace(/(\\\\|"|\')/g,\'\\\\$1\').replace(/(\\b\\w+\\b)/g,"\\",G[',$this->__inlineFunction,'],\\"")+\'"].join("")\')}("',
			addslashes($jsSource),'","',implode(',', $container),'".split(","))));'
		));
	}
	function __removeAllComments(&$removeSpaces) {
		$newLine = array("\n", "\r");
		$spaces = array(' ', "\n", "\r", "\t");
		$specialChars = array(
			',', '.', ';', '{', '}', '(', ')',
			':', '+', '-', '*', '=', '/', '%',
			'[', ']', '?', '!', '<', '>', '&', '|'
		);
		$regexp = array('replace', 'search', 'split', 'match');
		for($a = 0, $b = $this->__totalSources; $a < $b; $a++) {
			$this->__sources[$a]['code'] = $this->__removeComments(
				$this->__sources[$a]['code'],
				$newLine, $spaces, $specialChars, $regexp, $removeSpaces,
				strlen($this->__sources[$a]['code'])
			);
		}
	}
	function __removeSpacesAfterComments(&$jsSource, &$a, &$b, $firstAdd) {
		$a += $firstAdd;
		while($a < $b && preg_match("/[[:space:]]/", $jsSource{$a}))
			++$a;
		--$a;
	}
	function __removeComments(&$jsSource, &$newLine, &$spaces, &$specialChars, &$regexp, &$removeSpaces, $b) {
		$squote = $dquote = $sline = $mline = $reg = $allow = false;
		$next = $c = 0;
		$char = $nextChar = '';
		$newSource = str_repeat(' ', $b);
		for($a = 0; $a < $b; $a++) {
			$char = $jsSource{$a};
			if(($next = $a + 1) < $b) {
				$nextChar = $jsSource{$next};
				$allow = (!$reg && !$sline && !$mline && !$dquote && !$squote);
				if($allow && $char === '"') {
					$dquote = true;
					$newSource{($c++)} = $char;
				}
				elseif($allow && $char === '\'') {
					$squote = true;
					$newSource{($c++)} = $char;
				}
				elseif($allow && $char === '/' && $nextChar === '*') {
					$mline = true;
					++$a;
				}
				elseif($allow && $char === '/' && $nextChar === '/') {
					$sline = true;
					++$a;
				}
				elseif($allow && $char === '/') {
					$newSource{($c++)} = $char;
					$reg = $this->__isRegularExpression($jsSource, $spaces, $regexp, $a);
				}
				elseif($reg && $char === '/') {
					$newSource{($c++)} = $char;
					$this->__backSlashFilter($jsSource, $a, $next, $reg);
				}
				elseif($dquote && $char === '"') {
					$newSource{($c++)} = $char;
					$this->__backSlashFilter($jsSource, $a, $next, $dquote);
				}
				elseif($squote && $char === '\'') {
					$newSource{($c++)} = $char;
					$this->__backSlashFilter($jsSource, $a, $next, $squote);
				}
				elseif($mline && $char === '*' && $nextChar === '/') {
					$mline = false;
                                        $this->__removeSpacesAfterComments($jsSource, $a, $b, 2);
				}
				elseif($sline && in_array($nextChar, $newLine)) {
					$sline = false;
					$this->__removeSpacesAfterComments($jsSource, $a, $b, 1);
				}
				elseif($removeSpaces && $allow && in_array($char, $spaces)) {
					$next = $c - 1;
					if(
						$next >= 0 &&
						$newSource{$next} !== ' ' &&
						$nextChar !== ' ' &&
						!in_array($newSource{$next}, $specialChars) &&
						!in_array($nextChar, $specialChars)
					)

						$newSource{($c++)} = ' ';
				}
				elseif($reg || (!$sline && !$mline)) {
					$newSource{($c++)} = $char;
				}
			}
			elseif($next === $b && ($reg || (!$sline && !$mline)))
				$newSource{($c++)} = $char;
		}
		return rtrim($newSource);
	}
	function __setStats() {
		$this->stats = implode(' ', array(
			$this->__getSize($this->__sourceLength),
			'to',
			$this->__getSize($this->__sourceNewLength),
			'in',
			$this->__getTime($this->__startTime),
			'seconds'
		));
	}
	function __sourceManager(&$jsSource) {
		$b = count($jsSource);
		$this->__sources = array();
		if(is_string($jsSource))
			$this->__sourcePusher($jsSource, '');
		elseif(is_array($jsSource) && $b > 0) {
			if(isset($jsSource['code']))
				$this->__sourcePusher($jsSource['code'], (isset($jsSource['name']) ? $jsSource['name'] : ''));
			else {
				for($a = 0; $a < $b; $a++) {
					if(is_array($jsSource[$a]) && isset($jsSource[$a]['code'], $jsSource[$a]['name']))
						$this->__sourcePusher($jsSource[$a]['code'], trim($jsSource[$a]['name']));
					elseif(is_string($jsSource[$a]))
						$this->__sourcePusher($jsSource[$a], '');
				}
			}
		}
		$this->__totalSources = count($this->__sources);
	}
	function __sourcePusher(&$code, $name) {
		$this->__sourceLength += strlen($code);
		array_push($this->__sources, array('code'=>$code, 'name'=>$name));
	}
	function __wordsParser($str, &$d) {
		if(is_null($key = array_shift($key = array_keys($d,$str)))) {
			$key = count($d);
			array_push($d, $str);
		}
		return $key;
	}
}
?>