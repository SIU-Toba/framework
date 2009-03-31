<?php

class Toba_Sniffs_Files_FileEncodingSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OPEN_TAG);

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
		$previousOpenTag = $phpcsFile->findPrevious(array(T_OPEN_TAG), ($stackPtr - 1));
		if ($previousOpenTag !== false) {
			return;
		}
		$tokens = $phpcsFile->getTokens();
		$tokenLimit         = count($tokens);
		$tokenCount         = 0;
		for (; $tokenCount < $tokenLimit; $tokenCount++) {
			$string = $tokens[$tokenCount]['content'];
			if (! $this->es_latin1($string)) {
				$error = "[Archivo#codificacion] No está permitidas cadenas de texto en una codificación distinta a LATIN1 (iso88591)";
				$phpcsFile->addError($error, $tokenCount);
			}
		}
    }//end process()

	function es_latin1($string)
	{
		for ($i=0; $i < strlen($string);$i++){
			if (ord($string[$i]) == 195) {
				return false;
			}
		}
		return true;
	}


}//end class

?>
