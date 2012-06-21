var tipclick = {
 	//---Public
	vertical_offset : '-3px',
	horizontal_offset : '-3px',
	delay_disappear : 250,
	delay_appear : 1000,

	show : function(content, obj, e, delay) {
		if (typeof delay == 'undefined') {
			delay = this.delay_appear;	
		}
		if (window.event) { 
			event.cancelBubble=true;
		} else if (e.stopPropagation) {
			e.stopPropagation();
		}
		this._do_hide();
		var tooltip = document.getElementById("tipclick_div");
		tooltip.innerHTML = content;
		if ((e.type == "click" && tooltip.style.visibility == 'hidden') || e.type == "mouseover") {
			var self = this;
			this._show_handler = setTimeout(function() {self._do_show();}, delay);
		} else if (e.type=="click") {
			tooltip.style.visibility = 'hidden';
		}
		tooltip.x = this._get_pos_offset(obj, true);
		tooltip.y = this._get_pos_offset(obj, false);
		tooltip.style.left = tooltip.x - this._clear_browser_edge(obj, true) + "px";
		tooltip.style.top = tooltip.y - this._clear_browser_edge(obj, false) + obj.offsetHeight + "px";
		return true;
	},

	hide : function() {
		this._must_hide = true;
		var self = this;
 		this._hide_handler = setTimeout(function() { self._do_hide(); }, this.delay_disappear);
 		return true;
	},
	
	//---Private
	_show_handler : null,
	_hide_handler : null,
	_must_hide : true,

	_clear_tip : function() {
		if (typeof this._hide_handler != "undefined" || this._hide_handler !== null) {
			clearTimeout(this._hide_handler);
			delete(this._hide_handler);
		}
		if (typeof this._show_handler != "undefined" || this._show_handler !== null) {
			clearTimeout(this._show_handler);
			delete(this._show_handler);
		}
	},
	
	_get_pos_offset : function(what, is_left) {
		if (is_left) {  
			return $(what).offset().left;
		} else {
			return $(what).offset().top;
		}
	},
	
	_clear_browser_edge : function(obj, is_horizontal) {
		var tooltip = document.getElementById("tipclick_div");
		var edge_offset = (is_horizontal) ? parseInt(this.horizontal_offset, 10)*-1 : parseInt(this.vertical_offset, 10)*-1;
		var is_ie = document.all && !window.opera;
		var window_edge, content_measure;
		if (is_ie) {
			var ie_body = this._ie_body();
		}
		if (is_horizontal) {
			window_edge = is_ie ? ie_body.scrollLeft + ie_body.clientWidth-15 : window.pageXOffset+window.innerWidth-15;
			content_measure = tooltip.offsetWidth;
			if (window_edge - tooltip.x < content_measure) {
				edge_offset= content_measure - obj.offsetWidth;
			}
		} else {
			window_edge = is_ie ? ie_body.scrollTop + ie_body.clientHeight-15 : window.pageYOffset+window.innerHeight-18;
			content_measure= tooltip.offsetHeight;
			if (window_edge - tooltip.y < content_measure) {
				edge_offset = content_measure + obj.offsetHeight;
			}
		}
		return edge_offset;
	},
	
 	_ie_body : function() {
 		return (document.compatMode && document.compatMode!="BackCompat") ? document.documentElement : document.body;
	 },
	 
 	_do_show : function() {
	 	document.getElementById("tipclick_div").style.visibility="visible";
 	},
 	
 	_do_hide : function() {
 		if (this._must_hide) {
			document.getElementById("tipclick_div").style.visibility="hidden";
 		}
		this._clear_tip();
 	},
 	
 	_continue : function() {
 		this._must_hide = false;
 	},
 	
 	_stop : function() {
 		this._must_hide = true;
 		this.hide();
 	}
};

var html = '<div id="tipclick_div" onmouseover="if (typeof window.tipclick != \'undefined\' && window.tipclick !== null) return window.tipclick._continue()" onmouseout="if (typeof window.tipclick != \'undefined\' && window.tipclick !== null) return window.tipclick._stop()"></div>';
if (typeof pagina_cargada != 'undefined' && pagina_cargada) {
	document.body.innerHTML += html;	
} else {
	document.write(html);
}
if (typeof toba != 'undefined') {
	toba.confirmar_inclusion('basicos/tipclick');
}