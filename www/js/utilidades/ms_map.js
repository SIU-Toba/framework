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

// Old code
//Object.prototype.objRef = null;
var imgDir = toba_alias + '/img/';
var pixel_img = new Image();
pixel_img.src = imgDir + 'pixel.gif';
var browser = new Browser();
var dragObj = new Object();
dragObj.zIndex = 0;

var _iconFullExtentButton = imgDir + 'alpha_button_fullExtent.png';
var _iconZoomboxButton    = imgDir + 'alpha_button_zoombox.png';
var _iconPanButton        = imgDir + 'alpha_button_pan.png';
var _iconZoominButton     = imgDir + 'alpha_button_zoomIn.png';
var _iconZoomoutButton    = imgDir + 'alpha_button_zoomOut.png';

// msMap class prototype
function msMap(DivTag)
{
	// Private vars
	var i        = this;
	var _tagMain = DivTag;
  	var exp_style = /^(\d+)\s*px$/i;
	var _map_w   = null;
	var _map_h   = null;

	// Hidden map border in in pixel. You can set map size highter respect
	// div size (visible map)
	var _map_w_bord = 0;
	var _map_h_bord = 0;

	var _control = null;
	var _protocol = 'mapservercgi'; // 'mapservercgi', 'wms'
	var _cgi     = '/cgi-bin/mapserv';
	var _mode    = 'map';
	var _layers  = '';
	var _map_file;
	var _args;
	var _attachedMsMap = null;
	var _referenceMap  = null;
	var _report = null;
	var _loading_counter = 0;

	var _ext_Xmin_orig;
	var _ext_Xmax_orig;
	var _ext_Ymin_orig;
	var _ext_Ymax_orig;
	var _ext_Xmin = null;
	var _ext_Xmax = null;
	var _ext_Ymin = null;
	var _ext_Ymax = null;

	var _zoombox_x_first;
	var _zoombox_y_first;
	var _zoombox_x_last;
	var _zoombox_y_last;
	var _pixel_w;
	var _pixel_h;

	var _toolbars = new Array(); // Array of toolbars
	var _iconLoading = imgDir + 'button_loading.gif';

	// WMS.GetMap protocol specific
	var _wms_imageformat = 'image/png';
	var _wms_projection = 'EPSG:4326';

	// Point Overlay array
	var _pointsOverlayArray = new Array();
	var _read_cookie = false;

	var _tagMap = null;
	var _tagMap_B = null;
	var _tagZoombox   = null;
	var _tagReference= null;
	var _tagLoading   = null;;
	var _tagOverlay   = null; // Overlay Layer
	var _tagPoints    = null; // Points Overlay container
	var _tagInfo      = null; // Report win Overlay container
	var _tagEvents = null;


	//------------------------------------------------------------------------------------------------------------------------------//
	//															CLASS METHODS																			 //
	//------------------------------------------------------------------------------------------------------------------------------//
	this.setWidth = function(width)
	{
		_map_w = width;
	}

	this.width = function()
	{
		if (_map_w == null) {
			_map_w   = parseInt(exp_style.exec( this.getMainTag().style.width )[1]);
		}
		return _map_w;
	}

	this.setHeight = function(height)
	{
		_map_h = height;
	}

	this.height = function()
	{
		if (_map_h == null) {
			_map_h   = parseInt(exp_style.exec( this.getMainTag().style.height )[1]);
		}
		return _map_h;
	}

	this.set_protocol = function(protocolo)
	{
		if (protocolo !== null) {
			_protocol = protocolo;
		}
	}

	this.setControlPos = function (controlType)
	{
		_control = controlType;
	}

	this.get_zoombox_x_first = function()
	{
		return _zoombox_x_first;
	}

	this.get_zoombox_x_last = function()
	{
		return _zoombox_x_last;
	}
	
	this.get_zoombox_y_first = function()
	{
		return _zoombox_y_first;
	}

	this.get_zoombox_y_last = function()
	{
		return _zoombox_y_last;
	}
	
	this.get_ext_Xmin = function()
	{
		return _ext_Xmin;
	}
	
	this.get_ext_Xmax = function()
	{
		return _ext_Xmax;
	}
	
	this.get_ext_Ymin = function()
	{
		return _ext_Ymin;
	}
	
	this.get_ext_Ymax = function()
	{
		return _ext_Ymax;
	}
	
	this.get_pixel_w = function()
	{
		return _pixel_w;
	}
	
	this.get_pixel_h = function()
	{
		return _pixel_h;
	}
	
	this.get_map_h_bord = function()
	{
		return _map_h_bord;
	}
	
	this.get_map_w_bord = function()
	{
		return _map_w_bord;
	}
	
	this.getTagMap = function()
	{
		if (_tagMap == null) {
				 _tagMap   = document.createElement('img');
		}
		return _tagMap;
	}

	this.getTagEvent = function()
	{
		if (_tagEvents == null) {
			_tagEvents    = document.createElement('div');
		}
		return _tagEvents;
	}

	this.getTagReference = function()
	{
		if (_tagReference == null) {
			_tagReference = document.createElement('div');
		}
		return _tagReference;
	}

	this.getTagInfo = function ()
	{
		if (_tagInfo == null) {
				_tagInfo   = document.createElement('div'); // Report win Overlay container
		}
		return _tagInfo;
	}

	this.getTagMap_B = function()
	{
		if (_tagMap_B == null) {
			_tagMap_B = document.createElement('img');
		}
		return _tagMap_B;
	}

	this.getTagZoomBox = function()
	{
		if (_tagZoombox == null) {
			_tagZoombox   = document.createElement('div');
		}
		return _tagZoombox;
	}

	this.getTagLoading = function()
	{
		if (_tagLoading == null) {
			_tagLoading   = document.createElement('img');
		}
		return _tagLoading;
	}

	this.getTagOverlay = function()
	{
		if(_tagOverlay == null) {
			_tagOverlay   = document.createElement('div'); // Overlay Layer
		}
		return _tagOverlay;
	}

	this.getTagPoint = function()
	{
		if (_tagPoints == null) {
			_tagPoints    = document.createElement('div');
		}
		return _tagPoints;
	}

	this.getToolbar = function(p)
	{
		return _toolbars[p];
	}

	this.setToolbar = function(p)
	{
		_toolbars.push(p);
		this.getMainTag().appendChild(_toolbars[0].getTag());
	}

	this.setWmsImageFormat = function(p)
	{
		_wms_imageformat = p;
	}

	this.setWmsProjection = function(p)
	{
		_wms_projection = p;
	}

	this.getMainTag = function()
	{
		return _tagMain;
	}

	this.getInfoTag = function()
	{
		return this.getTagInfo();
	}

	this.control    = function()
	{
		return _control;
	}

	this.setCgi     = function(path)
	{
		_cgi = path;
	}

	this.setMapFile = function(p_mapFile)
	{
		_map_file = 'map='+p_mapFile;
	}

	this.setMode    = function(p_mode)
	{
		_mode = p_mode;
	}

	this.setLayers  = function(p_layers)
	{
		_layers = p_layers
	}

	this.setArgs    = function(p_args)
	{
		_args = p_args;
	}

	this.attachMap  = function(myMap)
	{
		_attachedMsMap = myMap;
	}

	this.setReferenceMap = function(myMap)
	{
		_referenceMap = myMap;
	}

	this.setExtent  = function(Xmin, Xmax, Ymin)
	{
		//Me aseguro que los valores sean float
		Xmin = parseFloat(Xmin);
		Xmax = parseFloat(Xmax); 
		Ymin = parseFloat(Ymin);

		//Calculo la 4ta coordenada.
		_ext_Xmin = Xmin;
		_ext_Xmax = Xmax;
		_ext_Ymin = Ymin;
		_ext_Ymax = ((this.height() / this.width())*(_ext_Xmax - _ext_Xmin)) + _ext_Ymin;
	}

	this.getExtentActual = function()
	{
		var result = [];
		
		result['xmin'] = (_ext_Xmin - i.wPixel2real(this.getBorder()));
		result['ymin'] = (_ext_Ymin - i.hPixel2real(_map_h_bord));
		result['xmax'] = (_ext_Xmax + i.wPixel2real(this.getBorder()));
		result['ymax'] = (_ext_Ymax + i.hPixel2real(_map_h_bord));
		
		return result;
	}

	//------------------------------------------------------------------------------------------------------------------------------------------//
	//																	AUX METHODS																							//
	//------------------------------------------------------------------------------------------------------------------------------------------//
	
	// Active Debug mode
	this.debug = function()
	{
		var db = document.createElement('a');
		db.oncontextmenu    = function(){return false;};
		setZindex(db, '110');
		db.style.position   = 'absolute';
		db.style.fontFamily = 'Verdana, Arial, Helvetica, sans-serif';
		db.style.fontSize   = '11px';
		db.style.left       = i.width()-72+'px';
		db.style.top        = i.height()-13+'px';
		db.style.fontWeight = 'normal';
		db.style.textDecoration = 'overline';
		db.appendChild(document.createTextNode("Debug INFO"));
		this.getMainTag().appendChild(db);
		add_event(db, 'click', function(){prompt('Debug INFO', i.get_map_url())} );
	}

	this.setReport = function(p)
	{
		if (_report != null) {
		//_tagInfo.removeChild(_tagInfo.childNodes[0]);
			i.setReportNull();
		}
		_report = p;
	}

	this.setReportNull = function()
	{
		if (this.getTagInfo().childNodes[0]) {
			this.getTagInfo().removeChild(this.getTagInfo().childNodes[0]);
		}
		delete _report;
		_report = null;
	}

	i.show_loading_image = function(p)
	{
		if (p == true)	{
			_loading_counter++;
			this.getTagLoading().style.display = '';
		} else if (p == false) {
			_loading_counter--;
			if (_loading_counter < 0) {
				_loading_counter = 0;
			}
			//xxx errore!!! da risolvere!!!    if (_loading_counter == 0)
			{this.getTagLoading().style.display = 'none';}
		}
	}

	this.getBorder = function()
	{
		return _map_w_bord;
	}

	this.setBorder = function(p)
	{
		_map_w_bord = p;
		_map_h_bord = p;

		// First image buffer (double buffer)
		this.getTagMap().style.width  = (i.width()+ this.getBorder()+this.getBorder())+'px';
		this.getTagMap().style.height = (i.height()+_map_h_bord+_map_h_bord)+'px';
		this.getTagMap().style.top    = (- this.getBorder()) +'px';
		this.getTagMap().style.left   = (- _map_h_bord) +'px';

		// Second image buffer (double buffer)
		this.getTagMap_B().style.width  = this.getTagMap().style.width;
		this.getTagMap_B().style.height = this.getTagMap().style.height;
		this.getTagMap_B().style.top    = this.getTagMap().style.top;
		this.getTagMap_B().style.left   = this.getTagMap().style.left;
	}

	this.recalc_pixel_size = function()
	{
		_pixel_w  = (_ext_Xmax - _ext_Xmin) / this.width();
		_pixel_h  = (_ext_Ymax - _ext_Ymin) / this.height();
	}

	this.recalc_map_size = function()
	{
		i.recalc_pixel_size();
		if ( _pixel_w > _pixel_h )
		{ // Modify only Y (box width > height)
			var middle = ((_ext_Ymax - _ext_Ymin) / 2) + _ext_Ymin;
			var new_h = (this.height() / this.width()) * (_ext_Xmax - _ext_Xmin);
			_ext_Ymin = middle - (new_h / 2);
			_ext_Ymax = middle + (new_h / 2);
		} else
		{ // Modify only X (box width < height)
			var middle = ((_ext_Xmax - _ext_Xmin) / 2) + _ext_Xmin;
			var new_w = (this.width() / this.height()) * (_ext_Ymax - _ext_Ymin);
			_ext_Xmin = middle - (new_w / 2);
			_ext_Xmax = middle + (new_w / 2);
		}

		i.recalc_pixel_size();
	}

	//------------------------------------------------------------------------------------------------------------------------------------------//
	//																	POSITION METHODS																			  //
	//------------------------------------------------------------------------------------------------------------------------------------------//
	// Check if a box (x, y, width, height) is within a map
	this.isPointInMap = function( p_x, p_y, p_w, p_h )
	{
		if ( (p_x > (_ext_Xmax+i.wPixel2real(this.getBorder()))) ||
			((p_x+p_w) < (_ext_Xmin-i.wPixel2real(this.getBorder()))) ) return false;
		if ( ((p_y-p_h) > (_ext_Ymax+i.hPixel2real(_map_h_bord))) ||
			((p_y+p_h-p_h) < (_ext_Ymin-i.hPixel2real(_map_h_bord))) ) return false;

		return true;
	}

	// Permette di disegnare un box (oggetto this.getTagReference()) nella mappa, usato
	// internamente per impostare il reference di un'altra mappa.
	this.setReferenceBox = function(p_Xmin, p_Xmax, p_Ymin, p_Ymax)
	{
		//i.fullExtentNoRedraw();  // 20060316
		Xmin = i.xReal2pixel(p_Xmin);Ymin = i.yReal2pixel(p_Ymin);
		Xmax = i.xReal2pixel(p_Xmax);Ymax = i.yReal2pixel(p_Ymax);

		this.getTagReference().style.left    = Xmin +'px';
		this.getTagReference().style.top     = Ymax +'px';
		this.getTagReference().style.width   = Xmax - Xmin +'px';
		this.getTagReference().style.height  = Ymin - Ymax +'px';
		this.getTagReference().style.display = '';
	}

	// Converte una coordinata X/Y reale in pixel (rispetto al bordo
	// sinistro/superiore dell'immagine)
	this.xReal2pixel = function(p_x)
	{
		return Math.round(this. _map_w * (p_x - _ext_Xmin) / (_ext_Xmax - _ext_Xmin) );
	}

	this.yReal2pixel = function(p_y)
	{
		return Math.round(this. _map_h * (_ext_Ymax - p_y) / (_ext_Ymax - _ext_Ymin) );
	}

	this.wPixel2real = function(p_w)
	{
		return (p_w * _pixel_w);
	}

	this.hPixel2real = function(p_h)
	{
		return (p_h * _pixel_h);
	}

	this.wReal2pixel = function(p_w)
	{
		return (p_w / _pixel_w);
	}

	this.hReal2pixel = function(p_h)
	{
		return (p_h / _pixel_w);
	}

	this.xPixel2Real = function(p_x)
	{
		return i.wPixel2real(p_x)+_ext_Xmin;
	}

	this.yPixel2Real = function(p_y)
	{
		return i.hPixel2real(_map_h-p_y)+_ext_Ymin;
	}

	this.setExtent  = function(Xmin, Xmax, Ymin)
	{
		Xmin = parseFloat(Xmin);
		Xmax = parseFloat(Xmax);
		Ymin = parseFloat(Ymin);

		_ext_Xmin = Xmin;
		_ext_Xmax = Xmax;
		_ext_Ymin = Ymin;
		_ext_Ymax = ((this.height() / this.width())*(_ext_Xmax - _ext_Xmin)) + _ext_Ymin;
	}

	this.setFullExtent = function(Xmin, Xmax, Ymin)
	{
		_ext_Xmin_orig = parseFloat(Xmin);
		_ext_Xmax_orig = parseFloat(Xmax);
		_ext_Ymin_orig = parseFloat(Ymin);
		_ext_Ymax_orig = ((this.height() / this.width())*(_ext_Xmax_orig - _ext_Xmin_orig)) + _ext_Ymin_orig;
		if (_read_cookie == false)
				{i.fullExtentNoRedraw();}	// 20060316 (IE bugfix)
	}

	this.fullExtentNoRedraw = function()
	{
		_ext_Xmin = _ext_Xmin_orig;
		_ext_Xmax = _ext_Xmax_orig;
		_ext_Ymin = _ext_Ymin_orig;
		_ext_Ymax = _ext_Ymax_orig;
	}

	this.fullExtent = function()
	{
		i.fullExtentNoRedraw();
		i.redraw();
	}

	//---------------------------------------------------------------------------------------------------------------------------------------------//
	//																		ZOOM METHODS
	//---------------------------------------------------------------------------------------------------------------------------------------------//
	this.setZoomboxFirst = function(x, y)
	{
		_zoombox_x_first = x;
		_zoombox_y_first = y;
	}

	this.setZoomboxWH = function(x, y)
	{
		_zoombox_x_last = x;
		_zoombox_y_last = y;
		this.getTagZoomBox().style.left   = min(_zoombox_x_first, _zoombox_x_last) + 'px';
		this.getTagZoomBox().style.top    = min(_zoombox_y_first, _zoombox_y_last) + 'px';
		this.getTagZoomBox().style.width  = max(_zoombox_x_last,  _zoombox_x_first) - min(_zoombox_x_last,  _zoombox_x_first) + 'px';
		this.getTagZoomBox().style.height = max(_zoombox_y_last,  _zoombox_y_first) -  min(_zoombox_y_last,  _zoombox_y_first) + 'px';
		this.getTagZoomBox().style.display = '';
	}

	this.zoomboxExtent = function()
	{
		this.getTagZoomBox().style.display = 'none';

		var ll = min(_zoombox_x_last,_zoombox_x_first);
		var rr = max(_zoombox_x_last, _zoombox_x_first);
		var bb = max(_zoombox_y_last, _zoombox_y_first);
		var tt = min(_zoombox_y_last, _zoombox_y_first);

		_ext_Xmin += ll * _pixel_w;
		_ext_Xmax -= (this.width() - rr) * _pixel_w;
		_ext_Ymax -= tt * _pixel_h;
		_ext_Ymin += (this.height() - bb) * _pixel_h;
	}

	this.zoomPerc = function(p_perc)
	{
		var wx     = _ext_Xmax - _ext_Xmin;
		var wx_new = wx * p_perc;
		var kx     = (wx_new - wx) / 2;
		var wy     = _ext_Ymax - _ext_Ymin;
		var wy_new = wy * p_perc;
		var ky     = (wy_new - wy) / 2;
		i.setExtent(_ext_Xmin + kx,_ext_Xmax - kx, _ext_Ymin + ky);
	}

	this.zoomStart = function(event)
	{
		//var el;
		dragObj.elNode = this.getTagMap();
		var x = i.getClick_X(event);
		var y = i.getClick_Y(event);
		if ( isNaN(this.getTagLoading().style.display) )
		{
			i.setZoomboxFirst(x, y);			
			if (! browser.isIE) {
				event.stopPropagation();
				event.preventDefault();				
				document.addEventListener("mousemove", i.zoomGo,   true);
				document.addEventListener("mouseup",   i.zoomStop, true);
			} else {											//IE event model 
				window.event.cancelBubble = true;
				window.event.returnValue = false;
				document.attachEvent("onmousemove", i.zoomGo);
				document.attachEvent("onmouseup",   i.zoomStop);
			}
		}
	}

	this.zoomGo = function(event)
	{
		var x = i.getClick_X(event);
		var y = i.getClick_Y(event);

		i.setZoomboxWH(x, y);
		if (! browser.isIE) {
			event.stopPropagation();			
			event.preventDefault();
		} else {
			window.event.cancelBubble = true;
			window.event.returnValue = false;
		}
	}

	this.zoomStop = function(event)
	{
		// Stop capturing mousemove and mouseup events.
		var flag = true;
		del_event(document, "mousemove", i.zoomGo, flag);
		del_event(document, "mouseup", i.zoomStop, flag);
		i.zoomboxExtent();
		i.redraw();
	}

	//----------------------------------------------------------------------------------------------------------------------------------------------//
	//																		DRAGGING METHODS
	//----------------------------------------------------------------------------------------------------------------------------------------------//
	this.dragStart = function(event)
	{
		//var el;
		dragObj.elNode = this.getTagMap();
		var x = i.getClick_X(event) + DL_GetElementLeft(i.getTagMap());
		var y = i.getClick_Y(event) + DL_GetElementTop(i.getTagMap());

		// Save starting positions of cursor and element.
		dragObj.cursorStartX = x;
		dragObj.cursorStartY = y;
		dragObj.elStartLeft  = parseInt(dragObj.elNode.style.left, 10);
		dragObj.elStartTop   = parseInt(dragObj.elNode.style.top,  10);
		if (isNaN(dragObj.elStartLeft)) {
						dragObj.elStartLeft = 0;
		}
		if (isNaN(dragObj.elStartTop)) {
						dragObj.elStartTop  = 0;
		}

		// Update element's z-index.
		//dragObj.elNode.style.zIndex = ++dragObj.zIndex;       // Serve???
		// Capture mousemove and mouseup events on the page.
		// xxxxxxxxxxxxxxxx yyyyyyyyyyyyyyyyyyyyyyyy				
		if ( isNaN(this.getTagLoading().style.display) )
		{
			if (! browser.isIE) {
				event.stopPropagation();
				event.preventDefault();				
				document.addEventListener("mousemove", i.dragGo,   true);
				document.addEventListener("mouseup",   i.dragStop, true);
			} else {
				window.event.cancelBubble = true;
				window.event.returnValue = false;				
				document.attachEvent("onmousemove", i.dragGo);
				document.attachEvent("onmouseup",   i.dragStop);
			}
		}
	}

	this.dragGo = function(event)
	{
		var xx = i.getClick_X(event) + DL_GetElementLeft(i.getTagMap());
		var yy = i.getClick_Y(event) + DL_GetElementTop(i.getTagMap());

		// Move map by the same amount the cursor has moved.
		dragObj.elNode.style.left = (dragObj.elStartLeft + xx - dragObj.cursorStartX) + "px";
		dragObj.elNode.style.top  = (dragObj.elStartTop  + yy - dragObj.cursorStartY) + "px";
		// Move Overlay layer
		i.getTagOverlay().style.left = parseInt(dragObj.elNode.style.left) + i.getBorder() +'px';
		i.getTagOverlay().style.top  = parseInt(dragObj.elNode.style.top)  +_map_h_bord +'px';

		if (! browser.isIE) {
			event.stopPropagation();
			event.preventDefault();
		} else {
			window.event.cancelBubble = true;
			window.event.returnValue = false;
		}
	}

	this.dragStop = function(event)
	{
		// Stop capturing mousemove and mouseup events.
		var flag = true;
		del_event(document, "mousemove", i.dragGo, flag);
		del_event(document, "mouseup", i.dragStop, flag);

		i.calculatePan(event);
		i.redraw();
	}

	this.calculatePan = function(event)
	{
		var xx, yy;
		var x = i.getClick_X(event) + DL_GetElementLeft(i.getTagMap());
		var y = i.getClick_Y(event) + DL_GetElementTop(i.getTagMap());
		// Move drag element by the same amount the cursor has moved.
		xx = (dragObj.elStartLeft + x - dragObj.cursorStartX);
		yy = (dragObj.elStartTop  + y - dragObj.cursorStartY);
		// Add buffer size
		xx += i.getBorder();
		yy += _map_h_bord;

		if ((xx != 0) || (yy != 0)) {
			i.setPan(xx, yy);
		}
	}

	//----------------------------------------------------------------------------------------------------------------------------------------------//
	//																SET ACTIONS	METHODS
	//----------------------------------------------------------------------------------------------------------------------------------------------//
	this.setActionZoombox = function()
	{ //_action = 'zoom';
		this.getTagMap().style.cursor = "crosshair";
	}

	this.setActionPan = function()
	{ //_action = 'pan';
		this.getTagMap().style.cursor = "move";
	}

	this.setActionCoords = function()
	{ //_action = 'coords';
		this.getTagMap().style.cursor = "crosshair";
	}

	this.setActionNone = function()
	{
		for (j=0; j < _toolbars.length; j++){
			_toolbars[j].hide();
		}
	//    _action = 'none';
		this.getTagMap().style.cursor = "";
	}

	this.setActionZoomIn = function()
	{
		if ( isNaN(this.getTagLoading().style.display) )
		{
			i.zoomPerc(1.40);
			i.redraw();
		}
	}

	this.setActionZoomOut = function()
	{
		if ( isNaN(this.getTagLoading().style.display) )
		{
			i.zoomPerc(0.30);
			i.redraw();
		}
	}

	this.setPan = function(x, y)
	{
		i.recalc_pixel_size();
		var x_real = x * _pixel_w;
		var y_real = y * _pixel_h;
		_ext_Xmin = _ext_Xmin - x_real;
		_ext_Xmax = _ext_Xmax - x_real;
		_ext_Ymin = _ext_Ymin + y_real;
		_ext_Ymax = _ext_Ymax + y_real;
	}

	//-------------------------------------------------------------------------------------------------------------------------------------------------//
	//																			CLICK METHODS
	//-------------------------------------------------------------------------------------------------------------------------------------------------//
	this.getClick_X = function(p_event)
	{
		var my_x;
		if (browser.isNS) {
			my_x = p_event.clientX + window.scrollX;
		} else {
			my_x = window.event.clientX + document.documentElement.scrollLeft +  document.body.scrollLeft;
		}
		return my_x - DL_GetElementLeft(i.getTagMap()) - this.getBorder();
	}

	this.getClick_Y = function(p_event)
	{
		var my_y;
		if (browser.isNS) {
			my_y = p_event.clientY + window.scrollY;
		} else	{
			my_y = window.event.clientY + document.documentElement.scrollTop +  document.body.scrollTop;
		}
		return my_y - DL_GetElementTop(i.getTagMap()) - this.getBorder();
	}

	//------------------------------------------------------------------------------------------------------------------------------------------------//
	//																				WFS/WMS  METHODS
	//------------------------------------------------------------------------------------------------------------------------------------------------//
	this.addPointOverlay = function(p_point, p_redraw)
	{
		p_point.setMap(i);
		_pointsOverlayArray.push(p_point);

		if ( (p_redraw == null) || (p_redraw == true) ) {
			i.overlayPointsResort();
		}
	}

	this.removeOverlayPoints = function()
	{
		i.setReportNull();
		// Clean _pointsOverlayArray
		_pointsOverlayArray.splice(0, _pointsOverlayArray.length);
		// Empty _tagPoints icons
		var kids = this.getTagPoint().childNodes;
		for(var j=kids.length-1; j>=0; j--) {
			this.getTagPoint().removeChild(kids[j]);
		}
	}

	this.overlayPointsResort = function()
	{
		// Z sorting
		_pointsOverlayArray.sort(function(a,b){if (a.getY()>b.getY()){return -1;}else{return 1;}});
		// Empty _tagPoints icons
		var kids = this.getTagPoint().childNodes;
		for(var j=kids.length-1; j>=0; j--) {
			this.getTagPoint().removeChild(kids[j]);
		}
		for(var j=0; j<_pointsOverlayArray.length; j++)	{
			this.getTagPoint().appendChild(_pointsOverlayArray[j].getShd());
		}
		// Non sono nello stesso ciclo perche` Explorer li sovrappone in base
		// all'ordine dell'appendChild anziche` dello z-index. Cosi` le ombre
		// si sovrapponevano alle icone.
		for(var j=0; j<_pointsOverlayArray.length; j++) {
			this.getTagPoint().appendChild(_pointsOverlayArray[j].getImg());
		}
		// Redraw...
		for(var j=0; j<_pointsOverlayArray.length; j++)	{
			_pointsOverlayArray[j].redraw();
		}
	}

	this.setOverlayPoints = function(p, p_icon, p_infoSkin)
	{
		/*
		if (p_infoSkin == null)
		{
		// Create a default Info-window icon object...
		p_infoSkin = new msInfoSkin( '/img/angolo_a.png', '/img/angolo_b.png',
		'/img/angolo_c.png', '/img/angolo_d.png',
		'/img/report_t.png', '/img/report_d.png',
		'/img/report_l.png', '/img/report_r.png',
		'/img/report_x.png', '/img/close.png',
		'/img/report_arrow.png' );
		}
		if (p_icon == null) {p_icon = new msIcon(null, null);}
		*/

		for(var j=0; j<p[0].length; j++)
		{
			var myPoint = new pointOverlay(p_icon, p_infoSkin, 'Info', p[0][j], p[1][j], p[2][j], p[3][j]);
			i.addPointOverlay(myPoint, false);
			//myPoint.setMap(i);
			//_pointsOverlayArray.push(myPoint);
		}
		i.overlayPointsResort()

		/*
		// Z sorting
		_pointsOverlayArray.sort(function(a,b){if (a.getY()>b.getY()){return -1;}else{return 1;}});

		// Empty _tagPoints icons
		var kids = _tagPoints.childNodes;
		for(var j=kids.length-1; j>=0; j--) {_tagPoints.removeChild(kids[j]);}

		for(var j=0; j<_pointsOverlayArray.length; j++)
		{_tagPoints.appendChild(_pointsOverlayArray[j].getShd());}
		// Non sono nello stesso ciclo perche` Explorer li sovrappone in base
		// all'ordine dell'appendChild anziche` dello z-index. Cosi` le ombre
		// si sovrapponevano alle icone.
		for(var j=0; j<_pointsOverlayArray.length; j++)
		{_tagPoints.appendChild(_pointsOverlayArray[j].getImg());}

		// Redraw...
		for(var j=0; j<_pointsOverlayArray.length; j++)
		{_pointsOverlayArray[j].redraw();}
		*/
	}



	this.loadPointsOverlayWFS = function(p_serv, p_name, p_icon, p_infoSkin)
	{
		i.show_loading_image(true);  // Show "loading" image

		var url = p_serv + '?SERVICE=WFS&VERSION=1.0.0&REQUEST=getfeature&TYPENAME=' +  p_name;
		f = function(p_xml)
		{
			var mydata = parsePointsFromGML(p_xml);
			i.setOverlayPoints(mydata, p_icon, p_infoSkin);
		}
		getXML(url, f);

		// Hide "loading" image when map is loaded
		i.show_loading_image(false);
	}

	this.spatialPointQueryWMSurl = function(p_server, p_x, p_y, p_layers)
	  {
		if (p_server == null) {
			p_server = _cgi;
		}
		
		var c;
		if(p_server.indexOf('?')==-1) {
			c='?';
		}else{
			c='&';
		}
		
		var ext  = (_ext_Xmin - i.wPixel2real(this.getBorder())) + ','
				   + (_ext_Ymin - i.hPixel2real( _map_h_bord)) + ','
				   + (_ext_Xmax + i.wPixel2real(this.getBorder())) + ','
				   + (_ext_Ymax + i.hPixel2real(_map_h_bord)) ;


		var url = p_server + c + 'SERVICE=WMS&VERSION=1.1.1&REQUEST=GetFeatureInfo' +
				  '&INFO_FORMAT=text/plain' + //gml' +
				  '&LAYERS=' + p_layers +
				  '&QUERY_LAYERS=' + p_layers +
				  '&x=' + p_x + '&y=' + p_y +
				  '&bbox=' + ext +
				  '&width=' +this.width()+ '&height=' +this.height();
			  
		return url;
	  }

	  this.spatialPointQueryWMS = function(p_server, p_x, p_y, p_layers)
	  {
			var url = i.spatialPointQueryWMSurl(p_server, p_x, p_y, p_layers);
			f = function(p_xml)	{
				  alert(p_xml);
			}
			i.getXML(url, f);
	  }

	//------------------------------------------------------------------------------------------------------------------------------------------------//
	//																				MAP METHODS
	//------------------------------------------------------------------------------------------------------------------------------------------------//
	this.mapLoaded = function()
	{
		// Swap image buffer (double buffering)
		var tmp = i.getTagMap();
		_tagMap = i.getTagMap_B();
		_tagMap_B = tmp;
		
		// Disegna gli overlay puntuali
		i.getTagOverlay().style.left = '0';
		i.getTagOverlay().style.top  = '0';
		for(var j=0; j<_pointsOverlayArray.length; j++) {
			_pointsOverlayArray[j].redraw();
		}
		if ( _report != null ) {
			_report.redraw();
		}

		i.getTagMap().style.cursor = i.getTagMap_B().style.cursor;
		i.getTagMap().style.left = (- i.getBorder()) +'px';
		i.getTagMap().style.top  = (- _map_h_bord) +'px';
		i.getTagMap_B().style.display = 'none';
		i.getTagMap().style.display = '';

		// Hide "loading" image when map is loaded
		i.show_loading_image(false);
	}

	this.get_map_url = function()
	{
		var my_url;

		if (_protocol == 'mapservercgi')
		{
			var size = 'mapsize=' + (this.width() + this.getBorder() + this.getBorder()) +  '+' 	+ (this.height() + _map_h_bord + _map_h_bord);

			var ext  = 'mapext=' 
			+ (_ext_Xmin - i.wPixel2real(this.getBorder())) +  '+'
			+ (_ext_Ymin - i.hPixel2real(_map_h_bord)) +  '+'
			+ (_ext_Xmax + i.wPixel2real(this.getBorder())) +  '+'
			+ (_ext_Ymax + i.hPixel2real(_map_h_bord)) ;

			my_url = _cgi + '&mode=' +  _mode + '&' + _map_file + '&' + ext + '&' + size + '&layers=' + _layers;

			// Opera9 Bug Fix (onload event don't work if image is in cache)
			if (browser.isOP) {
				my_url = my_url + '&' + Math.random();
			}
		}
		if (_protocol == 'wms')
		{
			var imgtype = 'FORMAT=' + _wms_imageformat;
			var proj = 'SRS=' + _wms_projection;
			var lay = 'LAYERS=' + _layers.replace(/\ /g,",");

			var size = 'width='   + (this.width()+this.getBorder()+this.getBorder()) +
			'&height=' + (this.height()+_map_h_bord+_map_h_bord);

			var ext  = 'BBOX=' + (_ext_Xmin - i.wPixel2real(this.getBorder())) + ','
			+ (_ext_Ymin - i.hPixel2real(_map_h_bord)) + ','
			+ (_ext_Xmax + i.wPixel2real(this.getBorder())) + ','
			+ (_ext_Ymax + i.hPixel2real(_map_h_bord)) ;

			my_url = _cgi + '?VERSION=1.1.1&REQUEST=GetMap&' + proj + '&' + lay +
			'&STYLES=&' + ext + '&' + imgtype + '&' + size;
		}
		return my_url + '&' + _args;
	}

	this.redraw = function(redrawAttached)
	{
		i.show_loading_image(true);
		//if ( _ext_Xmax == null ) { i.fullExtentNoRedraw(); }  // 20060316
		i.recalc_map_size();

		// Set second buffer map image (Double buffer)
		this.getTagMap_B().src = i.get_map_url();
		//prompt('', this.getTagMap_B.src);
		if ( (_attachedMsMap != null) && (redrawAttached != false) ) {
			_attachedMsMap.attachMap(i);
			_attachedMsMap.setExtent(_ext_Xmin, _ext_Xmax, _ext_Ymin);
			_attachedMsMap.redraw(false);
		}

		if ( _referenceMap != null ) {	// Draw zoom box in the reference map
			_referenceMap.setReferenceBox(_ext_Xmin, _ext_Xmax, _ext_Ymin, _ext_Ymax);
		}
	}

	this.init = function()
	{
		// If exists, get cookie with saved extension
		//var c = getCookie(i.getCookieName());
		/*if (c != null)	{
			var cord = c.split(" ");
			i.setExtent(cord[0], cord[1], cord[2]);
			if (cord.length == 3) {
				_read_cookie = true;
			}
		}*/
		/*if (ie) {
			window.event.cancelBubble = true;
		}*/

		this.getMainTag().className = 'mscross';  // css
		this.getMainTag().oncontextmenu  = function(){return false;};
		this.getMainTag().style.width    = i.width()+'px';
		this.getMainTag().style.height   = i.height()+'px';
		this.getMainTag().style.overflow = 'hidden';
		this.getMainTag().style.position = 'relative';

		this.getTagEvent().oncontextmenu = function(){return false;};
		setZindex(this.getTagEvent(), '0');
		this.getTagEvent().style.position = 'absolute';
		this.getTagEvent().left           = '0';
		this.getTagEvent().top            = '0';

		// First buffer (double buffer)
		this.getTagMap().objRef = i;
		this.getTagMap().oncontextmenu  = function(){return false;};
		this.getTagMap().onmousedown = function(){return false;};  // Disable drag'n drop
		add_event(this.getTagMap(), 'load', i.mapLoaded );
		//i.tagMap.setAttribute('style', '-moz-user-select:none;');
		setZindex(this.getTagMap(), '0');
		this.getTagMap().galleryImg = "no";
		this.getTagMap().style.width    = (i.width()+ this.getBorder()+ this.getBorder())+'px';
		this.getTagMap().style.height   = (i.height()+ _map_h_bord+ _map_h_bord)+'px';
		this.getTagMap().style.border   = '0 none';
		this.getTagMap().style.margin   = '0';
		this.getTagMap().style.padding  = '0';
		this.getTagMap().style.position = 'absolute';
		this.getTagMap().style.top      = (- this.getBorder()) +'px';
		this.getTagMap().style.left     = (- _map_h_bord) +'px';
		this.getTagMap().style.display  = 'none';

		// Second buffer (double buffer)
		this.getTagMap_B().objRef = this.getTagMap().objRef;
		this.getTagMap_B().oncontextmenu = this.getTagMap().oncontextmenu;
		this.getTagMap_B().onmousedown = this.getTagMap().onmousedown;
		add_event(this.getTagMap_B(), 'load', i.mapLoaded );
		
		setZindex(this.getTagMap_B(), '0');
		this.getTagMap_B().galleryImg = "no";
		this.getTagMap_B().style.width    = this.getTagMap().style.width;
		this.getTagMap_B().style.height   = this.getTagMap().style.height;
		this.getTagMap_B().style.border   = this.getTagMap().style.border;
		this.getTagMap_B().style.margin   = this.getTagMap().style.margin;
		this.getTagMap_B().style.padding  = this.getTagMap().style.padding;
		this.getTagMap_B().style.position = this.getTagMap().style.position;
		this.getTagMap_B().style.top      = this.getTagMap().style.top;
		this.getTagMap_B().style.left     = this.getTagMap().style.left;
		this.getTagMap_B().style.display  = 'none';

		this.getTagReference().className = 'mscross_reference_zoombox';  // css
		this.getTagReference().oncontextmenu    = function(){return false;};
		setZindex(this.getTagReference(), '100');
		this.getTagReference().style.display    = 'none';
		this.getTagReference().style.position   = 'absolute';
		this.getTagReference().style.margin     = '0';
		this.getTagReference().style.padding = '0';
		this.getTagReference().style.lineHeight = '0';
		this.getTagReference().style.border     = '1px solid #000000';
		this.getTagReference().style.background = '#a0a0a0';
		this.getTagReference().style.opacity    = '0.20';               // Gecko
		this.getTagReference().style.filter     = 'alpha(opacity=20)';  // Windows
		this.getTagReference().style.fontSize   = '1'; // 20061012 bugfix by Rodrigo

		this.getTagZoomBox().oncontextmenu    = function(){return false;};
		setZindex(this.getTagZoomBox(), '100');
		this.getTagZoomBox().style.position   = 'absolute';
		this.getTagZoomBox().style.display    = 'none';
		this.getTagZoomBox().style.border     = '1px dashed #000000';
		this.getTagZoomBox().style.margin     = '0px';
		this.getTagZoomBox().style.padding = '0px';
		this.getTagZoomBox().style.lineHeight = '0';
		this.getTagZoomBox().style.background = '#606060';	         //'#f0f0f0';
		this.getTagZoomBox().style.opacity    = '0.18';               // Gecko
		this.getTagZoomBox().style.filter     = 'alpha(opacity=18)';  // Windows
		this.getTagZoomBox().style.fontSize   = '1'; // 20061012 bugfix by Rodrigo

		// Overlay Layer
		this.getTagOverlay().oncontextmenu    = function(){return false;};
		setZindex(this.getTagOverlay(), '30');
		this.getTagOverlay().style.position   = 'absolute';

		this.getTagPoint().oncontextmenu    = function(){return false;};
		setZindex(this.getTagPoint(), '40');
		this.getTagPoint().style.position   = 'absolute';

		this.getTagInfo().oncontextmenu   = function(){return false;};
		setZindex(this.getTagInfo(), '50');
		this.getTagInfo().style.position  = 'absolute';

		// "Loading" image tag
		this.getTagLoading().oncontextmenu    = function(){return false;};
		this.getTagLoading().onmousedown = function(){return false;};  // Disable drag'n drop
		setZindex(this.getTagLoading(), '100');
		this.getTagLoading().style.position   = 'absolute';
		this.getTagLoading().style.display    = 'none';
		this.getTagLoading().style.border     = '0';
		this.getTagLoading().style.margin     = '0';
		this.getTagLoading().style.padding    = '0';
		this.getTagLoading().style.lineHeight = '0';
		setAlphaPNG(this.getTagLoading(), _iconLoading);
		this.getTagLoading().style.left = (this.width() - 130) / 2 + 'px';
		this.getTagLoading().style.top  = (this.height() - 122) / 2 + 'px';

		// Double buffer
		this.getTagEvent().appendChild(this.getTagMap());
		this.getTagEvent().appendChild(this.getTagMap_B());

		this.getTagOverlay().appendChild(this.getTagPoint());
		this.getTagOverlay().appendChild(this.getTagInfo());

		this.getMainTag().appendChild(this.getTagEvent());
		this.getMainTag().appendChild(this.getTagOverlay());
		this.getMainTag().appendChild(this.getTagZoomBox());
		this.getMainTag().appendChild(this.getTagReference());

		this.getMainTag().appendChild(_tagLoading);
	}

	//------------------------------------------------------------------------------------------------------------------------------------------------------------//
	//																										MAIN 
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------//	
	if ( browser.isIE )	{
		this.getMainTag().onselectstart = function(){return false;};
		this.getMainTag().ondrag = function(){return false;};
	} else	{
		this.getMainTag().style.setProperty("-moz-user-select", "none", "");
	}

	this.init();
}

