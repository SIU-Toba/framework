// 20050927 - Copyright (C) 2005-2006 Simone Manca <simone.manca@gmail.com>
// http://datacrossing.crs4.it/en_Documentation_mscross.html
// v1.1.9 20070218
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
	//--------------------------------------------------------------------------------------------------------------------------//
	//																		TAGS
	//--------------------------------------------------------------------------------------------------------------------------//

	function setZindex(p_tag, p)
	{
		if (p_tag.setAttribute) 	{
			p_tag.setAttribute('style', 'z-index:'+p+';');
		} else	{
			p_tag.style.zIndex = p;
		}
	}

	function setPos(p_obj, p_x, p_y)
	{
		p_obj.style.left    = p_x+'px';
		p_obj.style.top     = p_y+'px';
		p_obj.style.display = '';
	}

	//--------------------------------------------------------------------------------------------------------------------------//
	//																		EVENTOS
	//--------------------------------------------------------------------------------------------------------------------------//
	// Non e` molto elegante come soluzione... ma sembra funzionare...
	function ChiamaEvento(e)
	{
		var i;
		if (e.srcElement) {i = e.srcElement.objRef}
		if (e.target)     {i = e.target.objRef}
		i.dragStart(e);
	}

	// http://www.quirksmode.org/js/events_advanced.html
	function add_event(obj, event_id, func)
	{
		if (obj.addEventListener) {
			obj.addEventListener(event_id, func, false);
		} else if(obj.attachEvent) {
			event_id = 'on'+event_id;
			obj.attachEvent(event_id, func);
		} else {
			obj[event_id] = func;
		}
	}

	function del_event(obj, event_id, funct, flag)
	{
		if (obj.removeEventListener) {
			obj.removeEventListener(event_id, funct, flag);
		} else if(obj.detachEvent) {
			obj.detachEvent(event_id, funct);
			obj.detachEvent('on'+event_id, funct);
		}
	}

	//--------------------------------------------------------------------------------------------------------------------------//
	//																IMAGENES
	//--------------------------------------------------------------------------------------------------------------------------//
	function setAlphaBackgroundPNG( p_Tag, p_src )
	{
		if ( browser.isIE )	{
			p_Tag.style.backgroundImage = 'none';
			p_Tag.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader"+
			"(src='"+p_src+"',sizingMethod='scale')";
		} else	{
			p_Tag.style.backgroundImage = "url('"+p_src+"')";
		}
	}

	function setAlphaPNG(p_imgTag, p_src)
	{
		if (browser.isIE) {
			p_imgTag.src = pixel_img.src;
			p_imgTag.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader"+
			"(src='"+p_src+"',sizingMethod='image')";
		} else {
			p_imgTag.src = p_src;
		}
		//# "image": Keep the original size of the image.
		//# "scale": Stretch or compress the image to the container boundaries.
		//# "crop": Crop the image to the container dimensions.
		/*
		// Trucco per ricavare le dimensioni dell'immagine...
		// Tanto l'immagine p_src dovrebbe essere caricata solo una volta.
		var tmp = new Image();
		tmp.onload=function()
		{
		p_imgTag.style.width  = tmp.width+'px';
		p_imgTag.style.height = tmp.height+'px';
		}
		tmp.src = p_src;
		*/
	}


	function min(a, b)
	{
		if ( a < b ) {
			return a;
		} else {
			return b;
		}
	}

	function max(a, b)
	{
		if ( a > b ) {
			return a;
		} else {
			return b;
		}
	}

	function Browser()
	{
		//alert(navigator.vendor);
		// Firefox:
		// Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.10) Gecko/20050716 Firefox/1.0.6

		// Explorer:
		// Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)

		// Opera:
		// Mozilla/4.0 (compatibile; MSIE 6.0; Windows NT 5.1; en) Opera 8.50
		this.isIE    =  ie;
		this.isNS    = ns6;
		this.isOP    = false;
		this.name    = navigator.appName;
		this.version = null;
		if ((navigator.userAgent).indexOf("Opera")!=-1) {
			this.isOP = true;
		}

		return;
	}

	// xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
	// Determining Element Page Coordinates, Part 4:
	// http://www.webreference.com/dhtml/diner/realpos4/9.html
	function DL_GetElement(eElement, top)
	{
		if (!eElement && this) {			// if argument is invalid (not specified, is null or is 0)
			eElement = this;					// and function is a method
		}												// identify the element as the method owner

		var DL_bIE = document.all ? true : false; // initialize var to identify IE
		var nLeftPos = eElement.offsetLeft;       // initialize var to store calculations
		var nTopPos = eElement.offsetTop;         // initialize var to store calculations
		var eParElement = eElement.offsetParent;  // identify first offset parent element

		while (eParElement != null) {
			// move up through element hierarchy
			if(DL_bIE) {                            // if browser is IE, then...
				if( (eParElement.tagName != "TABLE") && (eParElement.tagName != "BODY") ) {   // if parent is not a table or the body, then...
				nLeftPos += eParElement.clientLeft; // append cell border width to calcs
				nTopPos += eParElement.clientTop; // append cell border width to calcs			
				}
			} else {                                  // if browser is Gecko, then...
				if(eParElement.tagName == "TABLE")  // if parent is a table, then...
				{                                   // get its border as a number
				var nParBorder = parseInt(eParElement.border);
				if(isNaN(nParBorder))            // if no valid border attribute, then...
				{                                // check the table's frame attribute
					var nParFrame = eParElement.getAttribute('frame');
					if(nParFrame != null) {        // if frame has ANY value, then...
						nLeftPos += 1;             // append one pixel to counter
					}
				} else if(nParBorder > 0) {          // if a border width is specified, then...
					nLeftPos += nParBorder;       // append the border width to counter
				}
				}
				// sm 20051010
				if(eParElement.tagName == "DIV") {
				var bord = parseInt(eParElement.style.border);
				if ( bord > 0 ) { nLeftPos += bord; }
				}
			}
			nLeftPos += eParElement.offsetLeft;    // append left offset of parent
			nTopPos += eParElement.offsetTop;      // append top offset of parent
			eParElement = eParElement.offsetParent; // and move up the element hierarchy
		}                                         // until no more offset parents exist

		if (top) {								//Retorno la variable de acuerdo a lo que pidieron
			return nTopPos;
		} else {
			return nLeftPos;
		}
	}

	function DL_GetElementLeft(eElement)
	{
		return DL_GetElement(eElement, false);  
	}

	function DL_GetElementTop(eElement)
	{
		return DL_GetElement(eElement, true);
	}

	//--------------------------------------------------------------------------------------------------------------------------//
	//															CLASES EXTRAS
	//--------------------------------------------------------------------------------------------------------------------------//

	function msInfoSkin( p_corner_a, p_corner_b, p_corner_c, p_corner_d, p_top, p_bottom, p_left, p_right,	p_fill, p_close, p_arrow)
	{
		var _corner_a = new Image(); _corner_a.src = p_corner_a;
		var _corner_b = new Image(); _corner_b.src = p_corner_b;
		var _corner_c = new Image(); _corner_c.src = p_corner_c;
		var _corner_d = new Image(); _corner_d.src = p_corner_d;

		var _bord_top    = new Image(); _bord_top.src = p_top;
		var _bord_bottom = new Image(); _bord_bottom.src = p_bottom;
		var _bord_left   = new Image(); _bord_left.src = p_left;
		var _bord_right  = new Image(); _bord_right.src = p_right;

		var _fill  = new Image(); _fill.src = p_fill;
		var _close = new Image(); _close.src = p_close;
		var _arrow = new Image(); _arrow.src = p_arrow;

		this.getCornerA = function() { return _corner_a.src; }
		this.getCornerB = function() { return _corner_b.src; }
		this.getCornerC = function() { return _corner_c.src; }
		this.getCornerD = function() { return _corner_d.src; }
		this.getFill = function() { return _fill.src; }
		this.getLeft = function() { return _bord_left.src; }
		this.getRight = function() { return _bord_right.src; }
		this.getTop = function() { return _bord_top.src; }
		this.getBottom = function() { return _bord_bottom.src; }

		this.getClose = function() { return _close.src; }
		this.getArrow = function() { return _arrow.src; }
	}

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	// msIcon class prototype
	function msIcon( p_img, p_shd, p_offsetX, p_offsetY )
	{
		var _img_name = p_img;
		var _shd_name = p_shd;
		var _offsetX = 1;	// Distanza del target dall'angolo
		var _offsetY = 1;	// alto-sinistra.

		if ( p_offsetX != null ) {
			_offsetX = p_offsetX;
		}
		if ( p_offsetY != null ) {
			_offsetY = p_offsetY;
		}
		if ( _img_name == null ) {
			_img_name = imgDir + 'mm_20_red.png';
			_shd_name = imgDir +'mm_20_shadow.png';
			_offsetX  = 6;
			_offsetY = 19;
		}
		if (_img_name == '') {
			_img_name= imgDir + 'pixel.gif';
		}
		if (_shd_name == '') {
			_shd_name= imgDir + 'pixel.gif';
		}

		this.getShiftX = function() { return _offsetX; }
		this.getShiftY = function() { return _offsetY; }

		this.getImage = function()
		{
			var tmp = document.createElement('img');
			tmp.oncontextmenu  = function(){return false;};
			tmp.onmousedown = function(){return false;};  // Disable drag'n drop
			setZindex(tmp, '110');
			tmp.style.position = 'absolute';
			tmp.style.cursor   = 'pointer';
			setAlphaPNG(tmp, _img_name);
			return tmp;
		}

		this.getShadow = function()
		{
			var tmp = document.createElement('img');
			tmp.oncontextmenu  = function(){return false;};
			tmp.onmousedown = function(){return false;};
			setZindex(tmp, '109');
			tmp.style.position = 'absolute';
			setAlphaPNG(tmp, _shd_name);
			return tmp;
		}
	}

	//---------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	// msReport class prototype
	function msReport(p_pnt, p_title)
	{
		var _pointOverlay = p_pnt;
		var d = document.createElement('div');
		p_pnt.getMap().getInfoTag().appendChild(d);
		var _content = document.createElement('div');
		var _scrollX = 16;  // Gli offset devono essere impostati dinamicamente
		var _scrollY = 0;
		var _title = p_title;
		var j = this;
		var _infoSkin = p_pnt.getInfoSkin();

		// Set _content style
		_content.style.paddingTop = '6px';
		_content.style.fontSize = '80%';

		this.redraw = function()
		{
			var h = parseInt(d.offsetHeight);
			var os_x = _scrollX;
			var os_y = _scrollY +h;

			d.style.left = p_pnt.getInfoX() -os_x +'px';
			d.style.top  = p_pnt.getInfoY() -os_y +'px';
		}

		// Chiude la finestra
		this.close = function()
		{
			//d.removeChild( d.childNodes[0] );
			var taginfo = p_pnt.getMap().getInfoTag();
			taginfo.removeChild( taginfo.childNodes[0] );
			p_pnt.getMap().setReportNull();
			delete j;
		}

		// Imposta il contenuto
		this.setContent = function(p_html) {_content.innerHTML = p_html;}

		this.init = function()
		{
			// Main DIV container
			d.oncontextmenu  = function(){return false;};
			d.style.position = 'absolute';

			// External table (borders)
			var t_b  = document.createElement('table');
			t_b.cellSpacing = '0';
			t_b.cellPadding = '0';

			var tb_b = document.createElement('tbody');
			t_b.appendChild(tb_b);
			var tr_a = document.createElement('tr');
			tb_b.appendChild(tr_a);
			var tr_w = document.createElement('tr');
			tb_b.appendChild(tr_w);
			var tr_b = document.createElement('tr');
			tb_b.appendChild(tr_b);
			var tr_c = document.createElement('tr');
			tb_b.appendChild(tr_c);
			var tr_d = document.createElement('tr');
			tb_b.appendChild(tr_d);

			var td_a1 = document.createElement('td');
			tr_a.appendChild(td_a1);
			var td_a2 = document.createElement('td');
			tr_a.appendChild(td_a2);
			var td_a3 = document.createElement('td');
			tr_a.appendChild(td_a3);

			// Close button
			var td_w1 = document.createElement('td');
			tr_w.appendChild(td_w1);
			var td_w2 = document.createElement('td');
			tr_w.appendChild(td_w2);
			var td_w3 = document.createElement('td');
			tr_w.appendChild(td_w3);

			var td_b1 = document.createElement('td');
			tr_b.appendChild(td_b1);
			var td_b2 = document.createElement('td');
			tr_b.appendChild(td_b2);
			var td_b3 = document.createElement('td');
			tr_b.appendChild(td_b3);

			var td_c1 = document.createElement('td');
			tr_c.appendChild(td_c1);
			var td_c2 = document.createElement('td');
			tr_c.appendChild(td_c2);
			var td_c3 = document.createElement('td');
			tr_c.appendChild(td_c3);

			var td_d1 = document.createElement('td');
			tr_d.appendChild(td_d1);
			var td_d2 = document.createElement('td');
			tr_d.appendChild(td_d2);
			var td_d3 = document.createElement('td');
			tr_d.appendChild(td_d3);

			var ang_a = document.createElement('img');
			setAlphaPNG(ang_a, _infoSkin.getCornerA());
			ang_a.onmousedown = function(){return false;};

			var ang_b = document.createElement('img');
			setAlphaPNG(ang_b, _infoSkin.getCornerB());
			ang_b.onmousedown = function(){return false;};

			var ang_c = document.createElement('img');
			setAlphaPNG(ang_c, _infoSkin.getCornerC());
			ang_c.onmousedown = function(){return false;};

			var ang_d = document.createElement('img');
			setAlphaPNG(ang_d, _infoSkin.getCornerD());
			ang_d.onmousedown = function(){return false;};

			var arrow = document.createElement('img');
			setAlphaPNG(arrow, _infoSkin.getArrow());
			arrow.onmousedown = function(){return false;};

			td_a1.appendChild(ang_a);
			td_a3.appendChild(ang_b);
			td_c1.appendChild(ang_d);
			td_c3.appendChild(ang_c);
			td_d2.appendChild(arrow);
			td_b2.appendChild(_content);

			setAlphaBackgroundPNG(td_b2, _infoSkin.getFill());
			setAlphaBackgroundPNG(td_b1, _infoSkin.getLeft());
			setAlphaBackgroundPNG(td_b3, _infoSkin.getRight());
			setAlphaBackgroundPNG(td_a2, _infoSkin.getTop());
			setAlphaBackgroundPNG(td_c2, _infoSkin.getBottom());
			setAlphaBackgroundPNG(td_w1, _infoSkin.getLeft());
			setAlphaBackgroundPNG(td_w2, _infoSkin.getFill());
			setAlphaBackgroundPNG(td_w3, _infoSkin.getRight());

			var close = document.createElement('img');
			setAlphaPNG(close, _infoSkin.getClose());
			add_event(close, 'click', function(){ j.close(); } );

			// Info window Title
			var tt = document.createElement('table');
			tt.style.width = "100%";
			var tt_b = document.createElement('tbody');
			tt.appendChild(tt_b);
			var tt_tr = document.createElement('tr');
			tt_b.appendChild(tt_tr);
			var tt_td1 = document.createElement('td');
			tt_tr.appendChild(tt_td1);
			var tt_td2 = document.createElement('td');
			tt_tr.appendChild(tt_td2);
			var title = document.createTextNode(_title);

			tt_td1.className = 'mscross_report_title';  // css

			tt_td1.style.fontWeight = 'bold';
			tt.cellSpacing = '0';
			tt.cellPadding = '0';
			tt_td1.style.borderBottom = '1px dashed #d0d0d0';
			tt_td1.appendChild(title);
			tt_td2.appendChild(close);
			tt_td2.style.textAlign = 'right';
			td_w2.appendChild(tt);

			d.appendChild(t_b);

			// BUG Firefox 1.0.7 ??? ////////
			if (browser.isNS) {
				d.style.display = 'table';
				//      t.style.display = 'table-cell';
				//d.style.setProperty("-moz-box-align", "stretch", "");
				//d.style.setProperty("-moz-box-sizing", "padding-box", "");
				// -moz-box-align stretch
				// -moz-box-sizing
			}
		}

		this.init();
		this.setContent(p_pnt.getHtmlAttributes());
		this.redraw();
	}

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	function getXML(p_url, p_funct)
	{
		http_request = false;
		if (window.XMLHttpRequest) {// Mozilla, Safari,...
			http_request = new XMLHttpRequest();
			if (http_request.overrideMimeType)	{ http_request.overrideMimeType('text/xml'); }
		} else if (window.ActiveXObject) {// IE
			try	{
				http_request = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e)	{
				try {
						http_request = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) {/*Non fatto niente*/}
			}
		}

		if (!http_request)	{
			alert('Giving up :( Cannot create an XMLHTTP instance');
			return false;
		}
		
		http_request.onreadystatechange = function()
		{
			if (http_request.readyState == 4) {
				if (http_request.status == 200)	{
						var xml = http_request.responseXML;
						p_funct(xml);
				} else {
					alert('There was a problem with the request.');
				}
			}
		}
		http_request.open('GET', p_url, true);
		http_request.send(null);
	}
	//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	function parsePointsFromGML(myxml)
	{
		var _coords = null;
		var _name = null;
		var prefix = "";
		var featureMember_Name = "featureMember";
		//var msGeometry_Name    = "msGeometry";
		var Point_Name         = "Point";
		var _add               = 0; // Mozilla utilizza gli indici di "childNodes" + 1.
		var _molt              = 1; // Mozilla moltiplica per 2

		if (browser.isIE) {  // IE
				featureMember_Name = "gml:"+featureMember_Name;
				// msGeometry_Name    = "ms:"+msGeometry_Name;
				Point_Name         = "gml:"+Point_Name;
		} else	if (window.XMLHttpRequest) { // Mozilla, Safari,...
				_add  = 1;
				_molt = 2;
		}

		var _data = new Array();
		_data[0] = new Array();	// X
		_data[1] = new Array();	// Y
		_data[2] = new Array();	// Name
		_data[3] = new Array();	// Value

		// For each point in GML file...
		var count = myxml.getElementsByTagName(featureMember_Name).length;
		for(var i=0; i<count; i++)	{
			_coords =  myxml.getElementsByTagName(featureMember_Name)[i].
			childNodes[0+_add].childNodes[0+_add].
			childNodes[0+_add].
			childNodes[0+_add].childNodes[0].nodeValue;
			var tmp = new Array(); tmp = _coords.split(',');
			var names = new Array(); var values = new Array();

			// Per ogni attributo alfanumerico...
			var size = (myxml.getElementsByTagName(featureMember_Name)[0].
			childNodes[0+_add].childNodes.length - _add) / _molt;

			for (var j=2; j<size; j++) {
					nam = myxml.getElementsByTagName(featureMember_Name)[i].
					childNodes[0+_add].childNodes[(j * _molt) +_add].tagName;
					var nam = nam.split(":");

					val = myxml.getElementsByTagName(featureMember_Name)[i].
					childNodes[0+_add].childNodes[(j * _molt) +_add].
					childNodes[0].nodeValue;

					names.push(nam[1]);
					values.push(val);
			}

			_data[0][i] = tmp[0];	// X
			_data[1][i] = tmp[1];	// Y
			_data[2][i] = names;	// Attributes Name
			_data[3][i] = values;	// and Values
		}

		return _data;
	}
