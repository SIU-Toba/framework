    /******************************************************************
     highlight.js                                           Muze Helene
     ------------------------------------------------------------------
     Author: Muze (info@muze.nl)
     Date: 28 februari 2004

     Copyright 2002 Muze

     This file is part of Helene.

     Helene is free software; you can redistribute it and/or modify
     it under the terms of the GNU General Public License as published 
     by the Free Software Foundation; either version 2 of the License, 
     or (at your option) any later version.
 
     Helene is distributed in the hope that it will be useful,
     but WITHOUT ANY WARRANTY; without even the implied warranty of
     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     GNU General Public License for more details.

     You should have received a copy of the GNU General Public License
     along with Helene; if not, write to the Free Software 
     Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  
     02111-1307  USA

    -------------------------------------------------------------------

     This file contains the syntax highlighting parser

    ******************************************************************/
  // Parsing smarty tags http://smarty.php.net/
  // Cofigure the smarty delimiters below ('{%' and '%}' by default)
  // Unset this flag if you need to parse only PHP
  var parseSmarty = true;

	/* states */
	var YY_STATE_HTML = 0;
	var YY_STATE_PINP = 1;
	var YY_STATE_DQSTRING = 2;
	var YY_STATE_SQSTRING = 3;
  var YY_STATE_SCRIPT = 4;
  var YY_STATE_BLOCKCOMMENT = 5;

  /* Smarty states */
  var YYS_STATE_TAG = 101;
  var YYS_STATE_PARAM = 102;
  var YYS_STATE_QPARAM = 103;

	/* tokens */
	var T_VAR = 0;
	var T_IDENT = 1;
	var T_FUNCTION = 2;
	var T_TOKEN = 3;
	var T_UNKNOWN = 4;
	var T_PINP_START = 5;
	var T_PINP_BLOCK = 6
	var T_PINP_END = 7;
	var T_SPACE = 8;
	var T_DQUOTE = 9
	var T_SQUOTE = 10;
	var T_ESCAPE = 11;
	var T_SPECIAL_CHAR = 12;
	var T_OPERATOR = 13;
	var T_SINGLE_COMMENTS = 14;
	var T_BLOCKCOMMENT = 15;
	var T_BLOCKCOMMENT_END = 16;
	var T_PHP_START = 17;
	var T_PHP_END = 18;
	var T_SCRIPT_START = 19;
	var T_SCRIPT_END = 20;
	var T_TAB = 21;

  /*Smarty tokens*/
  var TS_SMARTY_START = 101;
  var TS_SMARTY_END = 102;
  var TS_KEYWORD = 103;
  var TS_ATTRIBUTE = 104;
  var TS_ATTRVALUE = 105;
  var TS_VAR = 106;
	var TS_ENDTAG = 107;

	var hLines = new Array();
	var debug = 0;
	var scannerPos = 0;
  var hStyles = new Array();
	var hStateStyles = new Array();

  // May be there is the better place for it but now there
  initStyleDefault();
	// Making object from keywords, it makes a big perfomance overhead in IE
	// and slightly better perfomance in Mozilla
	// You can define any amount of keyword groups
	// Words should match ([a-z0-9][a-z0-9_]*) and should be separated by spaces
	var hPHPKeywords = cacheKeywords( /// TODO: Add more keywords here 
		"if foreach else elseif function class new",	// language constructs
		"echo print count array" // standart functions
		);	
	var hSmartyKeywords = cacheKeywords(
		"if foreach capture section", // Block keywords
		"include assign math counter else foreachelse sectionelse cycle elseif", // Other keywords
		"getprices getarticles" // Own plugins for smarty 
		); 

	function hLineToken(tokenType, tokenData) {
		this.type = tokenType;
		this.data = tokenData;
		this.reallength = tokenData.length;
    this.newState = -1;

		switch (this.data) {
			case '<':	
				this.data='&lt;';
			break;
			case '>':
				this.data='&gt;';
			break;
			case '&':
				this.data='&amp;';
			break;
		}
		switch (this.type) {
			case T_PINP_START:
			case T_PHP_START:
			case T_PINP_END:
			case T_PHP_END:
			case T_SCRIPT_START:
			case T_SCRIPT_END:
				this.data = this.data.replace(/[<]/g, '&lt;');
				this.data = this.data.replace(/[>]/g, '&gt;');
			case T_SPACE:
				this.data = this.data.replace(/[ ]/g, '&nbsp;');
			break;
			case T_TAB:
				var myTabRest = scannerPos % 8;
				var addLength = 8 - myTabRest;
				// alert( 'scannerPos ' + scannerPos + ' myTabRest ' + myTabRest + ' addLength ' + addLength);
				this.data = this.data.replace(/[\t]/g, '<span style="padding-left: '+ (addLength*letterWidth) + 'px;"></span>');
				scannerPos += (addLength -1);
			break;
		}
		scannerPos += tokenData.length;
	}

	function getToken(sData) {
		var re, match;

		/* white space */
		re = /^([ ]+)/;
		match = re.exec(sData);
		if (match) {
			result = new hLineToken(T_SPACE, match[1]);
			return result;
		}

		re = /^([\t])/;
		match = re.exec(sData);
		if (match) {
			result = new hLineToken(T_TAB, match[1]);
			return result;
		}

    /* Smarty tokens */
    if(parseSmarty) {
      re = /^\{%/;           // Matches $smarty->left_delimiter
      match = re.exec(sData);
      if(match) {
        return new hLineToken(TS_SMARTY_START, match[0]);
      }

      re = /^%\}/;           // Matches $smarty->right_delimiter
      match = re.exec(sData);
      if(match) {
        return new hLineToken(TS_SMARTY_END, match[0]);
      }
			re = /^\/[a-z0-9][a-z0-9_]*/i;
			match = re.exec(sData);
			if (match) {
				result = new hLineToken(TS_ENDTAG, match[0]);
				return result;
			}		
    }
    /* end of smarty tokens */

		/* variable or ident */
		re = /^([$]|->)?([a-z0-9][a-z0-9_]*)/i;
		match = re.exec(sData);
		if (match) {
			if (match[1]) {
				result = new hLineToken(T_VAR, match[0]);
			} else {
				result = new hLineToken(T_IDENT, match[2]);
			}
			return result;
		}

		/* single tokens */
		re = /^([(){},"'\\])/;
		match = re.exec(sData);
		if (match) {
			switch (match[1]) {
				case '\\':
					result = new hLineToken(T_ESCAPE, match[1]); 
				break;
				case '"':
					result = new hLineToken(T_DQUOTE, match[1]); 
				break;
				case "'":
					result = new hLineToken(T_SQUOTE, match[1]);
				break;
				default:
					result = new hLineToken(T_SPECIAL_CHAR, match[1]);
				break;
			}
			return result;
		}

		re = /^((\/[*])|([*]\/))/;
		match = re.exec(sData);
		if (match) {
			if (match[2]) {
				result = new hLineToken(T_BLOCKCOMMENT, match[2]);
			} else {
				result = new hLineToken(T_BLOCKCOMMENT_END, match[3]);
			}
			return result;
		}

		/* comments */
		re = /^(\/\/.*)/;
		match = re.exec(sData);
		if (match) {
			result = new hLineToken(T_SINGLE_COMMENTS, match[1]);
			return result;
		}

		/* php end tags */
		re = /^([\?\%]>)/;
		match = re.exec(sData);
		if (match) {
			result = new hLineToken(T_PHP_END, match[0]);
			return result;
		}

		re = /^([\-\+\.\*\/\=\%])/;
		match = re.exec(sData);
		if (match) {
			result = new hLineToken(T_OPERATOR, match[1]);
			return result;
		}


		/* pinp/php tags */
              re = /^((<(\/)?pinp>)|(<[%?]php|<\?|<script[^>]+language[^>]*=[^>]*php[^>]*>))/i;
		match = re.exec(sData);
		if (match) {
			if (match[3]) {
				result = new hLineToken(T_PINP_END, match[0]);
			} else
			if (match[2]) {
				result = new hLineToken(T_PINP_START, match[0]);
			} else {
				result = new hLineToken(T_PINP_START, match[0]);
			}
			return result;
		}

		/* javascript */
		re = /^<(\/)?script[^>]*>/;
		match = re.exec(sData);
		if (match) {
			if (match[1]) {
				result = new hLineToken(T_SCRIPT_END, match[0]);
			} else {
				result = new hLineToken(T_SCRIPT_START, match[0]);
			}
			return result;
		}
				
		return new hLineToken(T_UNKNOWN, sData.charAt(0));
	}

	function hLineParseString(sData) {
		var token;
		this.tokens = new Array();

		scannerPos = 0;
		while (sData != '') {
			token = getToken(sData);
			this.tokens[this.tokens.length] = token;	
			sData=sData.substring(token.reallength);
		}
	}

      function getElmSpan(token) { /// In the past styles were here
		var result = '';							// UPDATE: it is better to produce whole span tag here
    var cls = hStyles[token.type];
    if(cls!='') result = '<span class="'+cls+'">';
		else result = '<span>';
		return result;
	}
      function getStateSpan(token) { /// Style span for states
		var result = '';								// UPDATE: it is better to produce whole span tag here
    var cls = hStateStyles[token.newState];
    if(cls!='') result = '<span class="'+cls+'">';
		else result = '<span>';
		return result;
	}

	function hLineDoHighlight(callback) {
		var state = new Array();
		var result = '';
		if (this.lineNo) {
			/* load parent state */
			state = state.concat(hLines[this.lineNo-1].getEndState());
//			alert((this.lineNo-1)+':'+state.length);
		}
		for (var i = 0; i<state.length; i++) {
			if (!state[i].noMultiLine) {
				result += getStateSpan(state[i]);
			}
		}
		if (this.tokens) {
			for (var i=0; i<this.tokens.length; i++) {
				var cState = 0;
				var token = this.tokens[i];
				if (state.length) {
                                      cState = state[state.length-1].newState; 
          status = cState;
				}

                              switch (cState) {         // In the past state was the type of opening token
                                      case YY_STATE_HTML:     // It is a real state now
						switch (token.type) {
              //Smarty highlighting
              case TS_SMARTY_START:
                token.newState = YYS_STATE_TAG;
                result += getStateSpan(token) + getElmSpan(token) + token.data + '</span>';
                state.push(token);
              break;
              //End of smarty highlighting
							case T_PHP_START:
							case T_PINP_START:
								token.newState = YY_STATE_PINP; // We fix the new state here
								if (i == 1 && this.tokens[i-1].type == T_SPACE) {
									result = getStateSpan(token) + result + getElmSpan(token) + token.data + '</span>';
								} else {
									result += getStateSpan(token) + getElmSpan(token) + token.data + '</span>';
								}
								state.push(token); // But we still saving opening token
							break;
							case T_SCRIPT_START:
                token.newState = YY_STATE_SCRIPT;
								if (i == 1 && this.tokens[i-1].type == T_SPACE) {
									result = getStateSpan(token) + result + getElmSpan(token) + token.data + '</span>';
								} else {
									result += getStateSpan(token) + getElmSpan(token) + token.data + '</span>';
								}
								state.push(token);
							break;
							default:
								result += token.data;
							break;
						}
					break;
                                      case YY_STATE_SCRIPT:
						switch (token.type) {
							case T_PHP_START:
							case T_PINP_START:
                token.newState = YY_STATE_PINP;
								if (i == 1 && this.tokens[i-1].type == T_SPACE) {
									result = getStateSpan(token) + result + getElmSpan(token) + token.data + '</span>';
								} else {
									result += getStateSpan(token) + getElmSpan(token) + token.data + '</span>';
								}
								state.push(token);
							break;
							case T_SCRIPT_END:
								result += getElmSpan(token)+token.data+'</span>';
								result += '</span>';
								state.pop();
							break;
							default:
								result += token.data;
							break;
						}
					break;
                                      case YY_STATE_PINP:
						switch (token.type) {
							case T_IDENT:
								var t = hPHPKeywords[token.data];
								if(typeof(t)!='undefined') {
									result += '<span class="h_phpkeywords'+t+'">';
								} else {
									result += getElmSpan(token);
								}
								result += token.data + '</span>';
							break;
							case T_DQUOTE:
                token.newState = YY_STATE_DQSTRING;
							case T_SQUOTE:
                if(token.newState<0) token.newState = YY_STATE_SQSTRING;
							case T_BLOCKCOMMENT:
                if(token.newState<0) token.newState = YY_STATE_BLOCKCOMMENT;
								result += getStateSpan(token);
								result += token.data;
								state.push(token);
							break;
							case T_PHP_END:
							case T_PINP_END:
								result += getElmSpan(token)+token.data+'</span>';
								result += '</span>';
								state.pop();
							break;
							case T_VAR:
							case T_OPERATOR:
							case T_SPECIAL_CHAR:
							case T_SINGLE_COMMENTS:
								result += getElmSpan(token);
								result += token.data;
								result += '</span>';
							break;
							default:
								result += token.data;
							break;
						}
					break;
                                      case YY_STATE_BLOCKCOMMENT:
						switch (token.type) {
							case T_BLOCKCOMMENT_END:
								result += token.data+'</span>';
								state.pop();
							break;
							default:
								result += token.data;
							break;
						}
					break;
                                      case YY_STATE_DQSTRING:
						switch (token.type) {
                                                      case T_DQUOTE:
								result += token.data+'</span>';
								state.pop();
							break;
							case T_ESCAPE:
								result += token.data;
								token = this.tokens[++i];
								result += token.data;
							break;
							case T_VAR:
								result += getElmSpan(token);
								result += token.data;
								result += '</span>';
							break;
							default:
								result += token.data;
							break;
						}
					break;
                                      case YY_STATE_SQSTRING:
						switch (token.type) {
                                                      case T_SQUOTE:
								result += token.data+'</span>';
								state.pop();
							break;
							case T_ESCAPE:
								result += token.data;
								token = this.tokens[++i];
								result += token.data;
							break;
							default:
								result += token.data;
							break;
						}
					break;
          case YYS_STATE_TAG:
            switch (token.type) {
              case TS_SMARTY_END:
                result += getElmSpan(token) + token.data + '</span></span>';
                state.pop();
              break;
							case TS_ENDTAG:
								var t = hSmartyKeywords[token.data.substr(1)];
								if(t==1) {	// Only the first group has closing tags
									result += '<span class="h_smartykeywords'+t+'">' + token.data + '</span>';
								} else {
									result += token.data;
								}
							break;
							case T_VAR:
                result += getElmSpan(token) + token.data + '</span>';
								return;								
							case T_IDENT:
								var t = hSmartyKeywords[token.data];
								if(typeof(t)!='undefined' && 
									(t>3 || 																						// first three groups of keywords may appear
										(i>0 && this.tokens[i-1].type==TS_SMARTY_START))	// immediately after TS_SMARTY_START token only
										) {
									result += '<span class="h_smartykeywords'+t+'">' + token.data + '</span>';
								} else {
									result += token.data;
								}
							break;
              default:
							result += token.data;
              break;
            }
          break;
					default:
						result += token.data;
					break;
				}
			}
//			alert(this.lineNo+'::'+this.tokens.length+'::'+result);

		}
		var stateChanged = 0;
		if (state.length != this.getEndState().length) {
			stateChanged = 1;
		}

		for (i=state.length-1; i>=0; i--) {
			if (!stateChanged && state[i].type!=this.getEndState()[i].type) {
				stateChanged = 1;
			}
			if (!state[i].noMultiLine) {
				result += getElmSpan(state[i]);
			}
		}

		/* report update */
		if (callback) {
//			alert(this.lineNo+"::"+result);
			if (result) {
//				alert(this.lineNo+': 2 eol chars: "'+result.substr(result.length-2)+'"');
			}
			callback(this.lineNo, result);
		}
		this.setEndState(state);
              if (stateChanged) {
			if (debug) alert('updating: '+this.lineNo+1); 
      /// This makes a stack overflow
      /// so we are returning 'true' that means next line must be updated
      //~ hLines[this.lineNo+1].doHighlight(callback);
                      return true;
		}
    /// Next line need not be updated
    return false;
	}

	function hLineSetEndState(newEndState) {
//		alert(this.lineNo+': new endstate: '+newEndState.length);
		var frop=new Array();
		if (newEndState.length) {
//			alert(':'+newEndState.toString()+':');
			this.endState=frop.concat(newEndState); //newEndState; //.toSource();
		} else {
			this.endState=new Array();
		}
/*
		var line = hLines[2];
		if (line) {
			alert(this.lineNo+'->'+line.endState.length);
		}
*/
	}

	function hLineGetEndState() {
		return this.endState;
	}

	function hLineRemove() {
		if (this.lineNo < (hLines.length-1)) {
			var len = hLines.length-1;
			for (var i=this.lineNo; i<len; i++) {
				hLines[i] = hLines[i+1];
				hLines[i].lineNo = i;
			}
		}
		hLines.pop();
	}

	function hLine(lineNo, lineString) {
		this.lineNo = lineNo;
		if (lineNo && (lineNo != hLines.length)) {
			var hLinesLen = hLines.length;
			for (var i=hLinesLen; i>lineNo; i--) {
				hLines[i] = hLines[i-1];
				hLines[i].lineNo = i;
			}
		}
		hLines[lineNo] = this;
		this.tokens = new Array();
		this.setEndState = hLineSetEndState;
		this.setEndState(new Array());
		this.getEndState = hLineGetEndState;
		this.remove = hLineRemove;

		this.parseString = hLineParseString;
		if (lineString) {
			this.parseString(lineString);
		}
		if (debug) alert(this.lineNo);
		this.doHighlight = hLineDoHighlight;
	}

	function highlightUpdateLine(lineNo, lineContent, callback) {
//		alert('update line: '+lineNo+'::'+lineContent);
		hLines[lineNo].parseString(lineContent);
              while(hLines[lineNo].doHighlight(callback) && lineNo < hLines.length-1) lineNo++;
	}

	function highlightDeleteLine(lineNo, callback) {
//		alert('remove line: '+lineNo);
		line = hLines[lineNo];
		line.remove();
              if (hLines.length && (lineNo < hLines.length))
      while(hLines[lineNo].doHighlight(callback) && lineNo < hLines.length-1) lineNo++;
	}

	function highlightReset() {
		hLines = new Array();
		new hLine(0, '');
	}

	function highlightInsertLine(lineNo, lineContent, callback) {
		if (lineNo) {
			lineNo -= 1;
		}
//		alert('insert at: '+lineNo+'::'+lineContent);
		line = new hLine(lineNo, lineContent);
              while(line.doHighlight(callback) && lineNo < hLines.length-1) lineNo++;
	}

	function highlightAppendLine(lineNo, lineContent, callback) {
//		alert('append at: '+(lineNo+1)+'::'+lineContent);
		line = new hLine(lineNo+1, new String(lineContent));
              while(hLines[lineNo].doHighlight(callback) && lineNo < hLines.length-1) lineNo++;
	}

  function initStyleDefault() {
    hStyles[T_PINP_START] =
    hStyles[T_PINP_END] =
    hStyles[T_PHP_START] = 'h_pinp';
    hStyles[T_PHP_START] = 'h_script';
    hStyles[T_IDENT] = 'h_ident';
    hStyles[T_DQUOTE] = 'h_doublequote';
    hStyles[T_SQUOTE] = 'h_singlequote';
    hStyles[T_SPECIAL_CHAR] = 'h_special_char';
    hStyles[T_OPERATOR] = 'h_operator';
    hStyles[T_SINGLE_COMMENTS] = 'h_single_comments';
    hStyles[T_BLOCKCOMMENT] = 'h_blockcomment';
    hStyles[TS_SMARTY_START] =
    hStyles[TS_SMARTY_END] = 'h_smartymarkers';
		hStateStyles[YY_STATE_HTML] = '';
		hStateStyles[YY_STATE_PINP] = 'h_pinp_block';
		hStateStyles[YY_STATE_DQSTRING] = 'h_doublequote';
		hStateStyles[YY_STATE_SQSTRING] = 'h_singlequote';
		hStateStyles[YY_STATE_BLOCKCOMMENT] = 'h_blockcomment';
		hStateStyles[YY_STATE_SCRIPT] = 'h_scriptblock';
		hStateStyles[YYS_STATE_TAG] = 'h_smartytag';
  }
	function cacheKeywords() {
		var res = new Object();
		for(var i=0;i<arguments.length;i++) {
			var t = String(arguments[i]).split(" ");
			for(var j=0;j<t.length;j++)
				res[t[j]]=i+1;
		}
		return res;
	}
