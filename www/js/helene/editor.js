
   /******************************************************************
     editor.js                                              Muze Helene
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

     This file contains the basic editor functionality.

    ******************************************************************/
	var currentLine=1;
	var previousLine=1;
	var lastLine=0;
	var screenWidth=0;
	var lineHeight=16;
	var contentDiv;
	var lines;
	var inputLine;
	var inputLineEntry;
	var oldpos=-1;
	var isDirty=false;
	var winW = 630;
	var winH = 460;
	var inputLineHasFocus = false;
	var offsetLine = 0;
	var currInputHeight = lineHeight;
	var letterWidth = 9;
	var clickPosOffsetLeft = 0;
	var longestLine = 0;
	var adjustTop = 0;
	var minInputWidth = 0;
	var addedSpaces=0;
	var userSelecting = false;
	var keyBinding = new Array();
	var keyBindings = new Array();
	var cancelKey = false;
	var autoIndent = true;
	var startcoordinate = false;
	/* Undo/Redo vars */
	var undoBuffer = [];
	var redoBuffer = [];
	var startCursorPos = -1; // Cursor position before text alteration
	var saveUndo = true;
	var isMoz = false;
	var checkPastedReturns = false;

	/* keybindings */
	keyBindings["default"] = new Array();			
	keyBindings["default"]["default"] = do_Default;		// Default action for unrecognized keys
	keyBindings["default"]["40"] = do_Down;			// down
	keyBindings["default"]["s40"] = do_SelectDown; 	// shift-down
	keyBindings["default"]["38"] = do_Up;				// up
	keyBindings["default"]["s38"] = do_SelectUp;		// shift-up
	keyBindings["default"]["37"] = do_Left;			// left
	keyBindings["default"]["s37"] = do_SelectLeft;		// shift-left	
	keyBindings["default"]["39"] = do_Right;			// right
	keyBindings["default"]["s39"] = do_SelectRight;	// shift-right
	keyBindings["default"]["8"] = do_Backspace;		// backspace
	keyBindings["default"]["46"] = do_Delete;			// delete
	keyBindings["default"]["13"] = do_Linebreak;		// enter
	keyBindings["default"]["34"] = do_PageDown;		// page-down
	keyBindings["default"]["s34"] = do_SelectPageDown;	// shift-page-down
	keyBindings["default"]["33"] = do_PageUp;			// page-up
	keyBindings["default"]["s33"] = do_SelectPageUp;	// shift-page-up
	keyBindings["default"]["36"] = do_Home;			// home
	keyBindings["default"]["9"] = do_Tab;			// Tab
	keyBindings["default"]["c86"] = do_Paste;		//	ctrl-v
	keyBindings["default"]["c90"] = do_Undo;			// ctrl-z
	keyBindings["default"]["c89"] = do_Redo;			// ctrl-y
	keyBindings["default"]["cs90"] = do_Redo;			// ctrl-shift-z

	/* joe bindings */
	keyBindings["joe"] = new Array();			
	keyBindings["joe"]["c65"] = do_Home;				// ctrl-e
	keyBindings["joe"]["c69"] = do_End;				// ctrl-a
	keyBindings["joe"]["c88"] = do_skipCharsRight;		// ctrl-x
	keyBindings["joe"]["c90"] = do_skipCharsLeft;		// ctrl-z
	keyBindings["joe"]["c75"] = joe_Ctrlk;			// ctrl-k
	keyBindings["joe"]["cs189"] = do_Undo;			// ctrl shift -
	keyBindings["joe"]["cs54"] = do_Redo;			// ctrl shift 6
	keyBindings["joe"]["default"] = joe_Default;

	/* joe ctrl-k bindings */
	keyBindings["joe"]["ctrl-k"] = new Array();
	keyBindings["joe"]["ctrl-k"]["85"]	= joe_ScrollHome;	// ctrl-k u
	keyBindings["joe"]["ctrl-k"]["86"]	= joe_ScrollEnd;	// ctrl-k v
	keyBindings["joe"]["ctrl-k"]["default"] = joe_Default_Ctrlk;

	/* keybinding to use */
	keyBinding = keyBindings["default"];
	
	/* undo buffer constants */
	var undo = { CHANGE_TEXT: 1, LINE_AFTER: 2, DELETE_LINE: 3 };

	function init() {
		contentDiv=document.getElementById('content');	
		codeframeDiv=document.getElementById('codeframe');
		lines=document.getElementById('lines');
		lineHeight=lines.childNodes[0].offsetHeight;
		currInputHeight=lineHeight;
		lastLine=lines.childNodes.length;
		inputLine=document.getElementById('inputLine');
		inputLineEntry=document.getElementById('inputLineEntry');
		if (parseInt(navigator.appVersion)>3) {
			if (navigator.appName=="Netscape") {
				isMoz = true;
				winW = window.innerWidth-42;
				winH = window.innerHeight-2;
				minInputWidth = winW-16;
				document.onkeypress=moz_onKeyPress;
				document.onkeydown=keyDownHandler;
				// only mozilla has a keyUpHandler
				document.onkeyup = keyUpHandler;
				init_mozilla_compat();
				/* do some w3c dom magic to get the current font size */
				var vw=document.defaultView;
				var currStyle=vw.getComputedStyle(lines,"");
				var fontSize=currStyle.getPropertyValue("font-size");
				var fontFamily=currStyle.getPropertyValue("font-family");
			} else if (navigator.appName.indexOf("Microsoft")!=-1) {
				winW = document.body.offsetWidth;
				winH = document.body.offsetHeight;
				document.onkeydown=keyDownHandler;
				/* get current font size */
				var fontSize=lines.currentStyle.fontSize;
				var fontFamily=lines.currentStyle.fontFamily;
				clickPosOffsetLeft = 44;
				minInputWidth=winW-60;
				// To handle clipboard paste
				inputLineEntry.onpaste = pasteText;
			} else {
				document.onkeydown=keyDownHandler;
				/* do some w3c dom magic to get the current font size */
				var vw=document.defaultView;
				var currStyle=vw.getComputedStyle(lines,"");
				var fontSize=currStyle.getPropertyValue("font-size");
				var fontFamily=currStyle.getPropertyValue("font-family");
			}
		}
		document.getElementById('linenumbers').style.height=winH;
		updateLineNumbers();
		inputLine.style.width=minInputWidth;
		inputLine.style.height=lineHeight;
		inputLineEntry.onfocus=inputFocus;
		inputLineEntry.onblur=inputBlur;		
		inputLineEntry.style.fontSize=fontSize;
		inputLineEntry.style.fontFamily=fontFamily;
		inputLineEntry.style.height=lineHeight;
		inputLineEntry.style.overflow='hidden';
		// this is needed because MSIE sometimes shifts the textarea a bit...
		adjustTop=-inputLineEntry.offsetTop;
		/* calculate width, height of one letter, fix padding if needed */
		var templine=lines.childNodes[0].innerHTML;
		lines.childNodes[0].innerHTML='<span id="editorLetterWidth">m</span>';
		letterWidth=document.getElementById("editorLetterWidth").offsetWidth;
		spanHeight=document.getElementById("editorLetterWidth").offsetHeight;
		spanTopPadding=document.getElementById("editorLetterWidth").offsetTop;
		if (lineHeight>spanHeight) {
			// this is to make background colors span the entire height of the line
			spanBottomPadding=(lineHeight-spanHeight)-spanTopPadding;
		} else {
			spanBottomPadding=0;
		}
		if (spanTopPadding || spanBottomPadding) {
			// get the stylesheet and apply this padding to each span
			var editorStylesheet=document.styleSheets[0];
			if (navigator.appName.indexOf("Microsoft")!=-1) {		
				editorStylesheet.addRule("SPAN", "padding-top: "+spanTopPadding+"px; padding-bottom: "+spanBottomPadding+"px;", 0);
			} else {
				editorStylesheet.insertRule("SPAN { padding-top: "+spanTopPadding+"px; padding-bottom: "+spanBottomPadding+"px; }", 0);
			}
		}
		lines.childNodes[0].innerHTML=templine;
		document.onmousedown=mouseDownHandler;
		document.onmouseup=mouseUpHandler;
		var _temp='';
		for (var i=0; i<lines.childNodes.length; i++) {
			// need to remove trailing ' ', which IE puts there.
			_temp=getLineContent(i+1);
			if (navigator.appName.indexOf("Microsoft")!=-1) {
				_temp=_temp.substr(0, _temp.length-1);
			}
			if (_temp.length>longestLine) {
				longestLine=_temp.length;
			}
			currLine=new hLine(i, _temp);
			currLine.doHighlight(applyHighlightedLine);
		}
		if ((longestLine*letterWidth)>minInputWidth) {
			inputLine.style.width=longestLine*letterWidth+4;
		}

		redrawInputLine();
	}

	function pasteText() {
		var content = clipboardData.getData('text');
		var re = /^([^\n\r]*)(\r\n|\r|\n)/;
		var match;
		var cur = currentLine;
		if (document.selection) { // For IE only
			var tr = document.selection.createRange();
			tr.text = content;
			content = inputLineEntry.value;
		} else { // There is nothing to do in Mozilla
			return;
		}

		match = re.exec(content);
		if( match && match[1] != null ) {
			inputLineEntry.value = match[1];
			isDirty = true;
			checkDirty();
			updateLine(currentLine,match[1]);
			content = content.substr(match[0].length);
		} else {
			isDirty = true;
			checkDirty();
			updateLine(currentLine,content);
			content = false;
		}
		if(content != false) {
			while( content != false ) {
				var trans;
				match = re.exec(content);
				if( match && match[1] != null ) {
					trans = match[1];
					content = content.substr(match[0].length);
				} else {
					trans = content;
					content = false;
				}
				insertLineAfter(cur++,trans);
			}
		}
		event.returnValue=false;
	}

	function init_mozilla_compat() {
		HTMLElement.prototype.insertAdjacentHTML = function (sWhere, sText) {
			var r = document.createRange();
			safewhere = sWhere.toLowerCase();
			switch (safewhere) {
				case "beforebegin":
					r.setStartBefore(this);
					this.parentNode.insertBefore(r.createContextualFragment(sText), this);
					break;

				case "afterbegin":
					r.setStartBefore(this.firstChild);
					this.insertBefore(r.createContextualFragment(sText), this.firstChild);
					break;

				case "beforeend":
					r.setStartAfter(this.lastChild);
					this.appendChild(r.createContextualFragment(sText));
					break;

				case "afterend":
					r.setStartAfter(this);
					this.parentNode.insertBefore(r.createContextualFragment(sText),
						this.nextSibling);
					break;
			}
		}
		Node.prototype.removeNode = function( removeChildren ) {
			var self = this;
			if ( Boolean( removeChildren ) )
			{
				return this.parentNode.removeChild( self );
			}
			else
			{
				var range = document.createRange();
				range.selectNodeContents( self );
				return this.parentNode.replaceChild( range.extractContents(), self );
			}
		}
	}

	function inputFocus() {
		inputLineHasFocus=true;
	}

	function inputBlur() {
		inputLineHasFocus=false;
	}

	function applyHighlightedLine(lineNo, highlightedLine) {
		// the extra span is to correctly calculate the offset of the content
		// to position the input line
		setLineContent(lineNo+1, '<span><nobr>'+highlightedLine+'</nobr></span>');
	}

	function moveDown(amount) {
		//FIXME: start made with selections
		//  change moveUp as well
		//  and change textarea size back again in stopselect
		if (userSelecting) {
			if( (currentLine + offsetLine) < lastLine ) { // Don't select past the last line...
				currInputHeight+=lineHeight;
				inputLine.style.height=currInputHeight;
				inputLineEntry.style.height=currInputHeight;
				offsetLine++;
				inputLineEntry.value+='\n'+getLineContent(currentLine+offsetLine);
				return true;
			} else {
				return false;
			}
		} else {
			checkDirty();
			if (currentLine<lastLine) {
				previousLine = currentLine;
				currentLine+=amount;
				if (currentLine>lastLine) {
					currentLine=lastLine;
				}
				redrawInputLine();
			}
			return false;
		}
	}

	function moveUp(amount) {
		if( userSelecting) {
			if( offsetLine > 0 ) { // Don't select smaller than 1 line
				currInputHeight-=lineHeight;
				inputLine.style.height=currInputHeight;
				inputLineEntry.style.height=currInputHeight;
				offsetLine--;
				var myValue = inputLineEntry.value;
				myValue = myValue.replace(/(.*)\n(.*[\n]?)$/,"$1");
				inputLineEntry.value = myValue;
				return true;
			} else {
				return false;
			}
		} else {
			checkDirty();
			if (currentLine>1) {
				previousLine = currentLine;
				currentLine-=amount;
				if (currentLine<1) {
					currentLine=1;
				}
				redrawInputLine();
			}
			return false;
		}
	}

	function redrawInputLine(dontfocus) {
		var pos;
		if (!dontfocus) {
			var oldline;
			var counter;
			pos = getCursorPos();
			/*
			 * we are gona calculate the real old pos by counting tabs for 8
			 */
			oldpos = pos
			oldline = getLineContent(previousLine);
			counter = 0;
			while(counter < oldpos){
				if(oldline.charAt(counter) == "\t"){
					oldpos+=7;
				}
				counter++;
			}
		}
		// mozilla sometimes jumps the lines offset around, so we need to get the
		// offset of the content of the line, not the line itself 

		var mytop=lines.childNodes[currentLine-1].firstChild.offsetTop+adjustTop;
		inputLine.style.top=mytop;
		var value=getLineContent(currentLine);
		inputLineEntry.value=value;
		if (!dontfocus) {
			pos = oldpos;
			setCursorPos(pos);
//			I don't think this is needed anymore
//			inputLineEntry.focus(); // scroll into view pls..
		}
		startCursorPos = -1;
	}

	function getCursorPos() {
		// don't set the focus to the input line here, or the screen
		// will 'dance' around. Its not needed anyway.
		if (document.selection) {
			var mtext=document.selection.createRange();
			var count=0;
			var moved=0;
			while (moved=mtext.moveStart('character', -100)) {
				count-=moved;
			}
			chars=mtext.text.length;
			return chars;
		} else {
			var selection=window.getSelection();
			if (selection.anchorNode) {
				var len = inputLineEntry.selectionEnd;
				return len;
			}
		}
	}


	function setCursorPos(cursorstart, cursorend, clicked) {
		var counter;
		var contents=new String(inputLineEntry.value);
		if (cursorstart==-1) {
			cursorstart=contents.length;
		}

		counter = cursorstart;
		cursorstart = 0;
		while(counter > 0){
			if(contents.charAt(cursorstart) == "\t"){
				counter -= 7;
			}
			cursorstart++;
			counter--;
		}
		cursorstart = cursorstart;

		if (contents.length<cursorstart && !clicked) {
			addedSpaces=cursorstart-contents.length+1;
			for (i=0; i<addedSpaces; i++ ){
				contents+=' ';
			}
			inputLineEntry.value=contents;
		} else if (clicked) {
			if (cursorstart>contents.length) {
				cursorstart=contents.length;
			}
			addedSpaces=0;
		} else {
			addedSpaces=0;
		}
		if (document.selection) {
			var mtext=inputLineEntry.createTextRange();
			mtext.collapse(true);
			mtext.moveStart('character', cursorstart);
			mtext.collapse(true);
			if (cursorend) {
				mtext.moveEnd('character', (cursorend-cursorstart));
			}
			mtext.select();
		} else {
			// do something Mozilla style
			if (!cursorend) {
				cursorend=cursorstart;
			}
			// for mozilla, set the focus to scroll the input line into view
			inputLineEntry.focus();
			inputLineEntry.setSelectionRange(cursorstart, cursorend);
		
/*
			var selection=window.getSelection();
			if (selection.anchorNode) {
				inputLineEntry.selectionStart = cursorstart;
				if (cursorend) {
					inputLineEntry.selectionEnd = cursorend;
				} else {
					inputLineEntry.selectionEnd = cursorstart;
				}
			}
*/
		}

		// calculate cursor position in px
		if (cursorend) {
			var scrollToChar=cursorend;
		} else {
			var scrollToChar=cursorstart;
		}
		cursorpx=letterWidth*(scrollToChar+10);
		if (cursorpx>150) { //(winW-44)) {
//			don't do this, let IE and mozilla handle it themselves
//			document.body.scrollLeft=cursorpx+44;
		} else {
			document.body.scrollLeft=0;
		}
		return false;
	}

	function removeAddedSpaces() {
		if (addedSpaces) {
			var _temp=new String(inputLineEntry.value);
			_temp=_temp.substr(0, _temp.length-addedSpaces);
			inputLineEntry.value=_temp;
			addedSpaces=0;
		}
	}

	function getLineContent(lineNo) {
		var myString = new String();
		if(lines.childNodes[lineNo-1]){
			if( typeof(lines.childNodes[lineNo-1].originalInnerText)!="undefined" ) {
				return lines.childNodes[lineNo-1].originalInnerText;
			}
			if (lines.childNodes[lineNo-1].innerText) {
				return lines.childNodes[lineNo-1].innerText;
			} else if (document.createRange) {
				var html = document.createRange();
				var myLine = lineNo-1;
				while( lines.childNodes[myLine].tagName != 'LI' ) {
					myLine++;
				}
				html.selectNodeContents(lines.childNodes[myLine]);
				myString = html.toString();
				myString = myString.replace(/\n/g, '');
				return myString;
			}
		}
		return "";
	}

	function getContents() {
		var i=1;
		var myContent='';
		checkDirty();
		while( lines.childNodes[i-1] ) {
			myContent = myContent + getLineContent(i) + '\n';
			i++;
		}
		return myContent;
	}

	function setContents(content) {
		var myHead;
              var re = /^([^\n\r]*)(\r\n|\r|\n)/;
		var match;
		var originalArray = new Array();

		deleteContents();

		match = re.exec(content);
		if( match && match[1] != null ) {
			updateLine(1,match[1]);
			originalArray[lastLine] = match[1];
                      content = content.substr(match[0].length);
		} else {
			updateLine(1,content);
			originalArray[lastLine] = content;
			content = false;
		}
		if(content != false) {
			var htmlcontent = '';
			while( content != false ) {
				var trans;
				match = re.exec(content);
				if( match && match[1] != null ) {
					trans = match[1];
                                      content = content.substr(match[0].length);
				} else {
					trans = content;
					content = false;
				}
				lastLine++;
				originalArray[lastLine] = trans;
				trans = trans.replace(/[&]/g,"&amp;");
				trans = trans.replace(/[<]/g,"&lt;");
				trans = trans.replace(/[>]/g,"&gt;");
				trans = trans.replace(/[ ]/g,"&nbsp;");
				trans = trans.replace(/["]/g,"&quot;");
				htmlcontent += "<li>"+trans+"\n</li>";
			}
			if(htmlcontent){
				lines.childNodes[0].insertAdjacentHTML("AfterEnd",htmlcontent);
			}
		}
		var _temp='';
		for (var i=1; i<lines.childNodes.length; i++) {
			lines.childNodes[i].originalInnerText = originalArray[i+1];
			_temp=getLineContent(i+1);
			if (_temp.length>longestLine) {
				longestLine=_temp.length;
			}
			
			currLine=new hLine(i, _temp);
			currLine.doHighlight(applyHighlightedLine);
		}
		if ((longestLine*letterWidth)>minInputWidth) {
			inputLine.style.width=longestLine*letterWidth+4;
		}
		currentLine=1;
		updateLineNumbers();
		redrawInputLine();
		undoBuffer = [];
		redoBuffer = [];
	}

	function setKeyBinding(keybind) {
		if( keyBindings[keybind] ) {
			keyBinding = keyBindings[keybind];
		}
	}


	function setAutoIndent(indentvalue) {
		if( indentvalue == "on" ) {
			autoIndent = true;
		} else {
			autoIndent = false;
		}
	}

	function keyUpHandler() {

		// First we check for linebreaks that might have
		// Been pasted in.
		if( isMoz && checkPastedReturns ) {
			fixPastedReturns();
			checkPastedReturns = false;
        }
		
	}


	function fixPastedReturns() {
		var re = /^([^\n\r]*)(\r\n|\r|\n)/;
		var match;
		var content = inputLineEntry.value;
		var cur = currentLine;
			match = re.exec(content);
		if( match && match[1] != null ) {
			inputLineEntry.value = match[1];
			isDirty = true;
			checkDirty();
			updateLine(currentLine,match[1]);
			content = content.substr(match[0].length);
		} else {
			content = false;
		}
		while( content != false ) {
			var trans;
			match = re.exec(content);
			if( match && match[1] != null ) {
				trans = match[1];
				content = content.substr(match[0].length);
			} else {
				trans = content;
				content = false;
			}
			insertLineAfter(cur++,trans);
		}
	}


	function moz_onKeyPress(evt) {
		// Mozilla has a lot of trouble canceling events
		// an onkeydown event can not be cancelled
		// so we cancel the onkeypress...

		if( cancelKey ) {
			cancelKey = false;
			return false;
		} else {
			return true;
		}
	}
	
	function keyDownHandler(evt) {
		var charCode;
		var charString = '';
		var keyResult;
		
		evt = (evt) ? evt : ((event) ? event : null );
		if( evt ) {
			// Get the key pressed
			
			charCode = (evt.charCode ? evt.charCode : ((evt.keyCode) ? evt.keyCode : evt.which));

			// Create the encoded character string 

			charString = charCode;
			if( evt.shiftKey ) {
				charString = 's' + charString;
			}	
			if( evt.ctrlKey ) {
				charString = 'c' + charString;
			}			
			if( evt.altKey ) {
				charString = 'a' + charString;
			}

			window.status = charString;

			if( keyBinding[charString] ) {
				keyResult = keyBinding[charString]();
			} else {
				keyResult = keyBinding["default"](charString);
			}

			if( ! keyResult ) {
				cancelKey = true;
				if( evt.preventDefault ) {
					// This is the DOM way to cancel it but somehow
					// Mozilla refuses. Maybe another DOM browser will
					// make use of it :)
					evt.preventDefault();
				}
			}
			return keyResult;
		}
	}


	function joe_Ctrlk(charString) {
		keyBinding = keyBindings["joe"]["ctrl-k"];
		window.status = "ctrl-k ";
		return false;
	}

	function joe_Default_Ctrlk(charString) {
		keyBinding = keyBindings["joe"];
		window.status = "ctrl-k " + charString;
		return false;
	}

	function joe_Default(charString) {
		var keyBinding;
		keyBinding = keyBindings["default"];
		if( keyBinding[charString] ) {
			return keyBinding[charString]();
		} else {
			window.status = charString;
			return keyBinding["default"](charString);
		}
	}

	function joe_ScrollEnd() {
		keyBinding = keyBindings["joe"];
		window.status = "ctrl-k v";
		currentLine = lastLine;
		redrawInputLine();
		do_End();		
		return false;
	}
	
	function joe_ScrollHome() {
		keyBinding = keyBindings["joe"];
		window.status = "ctrl-k u";
		currentLine = 1;
		redrawInputLine();
		do_Home();
		return false;
	}

	function do_Default(charString) {
		removeAddedSpaces();
		oldpos=-1;
		isDirty=true;
		if(startCursorPos<0) startCursorPos = getCursorPos();
		return true;
	}
	
	function do_SelectUp() {
		// startSelect();
		return do_Up(true);
	}
	
	function do_SelectDown() {
		// startSelect();
		return do_Down(true);
	}
	
	function do_Down(selecting) {
		if( !selecting ) {
			stopSelect();
		}
		return moveDown(1);
	}
	
	function do_Up(selecting) {
		if( !selecting ) {
			stopSelect();
		}
		return moveUp(1);
	}
	
	function do_SelectLeft() {
		// startSelect();
		return do_Left(true);
	}

	function do_Left(selecting) {
		checkDirty();
		var result=true;
		if( !selecting ) {
			stopSelect();
		}
		if (addedSpaces) {
		removeAddedSpaces();
			result=false;
		}
		if (getCursorPos()==0) {
			if (currentLine>1) {
				moveUp(1);
				setCursorPos(-1);
				result=false;
			}
		}
		oldpos=-1;
		return result;
	}

	function do_SelectRight() {
		// startSelect();
		return do_Right(true);
	}

	function do_Right(selecting) {
		checkDirty();
		var result=true;
		if( !selecting ) {
			stopSelect();
		}
		if (currentLine<lastLine) {
			var _currline=getLineContent(currentLine);
			if (getCursorPos()>=_currline.length) {
				moveDown(1);
				setCursorPos(0);
				result=false;
			}
		}
		oldpos=-1;
		return result;
	}

	function do_Backspace() {
		var result=true;
		if (addedSpaces) {
			removeAddedSpaces();
			result=false;
		} else {
			oldpos=-1;
			if (getCursorPos()==0) {
				checkDirty();
				glueCurrentLine(-1);
				result=false;
			}
			isDirty=true;
		}
		return result;
	}

	function do_Delete() {
		var result=true;
		if (addedSpaces) {
			removeAddedSpaces();
			result=false;
		} else {
			oldpos=-1;
			if (currentLine<lastLine) {
				var _editline=inputLineEntry.value;
				if (getCursorPos()==_editline.length) {
					checkDirty();
					glueCurrentLine(1);
					result = false;
				}
			}
			isDirty=true;
		}
		return result;
	}

	function do_Linebreak() {
		removeAddedSpaces();
		oldpos=-1;
		breakCurrentLine();
		return false;
	}

	function do_SelectPageDown() {
		startSelect();
		return do_PageDown();
	}

	function do_PageDown() {
		moveDown(20);
		return false;
	}

	function do_SelectPageUp() {
		startSelect();
		return do_PageUp();
	}

	function do_PageUp() {
		moveUp(20);
		return false;
	}

	function do_Home() {
		removeAddedSpaces();
		setCursorPos(0);
		oldpos = -1;
		return false;
	}

	function do_End() {
		removeAddedSpaces();
		setCursorPos(inputLineEntry.value.length);
		oldpos = -1;
		return false;
	}

	function do_Tab() {
		var line = inputLineEntry.value;
		var cursor = getCursorPos();
		line = line.substr(0,cursor) + "\t" + line.substr(cursor);
		inputLineEntry.value = line;
		setCursorPos(cursor + 1 );
		isDirty=true;
		return false;
	}

	function do_skipCharsRight() {
		return skipChars(1);
	}

	function do_skipCharsLeft() {
		return skipChars(-1);
	}

	function do_Paste() {
		if( isMoz ) {
			checkPastedReturns = true;
		}
		return true;
	}
	
	function do_Undo() {
		checkDirty();
		if(undoBuffer.length<1) {
			return false;
		}
		var u = undoBuffer.pop();
		UndoIt(u,redoBuffer);
		return false;
	}
	
	function do_Redo() {
		if(redoBuffer.length<1) {
			return false;
		}
		var u = redoBuffer.pop();
		UndoIt(u,undoBuffer);
		return false;
	}
		
	function UndoIt(u,redoBuffer) {
		saveUndo = false;
		switch(u.type) {
			case undo.CHANGE_TEXT:
				updateLine(u.line,u.text);
				redoBuffer.push({ type: u.type, line: u.line, text: u.newText, newText: u.text, cursor: u.newCursor, newCursor: u.cursor });
			  break;
			case undo.LINE_AFTER:
				deleteLine(u.line);
				redoBuffer.push({ type: undo.DELETE_LINE, line: u.line, text: u.text, cursor: u.newCursor, newCursor: u.cursor });
			  break;
			case undo.DELETE_LINE:
				insertLineAfter(u.line-1,u.text);
				redoBuffer.push({ type: undo.LINE_AFTER, line: u.line, text: u.text, cursor: u.newCursor, newCursor: u.cursor });
			  break;
			default:
				saveUndo = true;
				return false;
		}		
		currentLine = u.line;
		inputLineEntry.value = getLineContent(currentLine);
		redrawInputLine();
		setCursorPos(u.cursor);
		saveUndo = true;
		return true;
	}
	
	function addUndo(ob) {
		undoBuffer.push(ob);
		redoBuffer = [];
	}

	function skipChars(dir) {
		var cursor, curstate, curchar;
		function is_letter (l) {
			if (l >= 'a' && l <= 'z' ||
				l >= 'A' && l <= 'Z') {
					return true;
			} else {
					return false;
			}
		}

		removeAddedSpaces();
		cursor = getCursorPos();
		if (dir < 0) {
			cursor--;
		}
		curstate = is_letter(inputLineEntry.value.charAt(cursor));
		if (!curstate) {
			/* forward over current state */
			while (curstate == is_letter(inputLineEntry.value.charAt(cursor+=dir))
				&& cursor > 0 && cursor < inputLineEntry.value.length);
		} else {
			cursor += dir;
		}

		curstate = is_letter(inputLineEntry.value.charAt(cursor));
		/* forward over next state */
		while (curstate == is_letter(inputLineEntry.value.charAt(cursor+=dir))
				&& cursor > 0 && cursor < inputLineEntry.value.length);

		if (dir < 0) {
			cursor++;
		}

		if (cursor < 0) {
			cursor = 0;
		} else if (cursor >= inputLineEntry.value.length) {
			cursor = inputLineEntry.value.length;
		}

		setCursorPos(cursor);
		oldpos = -1;
		return false;
	}


	function KeyUpHandler() {
	}


	function Coordinate(x, y) {
		this.x=x;
		this.y=y;
	}

	function mouseGetCoordinates(evt) {
		var target = (evt.target) ? evt.target : evt.srcElement;
		var clickX = -1;
		var clickY = -1;
		if( target && target.nodeName!='HTML' && target.nodeName!='BODY' ) { // HTML(moz) and BODY(IE) means clicked outside of our code..
			while (target && target.nodeName!='LI' && target.nodeName!='TEXTAREA' && target.nodeName!='DIV') {
				if( target.parentElement ) {
					target=target.parentElement;
				} else {
					// mozilla style
					target =  target.parentNode;
				}
			}
			if( target ) {
				// get click position
				if (evt.pageX) {
					var offsetX=evt.pageX - ((target.offsetLeft) ? target.offsetLeft : target.left);
					var offsetY=evt.pageY - ((target.offsetTop) ? target.offsetTop : target.top);				
				} else if (evt.offsetX || evt.offsetY) {
					var offsetX = evt.offsetX;
					var offsetY = evt.offsetY;	
				} else if (evt.clientX || evt.clientY) {
					var offsetX = evt.clientX - ((target.offsetLeft) ? target.offsetLeft : 0);
					var offsetY = evt.clientY - ((target.offsetTop) ? target.offsetTop : 0);
				}
				offsetX-=clickPosOffsetLeft;
				var clickX=Math.round(offsetX/letterWidth);
	
				// find a known container of the mouseclick
				if (target.nodeName=='LI') {
					clickY=1;
					while (target=target.previousSibling) {
						clickY++;
					}
				} else if (target.nodeName=='INPUT') {
				} else if (target.nodeName=='TEXTAREA') {
				} else if (target.nodeName=='DIV') {
				} else {
					clickY=Math.round(offsetY/lineHeight)+1;
				}
			}
		}
		if (clickX>-1 && clickY>-1) {
			return new Coordinate(clickX, clickY);		
		} else {
			return false;
		}
	}



	function mouseUpHandler(evt) {
		evt = (evt) ? evt : event;
		var coordinate=mouseGetCoordinates(evt);
		if (coordinate) {
			checkDirty();
			if (startcoordinate && 
					(!(startcoordinate.x==coordinate.x) || 
					 !(startcoordinate.y==coordinate.y)) ) {
				if (startcoordinate.y==coordinate.y) {
					// single line selection
					if (startcoordinate.x>coordinate.x) {
						var _temp=coordinate;
						coordinate=startcoordinate;
						startcoordinate=_temp;
					}
					currentLine=coordinate.y;
					redrawInputLine(true);
					setCursorPos(startcoordinate.x, coordinate.x, true);
					oldpos=-1;
					evt.cancelBubble=true;
					return false;
				} else {
					// multi line selection
					if (startcoordinate.y>coordinate.y) {
						var _temp=coordinate;
						coordinate=startcoordinate;
						startcoordinate=_temp;
					}
					
				}
			} else {
				// no selection
				currentLine=coordinate.y;
				redrawInputLine(true);
				setCursorPos(coordinate.x, 0, true);
				oldpos=-1;
				evt.cancelBubble=true;
				return false;
			}
		}
	}

	function mouseDownHandler(evt) {
		evt = (evt) ? evt : event;
		startcoordinate=mouseGetCoordinates(evt);
	}

	function startSelect() {
		userSelecting=true;
	}

	function stopSelect() {
		userSelecting=false;
		// Return the inputline to it's original size
		inputLine.style.height=lineHeight;
		inputLineEntry.style.height=lineHeight;
		currInputHeight = lineHeight;
		offsetLine=0;
		// get selected contents, put them in a buffer or something
		
	}

	function updateLine(lineNo, lineContent) {
		lines.childNodes[lineNo-1].originalInnerText = lineContent;
		highlightUpdateLine(lineNo-1, lineContent, applyHighlightedLine);
		if (lineContent.length>longestLine) {
			longestLine=lineContent.length;
			if( (longestLine*letterWidth) > minInputWidth ) {
				inputLine.style.width=longestLine*letterWidth+4;
			}
		}
	}

	function setLineContent(lineNo, lineContent) {
		if(lines.childNodes[lineNo-1]){
			lines.childNodes[lineNo-1].innerHTML=lineContent;
			
			lines.childNodes[lineNo-1].originalInnerHTML=lineContent;
		}
	}

	function insertLineAfter(lineNo, lineContent) {
		var oldCursor = getCursorPos();
		lines.childNodes[lineNo-1].insertAdjacentHTML("AfterEnd","<li>");
		highlightAppendLine(lineNo-1, lineContent, false); // do not display this yet
		currentLine=lineNo+1;
		updateLine(currentLine, lineContent);
		oldpos=-1;
		redrawInputLine(true);
		updateLineNumbers();
		lastLine++;
		if(saveUndo) {
			undoBuffer.push({type: undo.LINE_AFTER, line: currentLine, text: lineContent, cursor: getCursorPos(), newCursor: oldCursor });
		}
	}

	function deleteLine(lineNo) {
		if(saveUndo) {
			var content = getLineContent(lineNo);
			undoBuffer.push({type: undo.DELETE_LINE, line: lineNo, text: content, cursor: getCursorPos() , newCursor: content.length });
		}
		lines.childNodes[lineNo-1].removeNode(true);
		highlightDeleteLine(lineNo-1, applyHighlightedLine);
		updateLineNumbers();
		lastLine--;
	}

	function deleteContents() {
		var len = lines.childNodes.length;
		for (var i=len-1; i>0; i--) {
			lines.childNodes[i].removeNode(true);
		}
		setCursorPos(0);
		highlightReset();
		updateLineNumbers();
		lastLine=1;
		undoBuffer = [];
		redoBuffer = [];
	}


	function updateLineNumbers() {
		if (contentDiv.offsetHeight>winH) {
			document.getElementById('linenumbers').style.height=contentDiv.offsetHeight;
		}
	}


	function checkDirty() {
		if (isDirty) {
			var newContents = inputLineEntry.value;
			var oldContents = getLineContent(currentLine);
			if(newContents != oldContents) {
				undoBuffer.push({ type: undo.CHANGE_TEXT, line: currentLine, text: oldContents, newText: newContents,
					cursor: startCursorPos, newCursor: getCursorPos() });
				updateLine(currentLine, newContents);
				isDirty=false;
				startCursorPos = -1;
				return true;
			}
		} else {
		}
		return false;
	}

	function getLineHead() {
		var _line=new String(getLineContent(currentLine));
		var _pos=getCursorPos();
		return _line.substr(0, _pos);
	}

	function getLineTail() {
		var _line=new String(getLineContent(currentLine));
		var _pos=getCursorPos();
		return _line.substr(_pos);
	}

	function breakCurrentLine() {
		checkDirty();
		var head=getLineHead();
		var tail=getLineTail();
		var re = /([\t ]+)/;
		var match = re.exec(head);
		var newCursor = 0;
		
		if( autoIndent && match ) {
			tail = match[1] + tail;
			newCursor = match[1].length;
		}
		
		insertLineAfter(currentLine, tail);
		updateLine(currentLine-1, head);
		setCursorPos(newCursor);
		inputLineEntry.focus();
	}

	function glueCurrentLine(direction) {
		checkDirty();
		if (direction==-1) { 
			if (currentLine>1) {
				var tail=getLineContent(currentLine);
				var head=getLineContent(currentLine-1);
				updateLine(currentLine-1, head+tail);
				deleteLine(currentLine);
				currentLine--;
				redrawInputLine();
				setCursorPos(head.length);
			}			
		} else {
			if (currentLine<lastLine) {
				var tail=getLineContent(currentLine+1);
				var head=getLineContent(currentLine);
				updateLine(currentLine, head+tail);
				deleteLine(currentLine+1);
				redrawInputLine();
			}
		}
	}

	function jumpToLine(lineNo) {
		if(lineNo<lastLine) {
			currentLine=lineNo;
			oldpos=-1;
			redrawInputLine(true);
			updateLineNumbers();
		}
	}
	
