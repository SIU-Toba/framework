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

	// pointOverlay class prototype
	function pointOverlay( p_icon, p_infoSkin, p_title, p_x, p_y, p_item_name, p_item_value )
	{
		var _msMap = null;
		var _img   = null;	//document.createElement('img');
		var _shd   = null;	//document.createElement('img');
		var _x     = parseFloat(p_x);	// Real coord X
		var _y     = parseFloat(p_y);	// Real coord Y
		var _title = p_title;
		var _icon  = p_icon;
		var _infoSkin = p_infoSkin;
		var _item_name = p_item_name;
		var _item_value = p_item_value;
		var _offsetX = 0;
		var _offsetY = 0;

		if (_title == null) { _title = 'Info'; }
		if (_infoSkin == null)		{
			// Create a default Info-window icon object...
			_infoSkin = new msInfoSkin( imgDir + '/angolo_a.png',
										imgDir + '/angolo_b.png',
										imgDir + '/angolo_c.png',
										imgDir + '/angolo_d.png',
										imgDir + '/report_t.png',
										imgDir + '/report_d.png',
										imgDir + '/report_l.png',
										imgDir + '/report_r.png',
										imgDir + '/report_x.png',
										imgDir + '/close.png',
										imgDir + '/report_arrow.png' );
		}
		if (_icon == null) {
			_icon = new msIcon(null, null);
		}

		// Functions...
		this.setMap = function(m) { _msMap = m; }
		this.getMap = function()  { return _msMap; }
		this.getImg = function()  { return _img; }
		this.getShd = function()  { return _shd; }
		this.getX   = function()  { return _x; }
		this.getY   = function()  { return _y; }
		this.getHtmlAttributes = function()
		{
			var ret = "<table>";
			for (var j=0; j<_item_name.length; j++) 	{
				// css
				ret += "<tr><td class=\"mscross_report_attr_name\">"+ _item_name[j] +
					 "</td><td class=\"mscross_report_attr_value\" "+
					 "style='padding-left: 8px;'>"+ _item_value[j] +"</td></tr>";
			}
			ret += "</table>";
			return ret;
		}
		this.getInfoX = function() { return _msMap.xReal2pixel(_x); }
		this.getInfoY = function() { return Math.round(_msMap.yReal2pixel(_y) - (parseInt(_img.offsetHeight)/2) ); }
		this.getInfoSkin = function() { return _infoSkin; }
		this.getWidth  = function() { return parseInt(_img.style.width); }
		this.getHeight = function() { return parseInt(_img.style.height); }
		this.redraw = function()
		{
			// se e` visibile (coordinate del punto interne al box della mappa)...
			if ( _msMap.isPointInMap( _x - _msMap.wPixel2real(_offsetX),
									  _y + _msMap.hPixel2real(_offsetY),
									  _msMap.wPixel2real(_offsetX),
									  _msMap.hPixel2real(_offsetY) ) )
			{
				  setPos(_img, _msMap.xReal2pixel(_x) - _offsetX,
							   _msMap.yReal2pixel(_y) - _offsetY);
				  setPos(_shd, _msMap.xReal2pixel(_x) - _offsetX,
							   _msMap.yReal2pixel(_y) - _offsetY);
			} else{ 
				this.setVisible(false);
			}
		}

		this.setVisible = function(p_bool)
		{
			var str = null;
			if (p_bool) {str = '';} else {str = 'none';}
			_img.style.display = str;
			_shd.style.display = str;
		}

		this.showReport = function()
		{
			var pnt = new msReport(this, _title);
			_msMap.setReport(pnt);
		}

		// Initialization...
		_img = _icon.getImage(); _shd = _icon.getShadow();
		_offsetX = _icon.getShiftX() -1;
		_offsetY = _icon.getShiftY() -1;
		_img.objRef = this;

		add_event(_img, 'click', function(event){pointOverlayEvent(event);});
	}


	// pointOverlay Click Event
	function pointOverlayEvent(e)
	{
		var p;
		if (e.srcElement) {
			p = e.srcElement.objRef;
		}
		if (e.target) {
			p = e.target.objRef;
		}
		p.showReport();
	}