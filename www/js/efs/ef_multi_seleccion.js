ef_multi_seleccion.prototype = new ef();
ef_multi_seleccion.prototype.constructor = ef_multi_seleccion;

	/**
	 * @class Elemento que permite la selección de varios valores. Clase base abstracta
	 * @constructor
	 * @phpdoc Componentes/Efs/toba_ef_multi_seleccion toba_ef_multi_seleccion
	 */
	function ef_multi_seleccion(id_form, etiqueta, obligatorio, colapsado, limites) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
		this._limites = limites;
	}

	ef_multi_seleccion.prototype.validar = function() {
		var elemento;
		if (! ef.prototype.validar.call(this)) {
			return false;		
		}
		var valores = this.get_estado();
		if (this._obligatorio && valores.length === 0) {
			this._error = 'es obligatorio.';
		    return false;
		}
		//--- Mínimo
		if (this._limites[0] !== null) {
			if (valores.length < this._limites[0]) {
				elemento = (this._limites[0] == 1) ? "un elemento" : this._limites[0] + " elementos";				
				this._error = "requiere al menos " + elemento + " seleccionados.";
				return false;	
			}
		}
		//--- Máximo
		if (this._limites[1] !== null) {
			if (valores.length > this._limites[1]) {
				elemento = (this._limites[1] == 1) ? "un elemento" : this._limites[1] + " elementos";
				this._error = "no puede tener más de " + elemento + " seleccionados.";
				return false;	
			}
		}
		return true;
	};
	
	ef_multi_seleccion.prototype.set_solo_lectura = function(solo_lectura) {
		if (typeof solo_lectura == 'undefined') {
			solo_lectura = true;
		}
		var input = this.input();
		if (input) {
			input.disabled = solo_lectura;
		}
		var utilerias = document.getElementById(this._id_form + '_utilerias');
		if (utilerias) {
			utilerias.style.display	= (solo_lectura) ? 'none' : '';
		}
	};
	
	/**
	 *	Metodo aún no implementado!!!
	 */
	ef_multi_seleccion.prototype.set_estado = function(nuevo) {
		alert('metodo set_estado del ef_multi_seleccion no implementado');
	};	

	ef_multi_seleccion.prototype.resetear_estado = function() {
		this.seleccionar_todo(false);
	};	
	
// ########################################################################################################
// ########################################################################################################

ef_multi_seleccion_lista.prototype = new ef_multi_seleccion();
ef_multi_seleccion_lista.constructor = ef_multi_seleccion_lista;

	/**
	 * @class Permite la selección de varios valores a partir de una lista. Equivale al tag <em>select multiple</em> en HTML
	 * @constructor
	 * @phpdoc Componentes/Efs/toba_ef_multi_seleccion_lista toba_ef_multi_seleccion_lista
	 */
	function ef_multi_seleccion_lista(id_form, etiqueta, obligatorio, colapsado, limites) {
		ef_multi_seleccion.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado, limites);
	}

	/**
	 * Retorna los valores actualmente seleccionados en formato de arreglo
	 * @type Array
	 */	
	ef_multi_seleccion_lista.prototype.get_estado = function() {
		valores = [];
		var opciones = this.input().options;
		for (var i=0; i < opciones.length ; i++) {
			if (opciones[i].selected) {
				valores.push(opciones[i].value);	
			}
		}
		return valores;
	};	
	
	ef_multi_seleccion_lista.prototype.borrar_opciones = function() {
		this.input().length = 0;
	};
	
	/**
	 * Cambia las opciones disponibles para seleccionar
	 * @param valores Objeto asociativo id=>valor
	 */		
	ef_multi_seleccion_lista.prototype.set_opciones = function(valores) {
		this.borrar_opciones();
		var input = this.input();
		for (id in valores){
			input.options[input.options.length] = new Option(valores[id], id);
		}
	};
	
	/**
	 * Selecciona todos o ningún los elementos de la lista
	 * @param {boolean} todos True seleciona todos, false ninguno
	 */
	ef_multi_seleccion_lista.prototype.seleccionar_todo = function(todos) {
		var elem = this.input();
		for (var i=0; i < elem.length; i++) {
			elem.options[i].selected = todos;
		}
	};
	
	
// ########################################################################################################
// ########################################################################################################

