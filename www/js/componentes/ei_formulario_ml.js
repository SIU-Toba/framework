
ei_formulario_ml.prototype = new ei_formulario();
ei_formulario_ml.prototype.constructor = ei_formulario_ml;

/**
 * @class Un formulario multilínea (ei_formulario_ml) presenta una grilla de campos repetidos una cantidad dada de filas permitiendo recrear la carga de distintos registros con la misma estructura. 
 * La definición y uso de la grilla de campos es similar al formulario simple con el agregado de lógica para manejar un número arbitrario de filas.
 * Contiene una serie de ca campos se los denomina Elementos de Formulario (efs).
 * @see ef
 * @constructor
 * @phpdoc Componentes/Eis/toba_ei_formulario_ml toba_ei_formulario_ml
 */
function ei_formulario_ml(id, instancia, rango_tabs, input_submit, filas, 
								proximo_id, seleccionada, en_linea, maestros, esclavos, invalidos) {
	ei_formulario.prototype.constructor.call(this, id, instancia, rango_tabs, 
													input_submit, maestros, esclavos, invalidos);
	this._filas = filas;					//Carga inicial de las filas
	this._proximo_id = proximo_id;
	this._pila_deshacer = [];		//Pila de acciones a deshacer
	this._ef_con_totales = {};		//Lisa de efs que quieren sumarizar
	this._seleccionada = seleccionada;
	this._agregado_en_linea = en_linea;
}

	ei_formulario_ml.prototype.iniciar = function() {
		//Iniciar las filas
		for (fila in this._filas) {
			this.iniciar_fila(this._filas[fila], false);
		}
		//Agregar totales
		for (var id_ef in this._ef_con_totales) {
			this.agregar_procesamiento(id_ef);
		}
		this.agregar_procesamientos();
		this.refrescar_procesamientos(true);
		this.reset_evento();
	};

	/**
	 *	@private
	 */
	ei_formulario_ml.prototype.iniciar_fila = function (fila, agregar_tabindex) {
		for (id_ef in this._efs) {
			var ef = this._efs[id_ef].ir_a_fila(fila);
			if (this._invalidos[fila] && this._invalidos[fila][id_ef]) {
				this._efs[id_ef].resaltar(this._invalidos[fila][id_ef]);
			}			
			ef.iniciar(id_ef, this);
			if (agregar_tabindex) {
				ef.set_tab_index(this._rango_tabs[0]);
				this._rango_tabs[0]++;
			}
			ef.cuando_cambia_valor(this._instancia + '.validar_fila_ef(' + fila + ',"' + id_ef + '", true)');			
		}
	};	

	/**
	 *	Indica que un ef totalize los valores en todas sus filas colocandolo en la última fila
	 */
	ei_formulario_ml.prototype.agregar_total = function (id_ef) {
		this._ef_con_totales[id_ef] = true;
	};
		
	//----Consultas 
	/**
	 * Retorna las filas actuales del formulario
	 * Útil para hacer recorridos por todas las filas, por ejemplo
	 * <pre>
	 * var filas = this.filas()
	 * for (id_fila in filas) {
	 *      this.ef('nombre').ir_a_fila(filas[id_fila]).set_estado('nuevo');
	 *      this.seleccionar(filas[id_fila]);
	 * }
	 */
	ei_formulario_ml.prototype.filas = function () { return this._filas; };
	
	/**
	 * @private
	 */
	ei_formulario_ml.prototype.procesar = function (id_ef, fila, es_inicial, es_particular) {
		if (typeof es_particular == 'undefined') {
			es_particular = true;	
		}
		if (this.hay_procesamiento_particular_ef(id_ef)) {
			this['evt__' + id_ef + '__procesar'](es_inicial, fila);		 //Procesamiento particular
		}
		if (es_particular && id_ef in this._ef_con_totales) {
			return this.cambiar_total(id_ef, this.total(id_ef)); //Procesamiento por defecto
		}
	};

	/**
	 * Función de calculo de total por defecto, suma el valor de cada fila
	 */
	ei_formulario_ml.prototype.total = function (id_ef) {
		var total = 0;	
		for (fila in this._filas) {
			valor = this._efs[id_ef].ir_a_fila(this._filas[fila]).get_estado();
			valor = (valor === '' || isNaN(valor)) ? 0 : valor;
			total += valor;
		}
		return total;
	};
	
	//----Validación 
	ei_formulario_ml.prototype.validar = function() {
		var ok = true;
		var validacion_particular = 'evt__validar_datos';
		if(this._evento && this._evento.validar) {
			if (existe_funcion(this, validacion_particular)) {
				ok = this[validacion_particular]();
			}
			for (id_fila in this._filas) {
				ok = this.validar_fila(id_fila) && ok;
			}
		}
		return ok;
	};
	
	/**
	 * @private
	 */
	ei_formulario_ml.prototype.validar_fila = function(id_fila) {
		ok = true;
		for (id_ef in this._efs) {
			ok = this.validar_fila_ef(this._filas[id_fila], id_ef) && ok;
		}
		return ok;
	};

	/**
	 * @private
	 */	
	ei_formulario_ml.prototype.validar_fila_ef = function(fila, id_ef, es_online) {
		var ef = this._efs[id_ef].ir_a_fila(fila);
		var validacion_particular = 'evt__' + id_ef + '__validar';
		var es_valido = ef.validar();
		if (existe_funcion(this, validacion_particular)) {
			es_valido = this[validacion_particular](fila) && es_valido;
		}
		if (!es_valido) {
			if (! this._silencioso) {
				ef.resaltar(ef.get_error(), 8);
			}
			if (! es_online) {
				notificacion.agregar(ef.get_error(), 'error', ef._etiqueta);
			}
			ef.resetear_error();
			return false;
		}		
		ef.no_resaltar();
		return true;
	};
	
	ei_formulario_ml.prototype.resetear_errores = function() {
		if (! this._silencioso)	 {
			for (fila in this._filas) {
				for (id_ef in this._efs) {
					this._efs[id_ef].ir_a_fila(this._filas[fila]).no_resaltar();
				}
			}
		}
	};	
	
	//----Submit 
	ei_formulario_ml.prototype.submit = function() {
		//Si no es parte de un submit general, dispararlo
		if (this.controlador && !this.controlador.en_submit()) {
			return this.controlador.submit();
		}
		if (this._evento) {
			for (fila in this._filas) {
				for (id_ef in this._efs) {
					this._efs[id_ef].ir_a_fila(this._filas[fila]).submit();
				}
			}
			//--- Caso particular, si tiene parametros o es un pedido de fila nueva, igualmente se dispara el implicito
			if (this._evento_implicito && (this._evento.parametros || this._evento.id == 'pedido_registro_nuevo')) {
				document.getElementById(this._input_submit + "_implicito").value = this._evento_implicito.id;
			}
			
			//Si tiene parametros los envia
			if (this._evento.parametros) {
				document.getElementById(this._instancia + '__parametros').value = this._evento.parametros;
			}
			var lista_filas = this._filas.join('_');
			document.getElementById(this._instancia + '_listafilas').value = lista_filas;
			//Marco la ejecucion del evento para que la clase PHP lo reconozca
			document.getElementById(this._input_submit).value = this._evento.id;
			return true;
		}
	};
	
	//---- Cascadas
	ei_formulario_ml.prototype.cascadas_cambio_maestro = function(id_ef)
	{
		var actual = this.ef(id_ef).get_fila_actual();
		for (var ef in this._efs) {
			this._efs[ef].ir_a_fila(actual);
		}
		ei_formulario.prototype.cascadas_cambio_maestro.call(this, id_ef);
	};	
	
	//----Selección 
	/**
	 * Marca una fila como seleccionada, cambiando su color de fondo 
	 */
	ei_formulario_ml.prototype.seleccionar = function(fila) {
		if  (fila != this._seleccionada) {
			this.deseleccionar_actual();
			this._seleccionada = fila;
			this.refrescar_seleccion();
		}
	};
	
	/**
	 * Deselecciona cualquier seleccion anterior de fila
	 * @see #seleccionar
	 */
	ei_formulario_ml.prototype.deseleccionar_actual = function() {
		if (isset(this._seleccionada)) {	//Deselecciona el anterior
			var fila = document.getElementById(this._instancia + '_fila' + this._seleccionada);
			cambiar_clase(fila.cells, 'ei-ml-fila');			
			delete(this._seleccionada);
		}
	};
	
	/**
	 *	Toma la fila actualmente seleccionada y la intercambia en orden con la fila anterior en orden
	 */
	ei_formulario_ml.prototype.subir_seleccionada = function () {
		//Busco las posiciones a intercambiar
		var pos_anterior = null;
		for (posicion in this._filas) {
			if (this._seleccionada == this._filas[posicion]) {
				pos_selec = posicion;
				break;
			}
			pos_anterior = posicion;
		}
		if (pos_anterior !== null) {
			this.intercambiar_filas(pos_anterior, pos_selec);
			this.refrescar_numeracion_filas();
		}
	};
	
	/**
	 *	Toma la fila actualmente seleccionada y la intercambia en orden con la fila posterior en orden
	 */	
	ei_formulario_ml.prototype.bajar_seleccionada = function () {
		//Busco las posiciones a intercambiar
		var pos_siguiente = null;
		for (posicion = this._filas.length - 1; posicion >= 0; posicion--) {
			if (this._seleccionada == this._filas[posicion]) {
				pos_selec = posicion;
				break;
			}
			pos_siguiente = posicion;
		}
		if (pos_siguiente !== null) {
			this.intercambiar_filas(pos_selec, pos_siguiente);
			this.refrescar_numeracion_filas();
		}
	};

	/**
	 * Intercambia de posicion a dos filas dadas
	 * @param {int} pos_a Posicion que ocupa la primer fila
	 * @param {int} pos_b Posicion que ocupa la segunda fila
	 */
	ei_formulario_ml.prototype.intercambiar_filas = function (pos_a, pos_b) {
		//Reemplazo en el DOM
		var nodo_padre = document.getElementById(this._instancia + '_fila' + this._filas[pos_a]);
		var nodo_selecc = document.getElementById(this._instancia + '_fila' + this._filas[pos_b]);
		intercambiar_nodos(nodo_selecc, nodo_padre);
		
		//Reemplazo de los tabs index
		for (id_ef in this._efs) {
			var tab_a = this._efs[id_ef].ir_a_fila(this._filas[pos_a]).get_tab_index();
			var tab_b = this._efs[id_ef].ir_a_fila(this._filas[pos_b]).get_tab_index();
			this._efs[id_ef].ir_a_fila(this._filas[pos_a]).set_tab_index(tab_b);
			this._efs[id_ef].ir_a_fila(this._filas[pos_b]).set_tab_index(tab_a);			
		}
		
		//Reemplazo interno 
		var temp = this._filas[pos_a];
		this._filas[pos_a] = this._filas[pos_b];
		this._filas[pos_b] = temp;
	};

	//---ABM 
	/**
	 * Elimina del formulario la fila actualmente seleccionada
	 * El HTML solo se oculta, no se elimina, con lo cual puede ser recuperado en su estado actual
	 */
	ei_formulario_ml.prototype.eliminar_seleccionada = function() {
		var fila = this._seleccionada;
		if(existe_funcion(this, "evt__baja")){
			if(! ( this.evt__baja(fila) ) ){
				return false;
			}
		}	
		anterior = this.eliminar_fila(fila);
		delete(this._seleccionada);
		if (anterior !== null) {
			this.seleccionar(anterior);
		}
		this.refrescar_todo();
	};
	
	/**
	 * Elimina una fila y retorna la fila anterior en orden
	 * @param {string} fila Id. de la fila a eliminar
	 * @type string
	 */
	ei_formulario_ml.prototype.eliminar_fila = function(fila) {
			//'Elimina' la fila en el DOM
		var id_fila = this._instancia + '_fila' + fila;
		var id_deshacer = this._instancia + '_deshacer';
		cambiar_clase(document.getElementById(id_fila).cells, 'ei-ml-fila');
		document.getElementById(id_fila).style.display = 'none';
			//Elimina la fila en la lista interna
		for (i in this._filas) { 
			if (this._filas[i] == fila) {
				this._filas.splice(i, 1); 
				break;
			}
			var anterior = this._filas[i];		
		}
			//Crea función de deshacer
		this._pila_deshacer.push(new Function (
								'document.getElementById("' + id_fila + '").style.display = ""\n' +
								this._instancia + '._filas.splice(' + i + ',0,"' + fila + '")\n'));
		return anterior;
	};
	
	/**
	 * Agrega una nueva fila a la grilla.
	 * Dependiendo de la definición del componente en el editor, este método crea la fila directamente en javascript o
	 * lo hace a través de un evento 'pedido_registro_nuevo' en el servidor
	 */
	ei_formulario_ml.prototype.crear_fila = function() {
		//¿La fila se agrega en el server?
		if (! this._agregado_en_linea) {
			this.set_evento( new evento_ei('pedido_registro_nuevo', true, '', null));
			return;
		}
			//Crea la fila internamente
		this._filas.push(this._proximo_id);

			//Crea la fila en el DOM
		var fila_template = document.getElementById(this._instancia + '_fila__fila__');
		nuevo_nodo = fila_template.cloneNode(true);
		cambiar_atributos_en_arbol(nuevo_nodo, '__fila__', this._proximo_id);
		nuevo_nodo.style.display = '';
		fila_template.parentNode.appendChild(nuevo_nodo);

			//Refresca la interface
		this.iniciar_fila(this._proximo_id, true);
		this.refrescar_eventos_procesamiento(this._proximo_id);
		this.refrescar_numeracion_filas();
		this.refrescar_procesamientos();		
		this.seleccionar(this._proximo_id);
		this.refrescar_foco();
		this._proximo_id = this._proximo_id + 1;	//Busca un nuevo ID
	};
	
	/**
	 * Deshace la ultima eliminacion de fila
	 */
	ei_formulario_ml.prototype.deshacer = function() {
		if (this._pila_deshacer.length > 0) {
			var funcion = this._pila_deshacer.pop();
			funcion();
		}
		this.refrescar_todo();
	};

	//----Procesamiento
	/**
	 * Cambia el contenido de la fila destinada a contener el valor totalizado de un ef especifico
	 * @param {string} id_ef Id. del ef o columna a variar
	 * @param {string} total Nuevo total
	 * @see #agregar_total
	 */
	ei_formulario_ml.prototype.cambiar_total = function (id_ef, total) {
		//Se mantiene el id anterior, porque se multiplexa hacia otra fila y esto puede estar en el medio de otro proceso
		var id_ant = this._efs[id_ef]._id_form;
		//Se cambia el total
		var elemento = this._efs[id_ef].ir_a_fila(apex_ef_total);
		document.getElementById(elemento._id_form).innerHTML = elemento.formatear_valor(total);
		//Se restaura el id
		this._efs[id_ef]._id_form = id_ant;
		return total;
	};
	
	/**
	 * @private
	 */
	ei_formulario_ml.prototype.agregar_procesamiento = function (id_ef) {
		if (this._efs[id_ef]) {
			//¿Ya se agrego el procesamiento anteriormente?
			if (! this._efs_procesar[id_ef]) {
				this._efs_procesar[id_ef] = true;
				for (var fila in this._filas) {
					this.agregar_procesamiento_fila(id_ef, this._filas[fila]);
				}
			}
		}
	};
	
	/**
	 *	@private
	 */
	ei_formulario_ml.prototype.agregar_procesamiento_fila = function (id_ef, fila) {
		var callback = this._instancia + '.procesar("' + id_ef + '", ' + fila + ')';
		this._efs[id_ef].ir_a_fila(fila).cuando_cambia_valor(callback);
	};

	//----Botonera
	/**
	 * Referencia al tag html del boton eliminar
	 */
	ei_formulario_ml.prototype.boton_eliminar = function() {
		return document.getElementById(this._instancia + '_eliminar');
	};

	/**
	 * Referencia al tag html del boton deshacer
	 */	
	ei_formulario_ml.prototype.boton_deshacer = function() {
		return document.getElementById(this._instancia + '_deshacer');
	};
	
	/**
	 * Referencia al tag html que contiene la cantidad de eliminaciones a deshacer
	 */	
	ei_formulario_ml.prototype.boton_deshacer_cant = function() {
		return document.getElementById(this._instancia + '_deshacer_cant');
	};	
	
	/**
	 * Referencia al tag html que contiene el boton de subir la fila actual
	 */	
	ei_formulario_ml.prototype.boton_subir = function() {
		return document.getElementById(this._instancia + '_subir');
	};

	/**
	 * Referencia al tag html que contiene el boton de bajar la fila actual
	 */		
	ei_formulario_ml.prototype.boton_bajar = function() {
		return document.getElementById(this._instancia + '_bajar');
	};
	
	//----Refresco Grafico 
	
	/**
	 *	Refresca todos la grafica variable del formulario
	 */
	ei_formulario_ml.prototype.refrescar_todo = function () {
		this.refrescar_procesamientos();
		this.refrescar_numeracion_filas();
		this.refrescar_deshacer();
		this.refrescar_seleccion();
	};
	
	/**
	 * @private
	 * Recorre todas las filas y las vuelve a numerara comenzando desde 1
	 */
	ei_formulario_ml.prototype.refrescar_numeracion_filas = function () {
		var nro = 1;
		for (fila in this._filas) {
			var nro_fila = document.getElementById(this._instancia + '_numerofila' + this._filas[fila]);
			if (nro_fila) {
				nro_fila.innerHTML = nro;
			}
			nro++;
		}
	};
	
	/**
	 * Actualiza el botón deshacer
	 * @private
	 */
	ei_formulario_ml.prototype.refrescar_deshacer = function () {
		if (this.boton_deshacer()) {
			var tamanio = this._pila_deshacer.length;
			if (tamanio === 0) {
				this.boton_deshacer().disabled = true;
				this.boton_deshacer_cant().innerHTML = '';
			} else {
				this.boton_deshacer().disabled = false;
				this.boton_deshacer_cant().innerHTML = '(' + tamanio + ')';			
			}
		}
	};
	
	/**
	 * Resalta la línea seleccionada 
	 * @private
	 */
	ei_formulario_ml.prototype.refrescar_seleccion = function () {
		if (isset(this._seleccionada)) {
			cambiar_clase(document.getElementById(this._instancia + '_fila' + this._seleccionada).cells, 'ei-ml-fila-selec');
			if (this.boton_eliminar()) {
				this.boton_eliminar().disabled = false;
			}
			if (this.boton_subir()) {
				this.boton_subir().disabled = false;
				this.boton_bajar().disabled = false;			
			}
		} else {
			if (this.boton_eliminar()) {
				this.boton_eliminar().disabled = true;
			}
			if (this.boton_subir()) {
				this.boton_subir().disabled = true;
				this.boton_bajar().disabled = true;
			}
		}
	};
	
	/**
	 * Toma la fila seleccionada y le pone foco al primer ef que lo acepte
	 */
	ei_formulario_ml.prototype.refrescar_foco = function () {
		for (id_ef in this._efs) {
			if (this._efs[id_ef].ir_a_fila(this._seleccionada).seleccionar()) {
				break;
			}
		}
	};

	/**
	 * @private	 
	 */
	ei_formulario_ml.prototype.refrescar_procesamientos = function (es_inicial) {
		for (id_ef in this._efs) {
			if (id_ef in this._ef_con_totales) {
				this.cambiar_total(id_ef, this.total(id_ef)); //Procesamiento por defecto
			} 
			for (id_fila in this._filas) {
				if (this._efs_procesar[id_ef]) {
					this.procesar(id_ef, this._filas[id_fila], es_inicial, false);
				}
			}
		}
	};	
	
	/**
	 * Toma una fila y le refresca los listeners de procesamiento
	 * @private
	 */
	ei_formulario_ml.prototype.refrescar_eventos_procesamiento = function (fila) {
		for (id_ef in this._efs) {
			if (this._efs_procesar[id_ef]) {		
				this.agregar_procesamiento_fila(id_ef, fila);
			}
		}		
	};
	
