//--------------------------------------------------------------------------------
//Clase objeto_ei_arbol
objeto_ei_arbol.prototype = new objeto;
var def = objeto_ei_arbol.prototype;
def.constructor = objeto_ei_arbol;

function objeto_ei_arbol(instancia, input_submit, item_propiedades) {
	this._instancia = instancia;				//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
	this._input_submit = input_submit;			//Campo que se setea en el submit del form
	this._item_propiedades = item_propiedades;
}

	//---Submit
	def.submit = function() {
		var padre_esta_en_proceso = this._ci && !this._ci.en_submit();
		if (padre_esta_en_proceso)
			return this._ci.submit();
		if (this._evento) {
			//Si es la selección de una semana marco la semana
			if (this._evento.id == 'ver_propiedades') {
				document.getElementById(this._input_submit + '__seleccion').value = this._evento.parametros;
			}
			if (this._evento.id == 'sacar_foto') {
				document.getElementById(this._input_submit + '__foto_nombre').value = this._evento.parametros[0];
				document.getElementById(this._input_submit + '__foto_datos').value = this._evento.parametros[1];				
			}
			//Marco la ejecucion del evento para que la clase PHP lo reconozca
			document.getElementById(this._input_submit).value = this._evento.id;			
		}		
	}

	def.ver_propiedades = function(id) {
		this.set_evento( new evento_ei('ver_propiedades', true, '', id));
/*		try	{
			var requester = new XMLHttpRequest();
		} catch (error) {
			try	{
			  var requester = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (error)	{
			  return false;
			}
		}
		var vinculo = toba.crear_vinculo(this._item_propiedades);
		requester.open("GET", vinculo);
		requester.send(null);
		var stateHandler = function()
		{
			if (requester.readyState == 4) {
				if (requester.status == 200) {
					alert(requester.responseText);
				}
				else
					alert('Error conectando usando XMLHttpRequest');
			}
		}
		
		requester.onreadystatechange = stateHandler;*/
	}

	def.sacar_foto = function() {
		var nombre = prompt('Nombre de la foto', 'nombre de la foto');
		if (nombre != null && nombre != '') {
			datos_foto = this.datos_foto();
			var datos_join = [];
			for (id in datos_foto) {
				valor = id;
				if (datos_foto[id])
					valor += '=1';
				else
					valor += '=0';
				datos_join.push(valor);
			}
			var datos_foto_join = datos_join.join('||');
			this.set_evento( new evento_ei('sacar_foto', true, '', [nombre, datos_foto_join]));
		}
	}
	
	def.datos_foto = function() {
		var raiz = document.getElementById(this._instancia + '_nodo_raiz');
		var datos = new Object();
		this.datos_foto_recursivo(raiz, datos);
		ei_arbol(datos);
		return datos;
	}
	
	def.datos_foto_recursivo = function(nodo, datos) {
		if (nodo.getAttribute('id_nodo')) {
			datos[nodo.getAttribute('id_nodo')] = (nodo.style.display != 'none');
		}
		//Recorre los <ul> de este nodo
		for (var i=0; i < nodo.childNodes.length; i++) {
			var hijo = nodo.childNodes[i];
			if (hijo.tagName == 'UL' || hijo.tagName =='LI')
				this.datos_foto_recursivo(hijo, datos);
		}			
	}
	
	def.cambiar_expansion = function(nodo) {
		ul = this.buscar_primer_ul(nodo.parentNode);
		if (ul) {
			if (ul.style.display == 'none') {
				ul.style.display = '';
				nodo.src = toba.imagen('contraer_nodo');				
			} else {
				ul.style.display = 'none';			
				nodo.src = toba.imagen('expandir_nodo');
			}
		}
	}
	
	def.buscar_primer_ul = function(nodo) {
		//Busca el primer <ul> en este nodo y le cambia la visibilidad
		for (var i=0; i < nodo.childNodes.length; i++) {
			if (nodo.childNodes[i].tagName == 'UL')
				return nodo.childNodes[i];
		}	
	}	