ef_multi_seleccion_check.prototype = new ef_multi_seleccion();
ef_multi_seleccion_check.constructor = ef_multi_seleccion_check;

	/**
	 * @class Permite la selección de varios valores a partir de un conjunto de checkboxes
	 * @constructor
	 * @phpdoc Componentes/Efs/toba_ef_multi_seleccion_check toba_ef_multi_seleccion_check
	 */
	function ef_multi_seleccion_check(id_form, etiqueta, obligatorio, colapsado, limites, cant_columnas) {
		ef_multi_seleccion.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado, limites);
		this._cant_columnas = cant_columnas;
	}
	
	/**
	 * Retorna los valores actualmente seleccionados en formato de arreglo
	 * @type Array
	 */
	ef_multi_seleccion_check.prototype.get_estado = function() {
		valores = [];
		var elem = this.get_elementos();
		for (var i=0; i < elem.length; i++) {
			if (elem[i].type == 'hidden') {
				valores.push(elem[i].value);
			}
			if (elem[i].checked) {
				valores.push(elem[i].value);
			}
		}
		return valores;
	};	

	/**
	 * Retorna referencias a los distintos checkboxes que componen el elemento
	 * @type array de <a href=http://developer.mozilla.org/en/docs/DOM:element>elements</a>
	 */
	ef_multi_seleccion_check.prototype.get_elementos = function() {
		var elem = document.getElementsByName(this._id_form + '[]');
		if (elem.length) {
			return elem;
		} else {
			return [elem];	
		}		
	};
	
	ef_multi_seleccion_check.prototype.set_solo_lectura = function(solo_lectura) {
		solo_lectura = (typeof solo_lectura != 'undefined' && solo_lectura);		
		var elem = this.get_elementos();
		for (var i=0; i < elem.length; i++) {
			elem[i].disabled = solo_lectura;
		}
		ef_multi_seleccion.prototype.set_solo_lectura.call(this, solo_lectura);
	};
	
	/**
	 * Selecciona todos o ningún los elementos de la lista
	 * @param {boolean} todos True seleciona todos, false ninguno
	 */	
	ef_multi_seleccion_check.prototype.seleccionar_todo = function(todos) {
		var elem = this.get_elementos();
		for (var i=0; i < elem.length; i++) {
			elem[i].checked = todos;
		}
	};
	
	ef_multi_seleccion_check.prototype.borrar_opciones = function() {
		var opciones = document.getElementById(this._id_form + '_opciones');
		while(opciones.childNodes[0]) {
			opciones.removeChild(opciones.childNodes[0]);
		}
	};
	
	/**
	 * Cambia las opciones disponibles para seleccionar
	 * @param valores Objeto asociativo id=>valor
	 */		
	ef_multi_seleccion_check.prototype.set_opciones = function(valores) {
		this.borrar_opciones();
		var opciones = document.getElementById(this._id_form + '_opciones');		
		var nuevo = "<table>";
		var i = 0;
		for (clave in valores) {
    		if (i % this._cant_columnas === 0) {
    			nuevo += "<tr>\n";	
    		}			
			var id = this._id_form + i;
			nuevo += "<td><label class='ef-multi-check' for='" + id + "'>";
			nuevo += "<input name='" + this._id_form + "[]' type='checkbox' value='" + clave + "' id='" + id +"' class='ef-checkbox'/>";
			nuevo += valores[clave] + "</label></td>\n"; 
			i++;
    		if (i % this._cant_columnas === 0) {
    			nuevo += "</tr>\n";	
    		}			
		}
		nuevo += '</table>';		
		opciones.innerHTML = nuevo;
		if (typeof this._callback != 'undefined') {
			this.cuando_cambia_valor(this._callback);
		}
	};
	
	ef_multi_seleccion_check.prototype.cuando_cambia_valor = function(callback) {
		this._callback = callback;
		var elem = this.get_elementos();
		for (var i=0; i < elem.length; i++) {
			addEvent(elem[i], 'onchange', callback);
		}
	};
	
	ef_multi_seleccion_check.prototype.set_tab_index = function(tab_index) {
		var elem = this.get_elementos();
		if (elem.length > 0) {
			elem[0].tabIndex = tab_index;
		}
	};
	

	
// ########################################################################################################
// ########################################################################################################