//--------------------------------------------------------------------------------	
//Utilidades sobre arbol DOM 
if (self.Node && ! self.Node.prototype.swapNode) {
	/**
	 *	@ignore
	 */
	Node.prototype.swapNode = function (node) {
		var nextSibling = this.nextSibling;
		var parentNode = this.parentNode;
		node.parentNode.replaceChild(this, node);
		parentNode.insertBefore(node, nextSibling);  
	};
}

function intercambiar_nodos(nodo1, nodo2) {
	if (ie) {	//BUG del IE para mantener el estado de los checkbox
		var intercambio_vals = [];
		var inputs = document.getElementsByTagName('input');
		for (var i=0; i < inputs.length; i++) {
			if (inputs[i].type.toLowerCase() == 'checkbox' && inputs[i].id.indexOf('__fila__') == -1) {
				intercambio_vals.push( [inputs[i].id, inputs[i].checked]);
			}
		}	
	}
	nodo1.swapNode(nodo2);
	if (ie) {
		for (i=0; i < intercambio_vals.length; i++) {
			var check = intercambio_vals[i];
			document.getElementById(check[0]).checked = check[1];
		}
	}
}

//Cambia la clase a un conjunto de nodos
function cambiar_clase(nodos, nueva_clase) {
	for (nodo in nodos) {
		nodos[nodo].className = nueva_clase;
	}
}

