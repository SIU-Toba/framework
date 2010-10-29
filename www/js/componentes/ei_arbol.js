
ei_arbol.prototype = new ei();
ei_arbol.prototype.constructor = ei_arbol;

/**
 * @class Muestra un árbol donde el usuario puede colapsar/descolapsar niveles
 * @constructor
 * @phpdoc Componentes/Eis/toba_ei_arbol toba_ei_arbol
 */
function ei_arbol(instancia, input_submit, autovinculo) {
	this._instancia = instancia;				//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
	this._input_submit = input_submit;			//Campo que se setea en el submit del form
	this._autovinculo = autovinculo;
	this._ultimo_filtro = null;						//Ultimo criterio de filtrado
}

	//---Submit
	ei_arbol.prototype.submit = function() {
		var padre_esta_en_proceso = this.controlador && !this.controlador.en_submit();
		if (padre_esta_en_proceso) {
			return this.controlador.submit();
		}
		if (this._evento) {
			//Si es la selección de una semana marco la semana
			if (this._evento.id == 'ver_propiedades') {
				document.getElementById(this._input_submit + '__seleccion').value = this._evento.parametros;
			}
			//Marco la ejecucion del evento para que la clase PHP lo reconozca
			document.getElementById(this._input_submit).value = this._evento.id;			
		}		
		document.getElementById(this._input_submit + '__apertura_datos').value = this.datos_apertura();
	};

	/**
	 * Dispara en el servidor el evento ver_propiedades sobre un id especifico de nodo
	 * @param {string} Id Identificador del nodo que se quiere ver mas propiedades
	 */
	ei_arbol.prototype.ver_propiedades = function(id) {
		this.set_evento( new evento_ei('ver_propiedades', true, '', id));
	};

	/**
	 *	@private
	 */
	ei_arbol.prototype.datos_apertura = function() {
		var raiz = document.getElementById(this._instancia + '_nodo_raiz');
		var datos = {};
		if (raiz !== null) {
			this.datos_apertura_recursivo(raiz, datos);
		}
		var datos_join = [];
		for (id in datos) {
			var valor = id;
			if (datos[id]) {
				valor += '=1';
			} else {
				valor += '=0';
			}
			datos_join.push(valor);
		}
		return datos_join.join('||');
	};
	
	/**
	 *	@private
	 */
	ei_arbol.prototype.datos_apertura_recursivo = function(nodo, datos) {
		if (nodo.getAttribute('id_nodo')) {
			datos[nodo.getAttribute('id_nodo')] = (nodo.style.display != 'none');
		}
		//Recorre los <ul> de este nodo
		for (var i=0; i < nodo.childNodes.length; i++) {
			var hijo = nodo.childNodes[i];
			if (hijo.tagName && (hijo.tagName == 'UL' || hijo.tagName =='LI')) {
				this.datos_apertura_recursivo(hijo, datos);
			}
		}			
	};
	
	/**
	 * Invierte la expansión de un nodo especifico del arbol
	 * @param {Element} nodo Nodo HTML a expandir
	 */
	ei_arbol.prototype.cambiar_expansion = function(nodo) {
		var ul = this.buscar_primer_ul(nodo.parentNode);
		if (ul && ul.getAttribute('id_nodo')) {
			if (ul.innerHTML === '') {
				var id_nodo = ul.getAttribute('id_nodo');
				var callback =
				{
				  success: this.retorno_expansion,
				  failure: toba.error_comunicacion,
				  scope: this,
				  argument: nodo
				};
				var vinculo = vinculador.concatenar_parametros_url(this._autovinculo, {'id_nodo':id_nodo});
				conexion.asyncRequest('GET', vinculo, callback, null);
			} else {
				this.toggle_expansion(nodo, ul);
			}
		}
	};
	
	/**
	 * Dado un nodo UL abre el mismo
	 * @param {Element} nodo Nodo HTML a expandir
	 */
	ei_arbol.prototype.abrir_nodo = function(nodo) {
		var ul = this.buscar_primer_ul(nodo);
		if (ul.style.display == 'none') {
			var img = nodo.firstChild;
			ul.style.display = '';
			img.src = toba.imagen('contraer_nodo');				
		}		
	};	
	
	/**
	 * Callback de retorno del pedido de expansion AJAX
	 * @private
	 */
	ei_arbol.prototype.retorno_expansion = function(resultado)
	{
		var nodo =resultado.argument;
		var ul = this.buscar_primer_ul(nodo.parentNode);		
		if (ul) {
			ul.innerHTML = resultado.responseText;					
			this.toggle_expansion(nodo,ul);
		}
		return true;
	};
	
	/**
	 * @private
	 */
	ei_arbol.prototype.toggle_expansion = function(nodo, ul)
	{
		if (ul.style.display == 'none') {
			ul.style.display = '';
			nodo.src = toba.imagen('contraer_nodo');				
		} else {
			ul.style.display = 'none';			
			nodo.src = toba.imagen('expandir_nodo');
		}
	};

	/**
	 * Busca el primer <ul> en este nodo y le cambia la visibilidad
	 * @private
	 */	
	ei_arbol.prototype.buscar_primer_ul = function(nodo) {
		for (var i=0; i < nodo.childNodes.length; i++) {
			if (nodo.childNodes[i].tagName == 'UL') {
				return nodo.childNodes[i];
			}
		}	
	};

	/**
	 * Busca el <li> anterior a uno pasado como parametro
	 * @private
	 */	
	ei_arbol.prototype.buscar_li_previo = function(li) {
		var temp = li;
		while (true) {
			temp = temp.previousSibling;
			if ( temp === null || temp.tagName == 'LI' ) {
				return temp;
			}
		}
	};

	/**
	 * Busca el <li> siguiente a uno pasado como parametro
	 * @private
	 */	
	ei_arbol.prototype.buscar_li_siguiente = function(li) {
		var temp = li;
		while (true) {
			temp = temp.nextSibling;
			if ( temp === null || temp.tagName == 'LI' ) {
				return temp;
			}
		}
	};

	/**
	 * Busca el nodo raiz del arbol
	 * @type Element
	 */
	ei_arbol.prototype.get_nodo_raiz = function(nombre) {
		return $$(this._instancia + '_nodo_raiz');
	};	

	/**
	 * Busca los nodos hijos del padre especificado
	 * @param {Element} padre
	 * @type array(Element)
	 */
	ei_arbol.prototype.get_nodos_hijo = function(padre) {
		var hijos = [];
		for (var i=0; i < padre.childNodes.length; i++) {
			if (padre.childNodes[i].tagName == 'UL') {
				hijos = hijos.concat(this.get_nodos_hijo(padre.childNodes[i]));
			}
			if (padre.childNodes[i].tagName == 'LI') {
				hijos.push(padre.childNodes[i]);
			}	
		}
		return hijos;
	};		
	
	/**
	 * Retorna el nombre del nodo
	 * @param {Element} nodo
	 * @type string
	 */
	ei_arbol.prototype.get_nombre_nodo = function(nodo) {
		var nombre = nodo.childNodes[3].nodeValue;
		if (! isset(nombre)) {
			//Puede estar en un <a>
			if (nodo.childNodes[3].tagName == 'A') {
				nombre = nodo.childNodes[3].innerHTML;
			}
		}
		return nombre;
	};

	/**
	 * Filtra la apertura de nodos de acuerdo al nombre indicado
	 * @param {Element} nodo
	 * @param {string} nombre
	 */
	ei_arbol.prototype.filtrar_nodo_por_nombre = function(nodo, nombre) {
		var visible = false;
		var hijos = this.get_nodos_hijo(nodo);
		for(var i=0; i < hijos.length; i++) {
			if (this.filtrar_nodo_por_nombre(hijos[i], nombre)) {
				visible = true;
			}
		}
		//-- Si los hijos no son visibles, quizas si lo es el mismo
		if (! visible) {
			var nombre_nodo = this.get_nombre_nodo(nodo);
			if (quitar_acentos(nombre_nodo.toLowerCase()).indexOf(nombre) != -1) {
				visible = true;
			}
		} else {
			//Si algún hijo está visible, abrir el nodo
			this.abrir_nodo(nodo);
		}
		if (visible) {
			nodo.style.display = '';
		} else {
			nodo.style.display = 'none';
		}
		return visible;
	};	
	
	ei_arbol.prototype.filtro_foco = function() {
		$$(this._input_submit + '_filtro_rapido').value = '';
	};
	
	ei_arbol.prototype.filtro_cambio = function() {
		var filtro = $$(this._input_submit + '_filtro_rapido').value;
		filtro = filtro.toLowerCase().trim();
		filtro = quitar_acentos(filtro);
		if (filtro !== '' || isset(this._ultimo_filtro)) {
			this.filtrar_nodo_por_nombre(this.get_nodo_raiz(), filtro);
			this._ultimo_filtro = filtro;
		}
	};
	
	ei_arbol.prototype.filtro_salir = function() {
		this.filtro_cambio();
		var filtro = $$(this._input_submit + '_filtro_rapido').value;
		if (filtro === '') {
			$$(this._input_submit + '_filtro_rapido').value = 'Buscar...';
			this._ultimo_filtro = null;
		}
	};	

toba.confirmar_inclusion('componentes/ei_arbol');