ef_multi_seleccion_doble.prototype = new ef_multi_seleccion();
ef_multi_seleccion_doble.constructor = ef_multi_seleccion_doble;	


	/**
	 * @class Permite la selección de varios valores a partir de una lista doble, pasando los elementos de un lado hacia el otro
	 * @constructor
	 * @phpdoc Componentes/Efs/toba_ef_multi_seleccion_doble toba_ef_multi_seleccion_doble
	 */
	function ef_multi_seleccion_doble(id_form, etiqueta, obligatorio, colapsado, limites, imgs) {
		ef_multi_seleccion.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado, limites);
		this._imgs = imgs;
		this._callback = null;
	}

	
	/**
	 * Retorna la referencia a uno de los dos input html que componen el componente (por defecto el de la derecha)
	 * @param {string} cual Derecha ('der') o Izquierda ('izq')
	 * @type <a href=http://developer.mozilla.org/en/docs/DOM:element>element</a>
	 */
	ef_multi_seleccion_doble.prototype.input = function(cual) {
		cual = (typeof cual == 'undefined' || cual == 'der') ? '' : '_izq';
		return document.getElementById(this._id_form + cual);		
	};
		
	/**
	 * Retorna los valores actualmente seleccionados en formato de arreglo
	 * @type Array
	 */	
	ef_multi_seleccion_doble.prototype.get_estado = function() {
		valores = [];
		var opciones = this.input('der').options;
		for (var i=0; i < opciones.length ; i++) {
			valores.push(opciones[i].value);	
		}
		return valores;
	};
	
	/**
	 * Selecciona todos o ningún los elementos de la lista
	 * @param {boolean} todos True seleciona todos, false ninguno
	 */
	ef_multi_seleccion_doble.prototype.seleccionar_todo = function(todos) {
		if (todos) {
			this.pasar_a_derecha();
		} else {
			this.pasar_a_izquierda();
		}
	};	
	
	/**
	 *	Selecciona todos los elementos de la lista
	 */
	ef_multi_seleccion_doble.prototype.pasar_a_derecha = function() {
		this._pasar_seleccionados('izq', 'der');
	};

	/**
	 *	Deselecciona todos los elementos de la lista
	 */	
	ef_multi_seleccion_doble.prototype.pasar_a_izquierda = function() {
		this._pasar_seleccionados('der', 'izq');
	};	
	
	/**
	 *	@private
	 */
	ef_multi_seleccion_doble.prototype._pasar_seleccionados = function(desde, hasta) {
		var i_desde = this.input(desde);
		var i_hasta = this.input(hasta);
		var actual = i_desde.selectedIndex;
		while (actual != -1) {
			var opcion = i_desde.options[actual];
			try {
				i_hasta.add(opcion, null);
			} catch (e) {
				//Bug de IE
				var nueva = document.createElement("OPTION");
				nueva.value = opcion.value;
				nueva.text = opcion.text;
				i_hasta.add(nueva);
				i_desde.remove(actual);
			}
			actual = i_desde.selectedIndex;
		}
		this.refrescar_todo();
	};
	
	/**
	 *	@private
	 */	
	ef_multi_seleccion_doble.prototype.refrescar_todo = function()	{
		this.refrescar_iconos('izq');
		this.refrescar_iconos('der');
		if (this._callback !== null) {
			eval(this._callback);	
		}
	};
	
	/**
	 *	@private
	 */	
	ef_multi_seleccion_doble.prototype.refrescar_iconos = function(posicion) {
		var input = this.input(posicion);
		var img = document.getElementById(this._id_form + '_img_' + posicion);
		var offset = (posicion == 'izq') ? 0 : 2;
		if (input.selectedIndex != -1) {
			img.src = this._imgs[1 + offset];
		} else {
			img.src = this._imgs[0 + offset];
		}
	};
	
	/**
	 * Cambia las opciones disponibles para seleccionar
	 * @param valores Objeto asociativo id=>valor
	 */			
	ef_multi_seleccion_doble.prototype.set_opciones = function(valores) {
		this.borrar_opciones();
		var input = this.input('izq');
		for (id in valores){
			input.options[input.options.length] = new Option(valores[id], id);
		}
	};
	
	ef_multi_seleccion_doble.prototype.set_solo_lectura = function(solo_lectura) {
		if (typeof solo_lectura == 'undefined') {
			solo_lectura = true;
		}
		this.input('izq').selectedIndex = -1;			
		this.input('izq').disabled = solo_lectura;
		this.input('der').selectedIndex = -1;		
		this.input('der').disabled = solo_lectura;
		this.refrescar_iconos('izq');
		this.refrescar_iconos('der');		
	};
		
	ef_multi_seleccion_doble.prototype.borrar_opciones =  function() {
		this.input('izq').length = 0;
		this.input('der').length = 0;
	};

	ef_multi_seleccion_doble.prototype.submit = function () {
		var input = this.input('der');
		if (input && input.disabled) {
			input.disabled = false;
		}
		var opciones = this.input('der').options;
		for (var i=0; i < opciones.length ; i++) {
			opciones[i].selected = true;
		}
	};
		
	ef_multi_seleccion_doble.prototype.cuando_cambia_valor = function(callback) {
		this._callback = callback;
	};	
	
	ef_multi_seleccion_doble.prototype.set_tab_index = function(tab_index) {
		this.input('izq').tabIndex = tab_index;
	};
		
	
toba.confirmar_inclusion('efs/ef_multi_seleccion');