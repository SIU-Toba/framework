//-------------------------------------------------------------------------------- 
//Clase objeto_ei_formulario_ml 
objeto_ei_formulario_ml.prototype = new objeto_ei_formulario;
var def = objeto_ei_formulario_ml.prototype;
def.constructor = objeto_ei_formulario_ml;

	//----Construcción 
	function objeto_ei_formulario_ml(instancia, rango_tabs, input_submit, cant_filas, con_agregar) {
		objeto_ei_formulario.prototype.constructor.call(this, instancia, rango_tabs, input_submit);
		this._con_agregar = con_agregar;		//¿Permite agregar/quitar filas?
		this._filas = new Array();				//Carga inicial de las filas
		for (var i=0 ; i < cant_filas ; i++)
			this._filas.push(i);
		this._ultimo_id = i;
		this._pila_deshacer = new Array();		//Pila de acciones a deshacer
	}

	def.iniciar = function () {
		for (fila in this._filas)
			this.iniciar_fila(this._filas[fila]);
		this.agregar_procesamientos();
		this.refrescar_procesamientos(true);
		this.reset_evento();
	}

	def.iniciar_fila = function (fila) {
		for (id_ef in this._efs) {
			var ef = this._efs[id_ef].ir_a_fila(fila);
			ef.iniciar(id_ef);
			ef.cambiar_tab(this._rango_tabs[0]);
			ef.cuando_cambia_valor(this._instancia + '.validar_fila_ef(' + fila + ',"' + id_ef + '")');			
			this._rango_tabs[0]++;
		}
	}	
		
	//----Consultas 
	def.filas = function () { return this._filas };
	
	def.procesar = function (id_ef, es_inicial) {
		if (this.hay_procesamiento_particular_ef(id_ef))
			return this['procesar_' + id_ef](es_inicial);		 //Procesamiento particular
		else
			return this.cambiar_total(id_ef, this.total(id_ef)); //Procesamiento por defecto
	}

	//Función de calculo de procesamento por defecto, suma el valor de cada filas	
	def.total = function (id_ef) {
		var total = 0;	
		for (fila in this._filas) {
			valor = this._efs[id_ef].ir_a_fila(this._filas[fila]).valor();
			valor = (valor == '' || isNaN(valor)) ? 0 : valor;
			total += valor;
		}
		return total;
	}
	
	//----Validación 
	def.validacion_defecto = function() {
		var ok = true;
		for (id_fila in this._filas) {
			ok = this.validar_fila(id_fila) && ok;
		}
		return ok;
	}
	
	def.validar_fila = function(id_fila) {
		ok = true;
		for (id_ef in this._efs) {
			ok = this.validar_fila_ef(this._filas[id_fila], id_ef) && ok;
		}
		return ok;
	}
	
	def.validar_fila_ef = function(fila, id_ef) {
		var ef = this._efs[id_ef].ir_a_fila(fila);
		if (! ef.validar()) {
			if (! this._silencioso) {
				ef.resaltar(ef.error(), 6);
				ef.resetear_error();
			}
			return false;
		}		
		ef.no_resaltar();
		return true;
	}
	
	//----Submit 
	def.submit = function() {
		//Si no es parte de un submit general, dispararlo
		if (this._ci && !this._ci.en_submit)
			return this._ci.submit();

		if (this._evento) {
			for (fila in this._filas) {
				for (id_ef in this._efs) {
					this._efs[id_ef].ir_a_fila(this._filas[fila]).submit();
				}
			}
			var lista_filas = this._filas.join('_');
			document.getElementById(this._instancia + '_listafilas').value = lista_filas;
			//Marco la ejecucion del evento para que la clase PHP lo reconozca
			document.getElementById(this._input_submit).value = this._evento.id;
			return true;
		}
	}

	//----Selección 
	def.seleccionar = function(fila) {
		if  (fila != this.seleccionada) {
			this.deseleccionar_actual();
			this.seleccionada = fila;
			this.refrescar_seleccion();
		}
	}
	
	def.deseleccionar_actual = function() {
		if (this.seleccionada != null) {	//Deselecciona el anterior
			cambiar_clase(document.getElementById(this._instancia + '_fila' + this.seleccionada).cells, 'abm-fila-ml');			
			delete(this.seleccionada);
		}
	}
	
	def.subir_seleccionada = function () {
		//Busco las posiciones a intercambiar
		var pos_anterior = null;
		for (posicion in this._filas) {
			if (this.seleccionada == this._filas[posicion]) {
				pos_selec = posicion;
				break;
			}
			pos_anterior = posicion;
		}
		if (pos_anterior != null) {
			this.intercambiar_filas(pos_anterior, pos_selec);
			this.refrescar_numeracion_filas();
		}
	}
	
	def.bajar_seleccionada = function () {
		//Busco las posiciones a intercambiar
		var pos_siguiente = null;
		for (posicion = this._filas.length - 1; posicion >= 0; posicion--) {
			if (this.seleccionada == this._filas[posicion]) {
				pos_selec = posicion;
				break;
			}
			pos_siguiente = posicion;
		}
		if (pos_siguiente != null) {
			this.intercambiar_filas(pos_selec, pos_siguiente);
			this.refrescar_numeracion_filas();
		}
	}

	def.intercambiar_filas = function (pos_a, pos_b) {
		//Reemplazo en el DOM
		var nodo_padre = document.getElementById(this._instancia + '_fila' + this._filas[pos_a]);
		var nodo_selecc = document.getElementById(this._instancia + '_fila' + this._filas[pos_b]);
		intercambiar_nodos(nodo_selecc, nodo_padre);
		
		//Reemplazo de los tabs index
		for (id_ef in this._efs) {
			var tab_a = this._efs[id_ef].ir_a_fila(this._filas[pos_a]).tab();
			var tab_b = this._efs[id_ef].ir_a_fila(this._filas[pos_b]).tab();
			this._efs[id_ef].ir_a_fila(this._filas[pos_a]).cambiar_tab(tab_b);
			this._efs[id_ef].ir_a_fila(this._filas[pos_b]).cambiar_tab(tab_a);			
		}
		
		//Reemplazo interno 
		var temp = this._filas[pos_a];
		this._filas[pos_a] = this._filas[pos_b];
		this._filas[pos_b] = temp;
	}

	//---ABM 
	def.eliminar_seleccionada = function() {
		var fila = this.seleccionada;
		anterior = this.eliminar_fila(fila);
		delete(this.seleccionada);
		if (anterior != null)
			this.seleccionar(anterior);
		this.refrescar_todo();
	}
	
	//Elimina una fila y retorna la fila más cercana
	def.eliminar_fila = function(fila) {
			//'Elimina' la fila en el DOM
		var id_fila = this._instancia + '_fila' + fila;
		var id_deshacer = this._instancia + '_deshacer';
		cambiar_clase(document.getElementById(id_fila).cells, 'abm-fila-ml');
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
								this._instancia + '._filas.splice(' + i + ',0,"' + fila + '")\n'
								));
		return anterior;
	}
	
	def.crear_fila = function() {
			//Crea la fila internamente
		this._ultimo_id = this._ultimo_id + 1;	//Busca un nuevo ID
		this._filas.push(this._ultimo_id);

			//Crea la fila en el DOM
		var fila_template = document.getElementById(this._instancia + '_fila__fila__');
		nuevo_nodo = fila_template.cloneNode(true);
		cambiar_atributos_en_arbol(nuevo_nodo, '__fila__', this._ultimo_id);
		nuevo_nodo.style.display = '';
		fila_template.parentNode.appendChild(nuevo_nodo);

			//Refresca la interface
		this.iniciar_fila(this._ultimo_id);
		this.refrescar_eventos_procesamiento(this._ultimo_id);
		this.refrescar_numeracion_filas();
		this.refrescar_procesamientos();		
		this.seleccionar(this._ultimo_id);
		this.refrescar_foco();
	}
	
	def.deshacer = function() {
		if (this._pila_deshacer.length > 0) {
			var funcion = this._pila_deshacer.pop();
			funcion();
		}
		this.refrescar_todo();
	}

	//----Procesamiento
	def.cambiar_total = function (id_ef, total) {
		var elemento = this._efs[id_ef].ir_a_fila('s');
		document.getElementById(elemento._id_form).innerHTML = elemento.formato_texto(total);
		return total;
	}
	
	def.agregar_procesamiento = function (id_ef) {
		if (this._efs[id_ef]) {
			this._efs_procesar[id_ef] = true;
			for (fila in this._filas) {
				this.agregar_procesamiento_fila(id_ef, this._filas[fila]);
			}
		}
	}
	
	def.agregar_procesamiento_fila = function (id_ef, fila) {
		var callback = this._instancia + '.procesar("' + id_ef + '")';
		this._efs[id_ef].ir_a_fila(fila).cuando_cambia_valor(callback);
	}


	//----Refresco Grafico 
	def.refrescar_todo = function () {
		this.refrescar_procesamientos();
		this.refrescar_numeracion_filas();
		this.refrescar_deshacer();
		this.refrescar_seleccion();
	}
	
	//Recorre todas las filas y las vuelve a numerara comenzando desde 1
	def.refrescar_numeracion_filas = function () {
		var nro = 1;
		for (fila in this._filas) {
			document.getElementById(this._instancia + '_numerofila' + this._filas[fila]).innerHTML = nro;
			nro++;
		}
	}
	
	//Actualiza el botón deshacer
	def.refrescar_deshacer = function () {
		var tamanio = this._pila_deshacer.length;
		if (tamanio == 0) {
			document.getElementById(this._instancia + '_deshacer').disabled = true;
			document.getElementById(this._instancia + '_deshacer_cant').innerHTML = '';
		} else {
			document.getElementById(this._instancia + '_deshacer').disabled = false;
			document.getElementById(this._instancia + '_deshacer_cant').innerHTML = '(' + tamanio + ')';			
		}		
	}
	
	//Resalta la línea seleccionada 
	def.refrescar_seleccion = function () {
		if (this.seleccionada != null) {
			cambiar_clase(document.getElementById(this._instancia + '_fila' + this.seleccionada).cells, 'abm-fila-ml-selec');
			if (this._con_agregar) {
				document.getElementById(this._instancia + '_eliminar').disabled = false;
				document.getElementById(this._instancia + '_subir').disabled = false;
				document.getElementById(this._instancia + '_bajar').disabled = false;			
			}
		} else {
			if (this._con_agregar) {
				document.getElementById(this._instancia + '_eliminar').disabled = true;
				document.getElementById(this._instancia + '_subir').disabled = true;
				document.getElementById(this._instancia + '_bajar').disabled = true;
			}
		}
	}
	
	//Toma la fila seleccionada y le pone foco al primer ef que se la banque.
	def.refrescar_foco = function () {
		for (id_ef in this._efs) {
			if (this._efs[id_ef].ir_a_fila(this.seleccionada).seleccionar())
				break;
		}
	}
	
	//Toma una fila y le refresca los listeners de procesamiento
	def.refrescar_eventos_procesamiento = function (fila) {
		for (id_ef in this._efs) {
			if (this._efs_procesar[id_ef]) {		
				this.agregar_procesamiento_fila(id_ef, fila);
			}
		}		
	}
	
//--------------------------------------------------------------------------------	
//Utilidades sobre arbol DOM 
if (self.Node && ! self.Node.prototype.swapNode) {
	Node.prototype.swapNode = function (node) {
		var nextSibling = this.nextSibling;
		var parentNode = this.parentNode;
		node.parentNode.replaceChild(this, node);
		parentNode.insertBefore(node, nextSibling);  
	}
}

function intercambiar_nodos(nodo1, nodo2) {
	if (ie) {	//BUG del IE para mantener el estado de los checkbox
		var intercambio_vals = new Array();
		var inputs = document.getElementsByTagName('input');
		for (var i=0; i < inputs.length; i++) {
			if (inputs[i].type.toLowerCase() == 'checkbox' && inputs[i].id.indexOf('__fila__') == -1) {
				intercambio_vals.push( new Array(inputs[i].id, inputs[i].checked));
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
