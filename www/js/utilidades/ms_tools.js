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
function msTool(p_title, p_event_name, p_icon, usa_evento_auxiliar)
	{
		var _toolbar;
		var _event_name = p_event_name;
		var _event_map = null;

		if (usa_evento_auxiliar == true) {
			//Cuando es necesario que el evento no se lance apenas se produce el click
			//creo una funcion local que lo dispara luego
			_event_map = function(e) {
					if (e) {
						_toolbar.triggerEvent(_event_name, e);
					}
			}
		}

		this.eventClick = function(e)
		{
			if (_event_map != null)  {				//Si el evento posee una funcion local significa que no se activa solo  con el click.
				_toolbar.removeEvents();
				_toolbar.addMapEvent('mousedown', _event_map);
			}else if (_event_name != null) {				//Para los que necesitan solo del click
				_toolbar.triggerEvent(_event_name, e);
			}
		}

		this.create_element = function(p_title, p_icon)
		{
			var _img = document.createElement('img');
			_img.className = 'mscross_tool';
			_img.oncontextmenu = function(){return false;};
			_img.onmousedown = function(){return false;};  // Disable drag'n drop
			setAlphaPNG(_img, p_icon);
			_img.title = p_title;
			setZindex(_img, '200');
			_img.style.margin = '0';
			_img.style.padding = '0';
			_img.style.position = 'absolute';
			_img.style.cursor = 'pointer';
			_img.style.display = 'none';

			return _img;
		}

		var _tag = this.create_element(p_title, p_icon);
		add_event(_tag, 'click', this.eventClick);
		this.getTag = function(){return _tag;}
		this.setToolbar = function(p){ _toolbar=p; }
		this.haveMapEvent = function(){if (_event_map == null){return false;}return true;}
		this.removeEvent = function()
		{
			if (_event_map != null) {
				_toolbar.removeMapEvent('mousedown', _event_map);
			}
		}
	}

	function msToolbar(p_msMap, _control, _default, caller)
	{
		var	_caller = caller;
		var _toolbarArray = new Array();
		var _msMap = p_msMap;
		var _tagMap = _msMap.getTagMap();

		var _tagToolbar = document.createElement('div');
		setZindex(_tagToolbar, '100');
		_tagToolbar.style.position = 'absolute';
		this.getTag = function() {return _tagToolbar;}
		this.hide = function() {_tagToolbar.style.display = 'none';}
		_tagToolbar.oncontextmenu = function(){return false;};
	
		this.create_toolbox = function ()
		{
			var box = document.createElement('div');
			box.oncontextmenu  = function(){return false;};
			setZindex(box, '100');
			box.style.position = 'absolute';
			box.style.display  = '';
			box.style.border     = '0px';
			box.style.margin     = '0px';
			box.style.padding    = '0px';
			box.style.background = '#404040';
			box.style.lineHeight = '0';
			box.style.opacity    = '0.20';               // Gecko
			box.style.filter     = 'alpha(opacity=20)';  // Windows
			return box;
		}

		this.setMap = function(p_msMap)
		{
			_msMap = p_msMap;
		}
		
		this.addTool = function(p_tool)
		{
			p_tool.setToolbar(this);
			_toolbarArray.push(p_tool);
			_tagToolbar.appendChild(p_tool.getTag());
		}

		this.addMapEvent = function(event_name, funct)
		{
			add_event(_msMap.getTagEvent(), event_name, funct);
		}

		this.removeMapEvent = function (event_name, funct)
		{			
			del_event(_msMap.getTagEvent(), event_name, funct, false);
		}

		this.removeEvents = function()
		{
			for (i=0; i < _toolbarArray.length; i++) {
				_toolbarArray[i].removeEvent();
			}
		}

		this.triggerEvent = function(event_name,e)
		{
			_caller[event_name](e);	
		}

		this.activateButtons = function ()
		{
			// Activate first button with map function
			for (i=0; i < _toolbarArray.length; i++) {
				if (_toolbarArray[i].haveMapEvent() == true) {
					_toolbarArray[i].eventClick();
					break;
				}
			}
		}
		
		//------------------------------------------------------------------------------------------------------------------------------//
		//																	TOOLBAR POSITION	
		//------------------------------------------------------------------------------------------------------------------------------//
		this.drawLeft = function()
		{
				for (i=0; i < _toolbarArray.length; i++) {
					setPos(_toolbarArray[i].getTag(), 3, (i*40)+5 );
				}
				box.style.left   = '0px';
				box.style.top    = '0px';
				box.style.width  = '40px';
				box.style.height = _tagMap.style.height;
		}

		this.drawRight = function()
		{				
				box.style.left   = (parseInt(_tagMap.style.width) - (40+_msMap.getBorder()*2)) +'px';
				box.style.top    = '0px';
				box.style.width  = '40px';
				box.style.height = _tagMap.style.height;
				for (i=0; i < _toolbarArray.length; i++) {
					setPos(_toolbarArray[i].getTag(), parseInt(box.style.left)+5, (i*40)+5);
				}
		}

		this.drawUp = function()
		{
				for (i=0; i < _toolbarArray.length; i++){
					setPos(_toolbarArray[i].getTag(), (i*40)+5, 5 );
				}
				box.style.left   = '0px';
				box.style.top    = '0px';
				box.style.width  = _tagMap.style.width;
				box.style.height = '40px';
		}

		this.redraw = function()
		{
			if ( (_control == 'standard')  ||	(_control == 'standardRight') || (_control == 'standardCornerRight') ) {
				this.drawRight();
			} else	if ( (_control == 'standardLeft') || (_control == 'standardCornerLeft') ) {
				this.drawLeft();
			} else	if (_control == 'standardUp') {
				this.drawUp();
			}
		}

		//-----------------------------------------------------------------------------------------------------------------------------------------------------//
		//																							MAIN
		//-----------------------------------------------------------------------------------------------------------------------------------------------------//
		var box = this.create_toolbox();
		_tagToolbar.appendChild(box);
		if (_default == true) {
			var t_fullext = new msTool('Full Extent', _msMap.fullExtent, _iconFullExtentButton);
			var t_pan     = new msTool('Pan', _msMap.setActionPan, _iconPanButton, function(e, map, x, y){map.dragStart(e);});
			var t_zoom    = new msTool('Zoom', _msMap.setActionZoombox, _iconZoomboxButton, function(e, map, x, y){map.zoomStart(e);});
			var t_zoomin  = new msTool('Zoom In', _msMap.setActionZoomIn, _iconZoominButton);
			var t_zoomout = new msTool('Zoom Out', _msMap.setActionZoomOut, _iconZoomoutButton);
			this.addMapTool(t_fullext);
			this.addMapTool(t_pan);
			this.addMapTool(t_zoom);
			this.addMapTool(t_zoomin);
			this.addMapTool(t_zoomout);
			this.redraw();
			this.activateButtons();
		}
	}