//Determina si un atributo es un evento
//ATENCION: Buscar algo de mejor calidad
function es_evento(nombre) {
	return nombre.substring(0, 2).toLowerCase() == 'on';
}

//Recorre una rama del arbol DOM y reemplaza ocurrencias de un ID con otro
//Esto permite 'instanciar' templates con datos particulares
//ATENCION: Esto no funciona bien en Opera con los eventos
function cambiar_atributos_en_arbol(arbol, id_orig, nuevo_id) {
	if (arbol.attributes) {
		for (var a=0; a < arbol.attributes.length; a++) {
			var valor = arbol.attributes[a].value;
			var nombre = arbol.attributes[a].name;
			if (valor && valor.toString().indexOf(id_orig) != -1) {
				var nuevo_valor = valor.reemplazar(id_orig, nuevo_id);
				if (ie && es_evento(nombre)) { //Para solucionar caso particular en IE con los eventos
					arbol.setAttribute(arbol.attributes[a].name, new Function(nuevo_valor));	
				} else {
					arbol.attributes[a].value = nuevo_valor;
				}
			}
		}
	}
	//Recursion
	for (var i=0; i < arbol.childNodes.length; i++) {
		cambiar_atributos_en_arbol(arbol.childNodes[i], id_orig, nuevo_id);
	}
}

toba.confirmar_inclusion('componentes/ei_formulario_ml');