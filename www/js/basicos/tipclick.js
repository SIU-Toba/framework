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
		var tip_div = $('#tipclick_div');		
				
		tip_div.html(content);
		if ((e.type == "click" && tip_div.is(':hidden')) || e.type == "mouseover") {
			var self = this;
			this._show_handler = setTimeout(function() {self._do_show();}, delay);
		} else if (e.type=="click") {
			tip_div.hide();
		}
		
		//Calculamos la posicion del objeto que lanza el evento
		var x = Math.round(this._get_pos_offset(obj, true));
		var y = Math.round(this._get_pos_offset(obj, false));
		
		//Agregamos la clase correspondiente y modificamos la posicion via css
		tip_div.addClass('tooltip')
			   .css('visibility', 'visible')
			   .css('left', x - this._clear_browser_edge(obj, true, x, y ) + "px")
			   .css('top', y - this._clear_browser_edge(obj, false, x, y) + obj.offsetHeight + "px");
		
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
	
	_clear_browser_edge : function(obj, is_horizontal, x , y) {		
		var edge_offset = (is_horizontal) ? parseInt(this.horizontal_offset, 10)*-1 : parseInt(this.vertical_offset, 10)*-1;
		var is_ie = (document.all && !window.opera);
		var window_edge, content_measure;
		if (is_ie) {
			var ie_body = this._ie_body();
		}
		
		var tooltip = document.getElementById("tipclick_div");				//Esto es mas rapido que $('#tipclick_div') para calcular el offset
		if (is_horizontal) {
			window_edge = is_ie ? ie_body.scrollLeft + ie_body.clientWidth-15 : window.pageXOffset+window.innerWidth-15;
			content_measure = tooltip.offsetWidth;
			if (window_edge - x < content_measure) {
				edge_offset= content_measure - obj.offsetWidth;
			}
		} else {
			window_edge = is_ie ? ie_body.scrollTop + ie_body.clientHeight-15 : window.pageYOffset+window.innerHeight-18;
			content_measure= tooltip.offsetHeight;
			if (window_edge - y < content_measure) {
				edge_offset = content_measure + obj.offsetHeight;
			}
		}
		return edge_offset;
	},
	
 	_ie_body : function() {
 		return (document.compatMode && document.compatMode!="BackCompat") ? document.documentElement : document.body;
	 },
	 
 	_do_show : function() {
		$('#tipclick_div').show();
 	},
 	
 	_do_hide : function() {
 		if (this._must_hide) {
			$('#tipclick_div').hide();
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

$('<div />', {id: 'tipclick_div'})
		.on('mouseover', function(){
			if (typeof window.tipclick != 'undefined' && window.tipclick !== null) 
				return window.tipclick._continue();})
		.on('mouseout', function(){
			if (typeof window.tipclick != 'undefined' && window.tipclick !== null) 
				return window.tipclick._stop();})
		.appendTo($(document.body));

if (typeof toba != 'undefined') {
	toba.confirmar_inclusion('basicos/tipclick');
}