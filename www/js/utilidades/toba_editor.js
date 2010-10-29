function toba_invocar_editor(frame, url) 
{
	if (frame == 'undefined') {
		frame = 'frame_centro';
	}
	var encontrado = false;
	var rendido = false;
	var sujeto = window;
	//--- Trata de encontrar el frame de edicion
	while (! encontrado && ! rendido) {
		if (sujeto.top && sujeto.top.frame_control && sujeto.top.frame_control.editor) {
			encontrado = true;
			break;
		}
		if (sujeto.opener) {						//Previsuliazion comun
			sujeto = sujeto.opener;
		} else if (sujeto.top && sujeto.top.opener) {				//Previsualizacion de algo con frames
			sujeto = sujeto.top.opener;
		} else if (sujeto.top && sujeto.top.opener && sujeto.top.opener.opener) {		//Popup abierto desde la previsualizacion
			sujeto = sujeto.top.opener.opener;
		} else {
			//-- No hay mas padres, me rindo
			rendido = true;
		}
	}
	if (encontrado) {
		if (typeof url != 'undefined') {
			sujeto.top.frame_control.editor.abrir_editor(frame, url);
		}
		sujeto.focus();
	} else {
		alert('No se puede encontrar un editor de toba abierto, por favor lance nuevamente la previsualización desde toba_editor');
	}
	setTimeout ('editor_cambiar_vinculos(false)', 100);	//Para evitar que quede fijo
	return false;
}


function editor_cambiar_vinculos(set) {
	var automatico = true;			
	if (typeof set != 'undefined') {
		automatico = false;
	}
	var nodos = getElementsByClass('div-editor');
	var mostrar =false;
	for (var i=0; i< nodos.length; i++) {
		if (automatico) {
			set = (nodos[i].className.indexOf('editor-mostrar') == -1);
		}
		if (set) {
			nodos[i].className += ' editor-mostrar';
		} else {
			nodos[i].className = 'div-editor';
		}
	}
}

function editor_cambiar_ajax() {
	var nueva = toba.get_navegacion_ajax() ? false : true;
	toba.set_navegacion_ajax(nueva);
	editor_cambiar_ajax_icono();
	var param_url = nueva ? 1 : 0;
	var vinculo = vinculador.get_url(null, null, null, {navegacion_ajax: param_url, tcm: 'temp'});
	var con = conexion.asyncRequest('GET', vinculo, null, null);				
}

function editor_cambiar_ajax_icono()
{
	var nueva = toba.get_navegacion_ajax();
	var img = $$('editor_ajax').firstChild;
	var nuevo_src;
	if (nueva) {
		nuevo_src = img.src.reemplazar('_off', '_on');
	} else {
		nuevo_src = img.src.reemplazar('_on', '_off');
	}
	img.src = nuevo_src;			
}

function set_editor_on(e) {
   	var id = (window.event) ? event.keyCode : e.keyCode;
	if (id == 17) {
		editor_cambiar_vinculos(true);
		return false;
	}
}

function set_editor_off(e) {
   	var id = (window.event) ? event.keyCode : e.keyCode;
	if (id == 17) {
		editor_cambiar_vinculos(false);
	}
}

agregarEvento(document, 'keyup', set_editor_off);
agregarEvento(document, 'keydown', set_editor_on);
toba.agregar_onload(editor_cambiar_ajax_icono);

toba.confirmar_inclusion('utilidades/toba_editor');