//--------------------------------------------------------------------------------
//Clase objeto_ei_formulario_ml
objeto_ei_formulario_ml.prototype = new objeto_ei_formulario;
var def = objeto_ei_formulario_ml.prototype;
def.constructor = objeto_ei_formulario_ml;

	//----Construcción
	function objeto_ei_formulario_ml(instancia, rango_tabs, cant_filas, con_agregar) {
		this.instancia = instancia;				//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
		this.rango_tabs = rango_tabs;
		this.con_agregar = con_agregar;			//¿Permite agregar/quitar filas?
		this.filas = new Array();				//Carga inicial de las filas
		for (var i=0 ; i < cant_filas ; i++) {
			this.filas.push(i);
		}
		this.ultimo_id = i;

		this.efs = new Array();					//Lista de objeto_ef contenidos
		this.pila_deshacer = new Array();		//Pila de acciones a deshacer
		this.efs_totalizar = new Array();		///ID de los ef's que poseen totalizacion
	}

	def.iniciar = function () {
		for (fila in this.filas) {
			this.agregar_tab_index(this.filas[fila]);
		}
	}
	
	//----Submit
	def.submit = function() {
		if (!this.validar())
			return false;
		var lista_filas = this.filas.join('_');
		document.getElementById(this.instancia + '_listafilas').value = lista_filas;
		return true;
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
			cambiar_clase(document.getElementById(this.instancia + '_fila' + this.seleccionada).cells, 'abm-fila-ml');			
			delete(this.seleccionada);
		}
	}
	
	def.subir_seleccionada = function () {
		//Busco las posiciones a intercambiar
		var pos_anterior = null;
		for (posicion in this.filas) {
			if (this.seleccionada == this.filas[posicion]) {
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
		for (posicion = this.filas.length - 1; posicion >= 0; posicion--) {
			if (this.seleccionada == this.filas[posicion]) {
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
		var nodo_padre = document.getElementById(this.instancia + '_fila' + this.filas[pos_a]);
		var nodo_selecc = document.getElementById(this.instancia + '_fila' + this.filas[pos_b]);
		intercambiar_nodos(nodo_selecc, nodo_padre);
		
		//Reemplazo de los tabs index
		for (id_ef in this.efs) {
			var tab_a = this.efs[id_ef].posicionarse_en_fila(this.filas[pos_a]).tab();
			var tab_b = this.efs[id_ef].posicionarse_en_fila(this.filas[pos_b]).tab();
			this.efs[id_ef].posicionarse_en_fila(this.filas[pos_a]).cambiar_tab(tab_b);
			this.efs[id_ef].posicionarse_en_fila(this.filas[pos_b]).cambiar_tab(tab_a);			
		}
		
		//Reemplazo interno
		var temp = this.filas[pos_a];
		this.filas[pos_a] = this.filas[pos_b];
		this.filas[pos_b] = temp;
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
		var id_fila = this.instancia + '_fila' + fila;
		var id_deshacer = this.instancia + '_deshacer';
		cambiar_clase(document.getElementById(id_fila).cells, 'abm-fila-ml');
		document.getElementById(id_fila).style.display = 'none';
			//Elimina la fila en la lista interna
		for (i in this.filas) { 
			if (this.filas[i] == fila) {
				this.filas.splice(i, 1); 
				break;
			}
			var anterior = this.filas[i];		
		}
			//Crea función de deshacer
		this.pila_deshacer.push(new Function (
								'document.getElementById("' + id_fila + '").style.display = ""\n' +
								this.instancia + '.filas.splice(' + i + ',0,"' + fila + '")\n'
								));
		return anterior;
	}
	
	def.crear_fila = function() {
			//Crea la fila internamente
		this.ultimo_id = this.ultimo_id + 1;	//Busca un nuevo ID
		this.filas.push(this.ultimo_id);

			//Crea la fila en el DOM
		var fila_template = document.getElementById(this.instancia + '_fila__fila__');
		nuevo_nodo = fila_template.cloneNode(true);
		cambiar_atributos_en_arbol(nuevo_nodo, '__fila__', this.ultimo_id);
		nuevo_nodo.style.display = '';
		fila_template.parentNode.appendChild(nuevo_nodo);

			//Refresca la interface
		this.agregar_tab_index(this.ultimo_id);
		this.refrescar_eventos(this.ultimo_id);
		this.refrescar_numeracion_filas();
		this.seleccionar(this.ultimo_id);
		this.refrescar_foco();
	}
	
	def.deshacer = function() {
		if (this.pila_deshacer.length > 0) {
			var funcion = this.pila_deshacer.pop();
			funcion();
		}
		this.refrescar_todo();
	}

	//----Totalización
	def.totalizar_columna = function (id_ef) {
		total = this.get_total_columna(id_ef);
		document.getElementById(id_ef + 's').innerHTML = total;
	}

	def.get_total_columna = function (id_ef) {
		var total = 0;	
		for (fila in this.filas)	{
			valor = this.efs[id_ef].posicionarse_en_fila(this.filas[fila]).valor();
			valor = (valor == '' || isNaN(valor)) ? 0 : valor;
			total += valor
		}
		return total;
	}
		
	def.agregar_totalizacion = function (id_ef) {
		if (this.efs[id_ef]) {
			this.efs_totalizar[id_ef] = true;
			for (fila in this.filas) {
				this.agregar_totalizacion_fila(id_ef, this.filas[fila]);
			}
			this.totalizar_columna(id_ef);
		}
	}
	
	def.agregar_totalizacion_fila = function (id_ef, fila) {
		var callback = new Function (this.instancia + '.totalizar_columna("' + id_ef + '")');
		this.efs[id_ef].posicionarse_en_fila(fila).cuando_cambia_valor(callback);
	}


	//----Refresco Grafico
	def.refrescar_todo = function () {
		this.refrescar_totales();
		this.refrescar_numeracion_filas();
		this.refrescar_deshacer();
		this.refrescar_seleccion();
	}
	
	def.refrescar_totales = function () {
		for (id_ef in this.efs) {
			if (this.efs_totalizar[id_ef]) {		
				this.totalizar_columna(id_ef);
			}
		}
	}
	
	def.refrescar_numeracion_filas = function () {
		var nro = 1;
		for (fila in this.filas) {
			document.getElementById(this.instancia + '_numerofila' + this.filas[fila]).innerHTML = nro;
			nro++;
		}
	}
	
	def.refrescar_deshacer = function () {
		var tamanio = this.pila_deshacer.length;
		if (tamanio == 0) {
			document.getElementById(this.instancia + '_deshacer').disabled = true;
			document.getElementById(this.instancia + '_deshacer_cant').innerHTML = '';
		} else {
			document.getElementById(this.instancia + '_deshacer').disabled = false;
			document.getElementById(this.instancia + '_deshacer_cant').innerHTML = '(' + tamanio + ')';			
		}		
	}
	
	def.refrescar_seleccion = function () {
		if (this.seleccionada != null) {
			cambiar_clase(document.getElementById(this.instancia + '_fila' + this.seleccionada).cells, 'abm-fila-ml-selec');
			if (this.con_agregar) {
				document.getElementById(this.instancia + '_eliminar').disabled = false;
				document.getElementById(this.instancia + '_subir').disabled = false;
				document.getElementById(this.instancia + '_bajar').disabled = false;			
			}
		} else {
			if (this.con_agregar) {
				document.getElementById(this.instancia + '_eliminar').disabled = true;
				document.getElementById(this.instancia + '_subir').disabled = true;
				document.getElementById(this.instancia + '_bajar').disabled = true;
			}
		}
	}
	
	def.refrescar_foco = function () {
		for (id_ef in this.efs) {
			if (this.efs[id_ef].posicionarse_en_fila(this.seleccionada).seleccionar())
				break;
		}
	}
	
	def.refrescar_eventos = function (fila) {
		for (id_ef in this.efs) {
			if (this.efs_totalizar[id_ef]) {		
				this.agregar_totalizacion_fila(id_ef, fila);
			}
		}		
	}
	
	
	//----Tab
	def.agregar_tab_index = function (fila) {
		for (id_ef in this.efs) {
			this.efs[id_ef].posicionarse_en_fila(fila).cambiar_tab(this.rango_tabs[0]);
			this.rango_tabs[0]++;
		}
	}
	
	//----Validación
	def.validar = function() {
		for (fila in this.filas) {
			for (id_ef in this.efs) {
				var ef = this.efs[id_ef].posicionarse_en_fila(this.filas[fila]);
				if (! ef.validar()) {
					this.seleccionar(this.filas[fila]);
					ef.seleccionar();
					alert(ef.obtener_error());
					ef.resetear_error();
					return false;
				}
			}
		}
		return true;
	}

//--------------------------------------------------------------------------------	
//Utilidades sobre arbol DOM
if (!ie) {
	Node.prototype.swapNode = function (node) {
		var nextSibling = this.nextSibling;
		var parentNode = this.parentNode;
		node.parentNode.replaceChild(this, node);
		parentNode.insertBefore(node, nextSibling);  
	}
}

function intercambiar_nodos(nodo1, nodo2) {
	if (ie) {
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
				var nuevo_valor = reemplazar(valor, id_orig, nuevo_id);
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