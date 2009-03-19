<?php
/**
 * Tokenizes JS code.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: JS.php,v 1.9 2008/05/19 06:11:21 squiz Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Tokenizes JS code.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.1.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Tokenizers_JS
{

    /**
     * A list of tokens that are allowed to open a scope.
     *
     * This array also contains information about what kind of token the scope
     * opener uses to open and close the scope, if the token strictly requires
     * an opener, if the token can share a scope closer, and who it can be shared
     * with. An example of a token that shares a scope closer is a CASE scope.
     *
     * @var array
     */
    public $scopeOpeners = array(
                            T_IF       => array(
                                           'start'  => T_OPEN_CURLY_BRACKET,
                                           'end'    => T_CLOSE_CURLY_BRACKET,
                                           'strict' => false,
                                           'shared' => false,
                                           'with'   => array(),
                                          ),
                            T_TRY      => array(
                                           'start'  => T_OPEN_CURLY_BRACKET,
                                           'end'    => T_CLOSE_CURLY_BRACKET,
                                           'strict' => true,
                                           'shared' => false,
                                           'with'   => array(),
                                          ),
                            T_CATCH    => array(
                                           'start'  => T_OPEN_CURLY_BRACKET,
                                           'end'    => T_CLOSE_CURLY_BRACKET,
                                           'strict' => true,
                                           'shared' => false,
                                           'with'   => array(),
                                          ),
                            T_ELSE     => array(
                                           'start'  => T_OPEN_CURLY_BRACKET,
                                           'end'    => T_CLOSE_CURLY_BRACKET,
                                           'strict' => false,
                                           'shared' => false,
                                           'with'   => array(),
                                          ),
                            T_FOR      => array(
                                           'start'  => T_OPEN_CURLY_BRACKET,
                                           'end'    => T_CLOSE_CURLY_BRACKET,
                                           'strict' => false,
                                           'shared' => false,
                                           'with'   => array(),
                                          ),
                            T_FUNCTION => array(
                                           'start'  => T_OPEN_CURLY_BRACKET,
                                           'end'    => T_CLOSE_CURLY_BRACKET,
                                           'strict' => false,
                                           'shared' => false,
                                           'with'   => array(),
                                          ),
                            T_WHILE    => array(
                                           'start'  => T_OPEN_CURLY_BRACKET,
                                           'end'    => T_CLOSE_CURLY_BRACKET,
                                           'strict' => false,
                                           'shared' => false,
                                           'with'   => array(),
                                          ),
                            T_DO       => array(
                                           'start'  => T_OPEN_CURLY_BRACKET,
                                           'end'    => T_CLOSE_CURLY_BRACKET,
                                           'strict' => true,
                                           'shared' => false,
                                           'with'   => array(),
                                          ),
                            T_SWITCH   => array(
                                           'start'  => T_OPEN_CURLY_BRACKET,
                                           'end'    => T_CLOSE_CURLY_BRACKET,
                                           'strict' => true,
                                           'shared' => false,
                                           'with'   => array(),
                                          ),
                            T_CASE     => array(
                                           'start'  => T_COLON,
                                           'end'    => T_BREAK,
                                           'strict' => true,
                                           'shared' => true,
                                           'with'   => array(
                                                        T_DEFAULT,
                                                        T_CASE,
                                                       ),
                                          ),
                            T_DEFAULT  => array(
                                           'start'  => T_COLON,
                                           'end'    => T_BREAK,
                                           'strict' => true,
                                           'shared' => true,
                                           'with'   => array(T_CASE),
                                          ),
                           );

    /**
     * A list of tokens that end the scope.
     *
     * This array is just a unique collection of the end tokens
     * from the _scopeOpeners array. The data is duplicated here to
     * save time during parsing of the file.
     *
     * @var array
     */
    public $endScopeTokens = array(
                              T_CLOSE_CURLY_BRACKET,
                              T_BREAK,
                             );

    /**
     * A list of special JS tokens and their types.
     *
     * @var array
     */
    protected $tokenValues = array(
                              'function'  => 'T_FUNCTION',
                              'prototype' => 'T_PROTOTYPE',
                              'try'       => 'T_TRY',
                              'catch'     => 'T_CATCH',
                              'return'    => 'T_RETURN',
                              'if'        => 'T_IF',
                              'else'      => 'T_ELSE',
                              'do'        => 'T_DO',
                              'while'     => 'T_WHILE',
                              'for'       => 'T_FOR',
                              'var'       => 'T_VAR',
                              'true'      => 'T_TRUE',
                              'false'     => 'T_FALSE',
                              'null'      => 'T_NULL',
                              'this'      => 'T_THIS',
                              '('         => 'T_OPEN_PARENTHESIS',
                              ')'         => 'T_CLOSE_PARENTHESIS',
                              '{'         => 'T_OPEN_CURLY_BRACKET',
                              '}'         => 'T_CLOSE_CURLY_BRACKET',
                              '['         => 'T_OPEN_SQUARE_BRACKET',
                              ']'         => 'T_CLOSE_SQUARE_BRACKET',
                              '?'         => 'T_INLINE_THEN',
                              '.'         => 'T_OBJECT_OPERATOR',
                              '+'         => 'T_PLUS',
                              '-'         => 'T_MINUS',
                              '*'         => 'T_MULTIPLY',
                              '%'         => 'T_MODULUS',
                              '/'         => 'T_DIVIDE',
                              ','         => 'T_COMMA',
                              ';'         => 'T_SEMICOLON',
                              ':'         => 'T_COLON',
                              '<'         => 'T_LESS_THAN',
                              '>'         => 'T_GREATER_THAN',
                              '<='        => 'T_IS_SMALLER_OR_EQUAL',
                              '>='        => 'T_IS_GREATER_OR_EQUAL',
                              '!'         => 'T_BOOLEAN_NOT',
                              '!='        => 'T_IS_NOT_EQUAL',
                              '!=='       => 'T_IS_NOT_IDENTICAL',
                              '='         => 'T_EQUAL',
                              '=='        => 'T_IS_EQUAL',
                              '==='       => 'T_IS_IDENTICAL',
                              '-='        => 'T_MINUS_EQUAL',
                              '+='        => 'T_PLUS_EQUAL',
                              '++'        => 'T_INC',
                              '--'        => 'T_DEC',
                              '//'        => 'T_COMMENT',
                              '/*'        => 'T_COMMENT',
                              '/**'       => 'T_DOC_COMMENT',
                              '*/'        => 'T_COMMENT',
                             );

    /**
     * A list string delimiters.
     *
     * @var array
     */
    protected $stringTokens = array(
                               '\'',
                               '"',
                              );

    /**
     * A list tokens that start and end comments.
     *
     * @var array
     */
    protected $commentTokens = array(
                                '//'  => null,
                                '/*'  => '*/',
                                '/**' => '*/',
                               );


    /**
     * Creates an array of tokens when given some PHP code.
     *
     * Starts by using token_get_all() but does a lot of extra processing
     * to insert information about the context of the token.
     *
     * @param string $string  The string to tokenize.
     * @param string $eolChar The EOL character to use for splitting strings.
     *
     * @return array
     */
    public function tokenizeString($string, $eolChar='\n')
    {
        $tokenTypes = array_keys($this->tokenValues);

        $maxTokenLength = 0;
        foreach ($tokenTypes as $token) {
            if (strlen($token) > $maxTokenLength) {
                $maxTokenLength = strlen($token);
            }
        }

        $tokens      = array();
        $inString    = '';
        $inComment   = '';
        $buffer      = '';
        $cleanBuffer = false;

        $tokens[] = array(
                     'code'    => T_OPEN_TAG,
                     'type'    => 'T_OPEN_TAG',
                     'content' => '',
                    );

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** START TOKENIZING ***".PHP_EOL;
        }

        $chars = str_split($string);
        foreach ($chars as $i => $char) {
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                $content = str_replace($eolChar, '\n', $char);
                if ($inString !== '') {
                    echo "\t";
                }

                if ($inComment !== '') {
                    echo "\t";
                }

                echo "Process char $i => $content".PHP_EOL;
            }

            if ($inString === '' && $inComment === '' && $buffer !== '') {
                // If the buffer only has whitespace and we are about to
                // add a character, store the whitespace first.
                if (trim($char) !== '' && trim($buffer) === '') {
                    $tokens[] = array(
                                 'code'    => T_WHITESPACE,
                                 'type'    => 'T_WHITESPACE',
                                 'content' => $buffer,
                                );

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $content = str_replace($eolChar, '\n', $buffer);
                        echo "=> Added token T_WHITESPACE ($content)".PHP_EOL;
                    }

                    $buffer = '';
                }

                // If the buffer is not whitespace and we are about to
                // add a whitespace character, store the content first.
                if ($inString === '' && $inComment === '' && trim($char) === '' && trim($buffer) !== '') {
                    $tokens[] = array(
                                 'code'    => T_STRING,
                                 'type'    => 'T_STRING',
                                 'content' => $buffer,
                                );

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $content = str_replace($eolChar, '\n', $buffer);
                        echo "=> Added token T_STRING ($content)".PHP_EOL;
                    }

                    $buffer = '';
                }
            }//end if

            // Process strings.
            if ($inComment === '' && in_array($char, $this->stringTokens) === true) {
                if ($inString === $char) {
                    // This could be the end of the string, but make sure it
                    // is not escaped first.
                    $escapes = 0;
                    for ($x = ($i - 1); $x >= 0; $x--) {
                        if ($chars[$x] !== '\\') {
                            break;
                        }

                        $escapes++;
                    }

                    if ($escapes === 0 || ($escapes % 2) === 0) {
                        // There is an even number escape chars,
                        // so this is not escaped, it is the end of the string.
                        $tokens[] = array(
                                     'code'    => T_CONSTANT_ENCAPSED_STRING,
                                     'type'    => 'T_CONSTANT_ENCAPSED_STRING',
                                     'content' => $buffer.$char,
                                    );

                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo "\t* found end of string *".PHP_EOL;
                            $content = str_replace($eolChar, '\n', $buffer.$char);
                            echo "=> Added token T_CONSTANT_ENCAPSED_STRING $content)".PHP_EOL;
                        }

                        $buffer   = '';
                        $inString = '';
                        continue;
                    }
                } else if ($inString === '') {
                    $inString = $char;

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo "\t* looking for string closer *".PHP_EOL;
                    }
                }//end if
            }//end if

            $buffer .= $char;

            // We don't look for special tokens inside strings,
            // so if we are in a string, we can continue here now
            // that the current char is in the buffer.
            if ($inString !== '') {
                continue;
            }

            // Check for known tokens, but ignore tokens found that are not at
            // the end of a string, like FOR and this.FORmat.
            if (in_array(strtolower($buffer), $tokenTypes) === true && (preg_match('|[a-zA-z0-9_]|', $char) === 0 || preg_match('|[a-zA-z0-9_]|', $chars[($i + 1)]) === 0)) {
                $matchedToken    = false;
                $lookAheadLength = ($maxTokenLength - strlen($buffer));

                if ($lookAheadLength > 0) {
                    // The buffer contains a token type, but we need
                    // to look ahead at the next chars to see if this is
                    // actually part of a larger token. For example,
                    // FOR and FOREACH.
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo "\t* buffer possibly contains token, looking ahead $lookAheadLength chars *".PHP_EOL;
                    }

                    $charBuffer = $buffer;
                    for ($x = 1; $x <= $lookAheadLength; $x++) {
                        if (isset($chars[($i + $x)]) === false) {
                            break;
                        }

                        $charBuffer .= $chars[($i + $x)];

                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $content = str_replace($eolChar, '\n', $charBuffer);
                            echo "\t=> Looking ahead $x chars => $content".PHP_EOL;
                        }

                        if (in_array(strtolower($charBuffer), $tokenTypes) === true) {
                            // We've found something larger that matches
                            // so we can ignore this char.
                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                $type = $this->tokenValues[strtolower($charBuffer)];
                                echo "\t* look ahead found more specific token ($type), ignoring $i *".PHP_EOL;
                            }

                            $matchedToken = true;
                            break;
                        }
                    }//end for
                }//end if

                if ($matchedToken === false) {
                    $value    = $this->tokenValues[strtolower($buffer)];
                    $tokens[] = array(
                                 'code'    => constant($value),
                                 'type'    => $value,
                                 'content' => $buffer,
                                );

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        if ($lookAheadLength > 0) {
                            echo "\t* look ahead found nothing *".PHP_EOL;
                        }

                        $content = str_replace($eolChar, '\n', $buffer);
                        echo "=> Added token $value ($content)".PHP_EOL;
                    }

                    $cleanBuffer = true;
                }
            } else if (in_array(strtolower($char), $tokenTypes) === true) {
                // No matter what token we end up using, we don't
                // need the content in the buffer any more because we have
                // found a valid token.
                $tokens[] = array(
                             'code'    => T_STRING,
                             'type'    => 'T_STRING',
                             'content' => substr($buffer, 0, -1),
                            );

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    $content = str_replace($eolChar, '\n', substr($buffer, 0, -1));
                    echo "=> Added token T_STRING ($content)".PHP_EOL;
                }

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo "\t* char is token, looking ahead ".($maxTokenLength - 1).' chars *'.PHP_EOL;
                }

                // The char is a token type, but we need to look ahead at the
                // next chars to see if this is actually part of a larger token.
                // For example, = and ===.
                $charBuffer   = $char;
                $matchedToken = false;
                for ($x = 1; $x <= $maxTokenLength; $x++) {
                    if (isset($chars[($i + $x)]) === false) {
                        break;
                    }

                    $charBuffer .= $chars[($i + $x)];

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $content = str_replace($eolChar, '\n', $charBuffer);
                        echo "\t=> Looking ahead $x chars => $content".PHP_EOL;
                    }

                    if (in_array(strtolower($charBuffer), $tokenTypes) === true) {
                        // We've found something larger that matches
                        // so we can ignore this char.
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $type = $this->tokenValues[strtolower($charBuffer)];
                            echo "\t* look ahead found more specific token ($type), ignoring $i *".PHP_EOL;
                        }

                        $matchedToken = true;
                        break;
                    }
                }//end for

                if ($matchedToken === false) {
                    $value    = $this->tokenValues[strtolower($char)];
                    $tokens[] = array(
                                 'code'    => constant($value),
                                 'type'    => $value,
                                 'content' => $char,
                                );

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo "\t* look ahead found nothing *".PHP_EOL;
                        $content = str_replace($eolChar, '\n', $char);
                        echo "=> Added token $value ($content)".PHP_EOL;
                    }

                    $cleanBuffer = true;
                } else {
                    $buffer = $char;
                }
            }//end if

            // Keep track of content inside comments.
            if ($inComment === '' && array_key_exists($buffer, $this->commentTokens) === true) {
                // We have started a comment.
                $inComment = $buffer;

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo "\t* looking for end of comment *".PHP_EOL;
                }
            } else if ($inComment !== '') {
                if ($this->commentTokens[$inComment] === null) {
                    // Comment ends at the next newline.
                    if (strpos($buffer, $eolChar) !== false) {
                        $inComment = '';
                    }
                } else {
                    if ($this->commentTokens[$inComment] === $buffer) {
                        $inComment = '';
                    }
                }

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    if ($inComment === '') {
                        echo "\t* found end of comment *".PHP_EOL;
                    }
                }
            }//end if

            if ($cleanBuffer === true) {
                $buffer      = '';
                $cleanBuffer = false;
            }
        }//end foreach

        $tokens[] = array(
                     'code'    => T_CLOSE_TAG,
                     'type'    => 'T_CLOSE_TAG',
                     'content' => '',
                    );

        /*
            Now that we have done some basic tokenizing, we need to
            modify the tokens to join some together and split some apart
            so they match what the PHP tokenizer does.
        */

        $finalTokens = array();
        $newStackPtr = 0;
        $numTokens   = count($tokens);
        for ($stackPtr = 0; $stackPtr < $numTokens; $stackPtr++) {
            $token = $tokens[$stackPtr];

            /*
                Look for regular expressions and join the tokens together.
            */

            if ($token['code'] === T_DIVIDE) {
                $beforeTokens = array(
                                 T_EQUAL,
                                 T_OPEN_PARENTHESIS,
                                );

                $afterTokens = array(
                                 T_COMMA,
                                 T_CLOSE_PARENTHESIS,
                                 T_SEMICOLON,
                                 T_WHITESPACE,
                                );

                for ($prev = ($stackPtr - 1); $prev >= 0; $prev--) {
                    if (in_array($tokens[$prev]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === false) {
                        break;
                    }
                }

                // Token needs to be one of the standard allowed or the replace()
                // method that can be called on string: string.replace(/abc/...).
                if (in_array($tokens[$prev]['code'], $beforeTokens) === true || $tokens[$prev]['content'] === 'replace') {
                    // This might be a regular expression.
                    $regexTokens = array(
                                    T_STRING,
                                    T_WHITESPACE,
                                    T_OBJECT_OPERATOR,
                                   );

                    for ($next = ($stackPtr + 1); $next < $numTokens; $next++) {
                        if (in_array($tokens[$next]['code'], $regexTokens) === false) {
                            break;
                        }
                    }

                    if ($tokens[$next]['code'] === T_DIVIDE) {
                        if ($tokens[($next + 1)]['code'] === T_STRING) {
                            // The token directly after the end of the regex can
                            // be modifiers like global and case insensitive
                            // (.e.g, /pattern/gi).
                            $next++;
                        }

                        $regexEnd = $next;

                        for ($next = ($next + 1); $next < $numTokens; $next++) {
                            if (in_array($tokens[$next]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === false) {
                                break;
                            } else if (strpos($tokens[$next]['content'], $eolChar) !== false) {
                                // If this is the last token on the line.
                                break;
                            }
                        }

                        if (in_array($tokens[$next]['code'], $afterTokens) === true) {
                            // This is a regular expression, so join all the
                            // tokens together.
                            for ($i = ($stackPtr + 1); $i <= $regexEnd; $i++) {
                                $token['content'] .= $tokens[$i]['content'];
                            }

                            $token['code'] = T_REGULAR_EXPRESSION;
                            $token['type'] ='T_REGULAR_EXPRESSION';
                            $stackPtr      = $regexEnd;
                        }
                    }
                }//end if
            }//end if

            /*
                Look for comments and join the tokens together.
            */

            if (array_key_exists($token['content'], $this->commentTokens) === true) {
                $newContent   = '';
                $tokenContent = $token['content'];
                $endContent   = $this->commentTokens[$tokenContent];
                while ($tokenContent !== $endContent) {
                    if ($endContent === null && strpos($tokenContent, $eolChar) !== false) {
                        // A null end token means the comment ends at the end of
                        // the line so we look for newlines and split the token.
                        $tokens[$stackPtr]['content'] = substr($tokenContent, (strpos($tokenContent, $eolChar) + strlen($eolChar)));

                        $tokenContent = substr($tokenContent, 0, (strpos($tokenContent, $eolChar) + strlen($eolChar)));

                        // If the substr failed, skip the token as the content
                        // will now be blank.
                        if ($tokens[$stackPtr]['content'] !== false) {
                            $stackPtr--;
                        }

                        break;
                    }//end if

                    $stackPtr++;
                    $newContent  .= $tokenContent;
                    if (isset($tokens[$stackPtr]) === false) {
                        break;
                    }

                    $tokenContent = $tokens[$stackPtr]['content'];
                }//end while

                // Save the new content in the current token so
                // the code below can chop it up on newlines.
                $token['content'] = $newContent.$tokenContent;
            }//end if

            /*
                If this token has newlines in its content, split each line up
                and create a new token for each line. We do this so it's easier
                to asertain where errors occur on a line.
                Note that $token[1] is the token's content.
            */

            if (strpos($token['content'], $eolChar) !== false) {
                $tokenLines = explode($eolChar, $token['content']);
                $numLines   = count($tokenLines);

                for ($i = 0; $i < $numLines; $i++) {
                    $newToken['content'] = $tokenLines[$i];
                    if ($i === ($numLines - 1)) {
                        if ($tokenLines[$i] === '') {
                            break;
                        }
                    } else {
                        $newToken['content'] .= $eolChar;
                    }

                    $newToken['type']          = $token['type'];
                    $newToken['code']          = $token['code'];
                    $finalTokens[$newStackPtr] = $newToken;
                    $newStackPtr++;
                }
            } else {
                $finalTokens[$newStackPtr] = $token;
                $newStackPtr++;
            }//end if

            // Convert numbers, including decimals.
            if ($token['code'] === T_STRING || $token['code'] === T_OBJECT_OPERATOR) {
                $newContent  = '';
                $oldStackPtr = $stackPtr;
                while (preg_match('|^[0-9\.]+$|', $tokens[$stackPtr]['content']) !== 0) {
                    $newContent .= $tokens[$stackPtr]['content'];
                    $stackPtr++;
                }

                if ($newContent !== '' && $newContent !== '.') {
                    $finalTokens[($newStackPtr - 1)]['content'] = $newContent;
                    if (ctype_digit($newContent) === true) {
                        $finalTokens[($newStackPtr - 1)]['code'] = constant('T_LNUMBER');
                        $finalTokens[($newStackPtr - 1)]['type'] = 'T_LNUMBER';
                    } else {
                        $finalTokens[($newStackPtr - 1)]['code'] = constant('T_DNUMBER');
                        $finalTokens[($newStackPtr - 1)]['type'] = 'T_DNUMBER';
                    }

                    $stackPtr--;
                } else {
                    $stackPtr = $oldStackPtr;
                }
            }//end if
        }//end for

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** END TOKENIZING ***".PHP_EOL;
        }

        return $finalTokens;

    }//end tokenizeString()


}//end class

?>
