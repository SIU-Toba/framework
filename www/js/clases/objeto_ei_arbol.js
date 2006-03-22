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
			//Marco la ejecucion del evento para que la clase PHP lo reconozca
			document.getElementById(this._input_submit).value = this._evento.id;			
		}		
		document.getElementById(this._input_submit + '__apertura_datos').value = this.datos_apertura();
	}

	def.ver_propiedades = function(id) {
		this.set_evento( new evento_ei('ver_propiedades', true, '', id));
	}

	def.datos_apertura = function() {
		var raiz = document.getElementById(this._instancia + '_nodo_raiz');
		var datos = new Object();
		if (raiz != null)
			this.datos_apertura_recursivo(raiz, datos);
		var datos_join = [];
		for (id in datos) {
			var valor = id;
			if (datos[id])
				valor += '=1';
			else
				valor += '=0';
			datos_join.push(valor);
		}
		return datos_join.join('||');
	}
	
	def.datos_apertura_recursivo = function(nodo, datos) {
		if (nodo.getAttribute('id_nodo')) {
			datos[nodo.getAttribute('id_nodo')] = (nodo.style.display != 'none');
		}
		//Recorre los <ul> de este nodo
		for (var i=0; i < nodo.childNodes.length; i++) {
			var hijo = nodo.childNodes[i];
			if (hijo.tagName && (hijo.tagName == 'UL' || hijo.tagName =='LI'))
				this.datos_apertura_recursivo(hijo, datos);
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

toba.confirmar_inclusion('clases/objeto_ei_arbol');