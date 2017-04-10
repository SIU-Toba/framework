
ef_editable.prototype = new ef();
ef_editable.prototype.constructor = ef_editable;

	/**
	 * @class Elemento editable equivalente a un 'input type=text'
	 * @constructor
	 * @phpdoc Componentes/Efs/toba_ef_editable toba_ef_editable
	 */
	function ef_editable(id_form, etiqueta, obligatorio, colapsado, masc, expreg) {
		ef.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
		this._forma_mascara = (masc && masc.trim().toLowerCase() != 'no') ? masc : null;
		this._expreg = expreg;
		this._mascara = null;
	}
	
	//---Consultas
	
	ef_editable.prototype.activo = function() {
		return this.input().type !='hidden' && !this.input().readOnly;
	};
	
	/**
	 * Retorna el valor actual del elemento.
	 * Este valor es retornado sin formato, aun si el elemento posee una máscara asociada.
	 * Por ejemplo si con formato se ve como <pre>$ 1.423,12</pre> el valor retornado es <pre>1423.12</pre>
	 * @type string
	 */	
	ef_editable.prototype.get_estado = function() {
		if (this._mascara) {
			//Refresco del valor, esto es necesario por la multiplexacion del ML
			this._mascara.format(this.input().value, false);
			return this._mascara.valor_sin_formato();
		} else {
			return this.input().value;
		}
	};	
	
	/**
	 * Retorna el estado o valor actual del elemento en un formato legible al usuario
	 * Si el editable posee una mascara, retorna el estado con el formato que da esta mascara
	 * @type string
	 */
	ef_editable.prototype.get_estado_con_formato = function() {
		return this.input().value;
	};		
	
	ef_editable.prototype.formatear_valor = function(valor) {
		if (this._mascara) {
			return this._mascara.format(valor, false, true);
		} else {
			return valor;
		}
	};	
	
	ef_editable.prototype.validar = function () {
		if (! ef.prototype.validar.call(this)) {
			return false;
		}
		var estado = this.get_estado();
		if (this._obligatorio && ereg_nulo.test(estado)) {
			this._error = 'es obligatorio.';
		    return false;
		}
		if (estado !== '' && isset(this._expreg) && this._expreg !== '') {
			var erv, temp = false;
			erv = this._get_er_validacion();
			if (erv !== false) {				
				temp = new RegExp(erv['er'], erv['flags']).test(estado);
			}
			if (! temp) {
				this._error = 'no es válido';
				return false;
			}
		}
		return true;
	};
		
	//---Comandos

	ef_editable.prototype.iniciar = function(id, contenedor) { 
		ef.prototype.iniciar.call(this, id, contenedor);
		if (this._forma_mascara) {
			this._mascara = new mascara_generica(this._forma_mascara);
			this._mascara.attach(this.input());
			this.set_estado(this.input().value);
		}
	};	
	
	ef_editable.prototype.submit = function() { 
		if (this._mascara) {
			this.input().value = this.get_estado();
		}
		ef.prototype.submit.call(this);
	};
	
	ef_editable.prototype.set_estado = function(nuevo) {
		if (nuevo === null) {
			return this.resetear_estado();
		}
		if (this._mascara) {
			var valor = this._mascara.format(nuevo, false, true);
			this.input().value = (typeof valor == 'string') ? valor.decodeEntities(): valor;
			var desc = document.getElementById(this._id_form + '_desc');
			if (desc) {
				desc.value = (typeof valor == 'string') ? valor.decodeEntities(): valor;
			}
		} else {
			return ef.prototype.set_estado.call(this, nuevo);	
		}
		if (this.input().onblur) {
			this.input().onblur();
		}
	};	
	
	//cuando_cambia_valor (disparar_callback)
	ef_editable.prototype.cuando_cambia_valor = function(callback) { 
		addEvent(this.input(), 'onblur', callback);
	};
	
	ef_editable.prototype.set_solo_lectura = function(solo_lectura) {
		this.input().readOnly = (typeof solo_lectura == 'undefined' || solo_lectura);
		this._solo_lectura = this.input().readOnly;		
	};	

	/*
	* Permite setear en runtime una mascara diferente a la especificada por metadatos
	* Con null se resetea a la mascara original
	*/
	ef_editable.prototype.set_mascara = function (masc_obj) {
		var estado_actual = this.get_estado();
		this._mascara = masc_obj;
		if (masc_obj !== null) {
			this._mascara.attach(this.input());
			this.set_estado(estado_actual);
		}
	};
	
	/*
	* Permite setear en runtime una expreg diferente a la especificada por metadatos
	* Con null se resetea a la expreg original
	*/
	ef_editable.prototype.set_expreg = function (expresion_regular) {
		this._expreg = expresion_regular;
	};

	/*
	* Permite setear en runtime una placeholder diferente a la especificada por metadatos
	* Con null se resetea a la placeholder original
	*/
	ef_editable.prototype.set_placeholder = function (placeholder) {
		var inp_field = this.input();
		inp_field.setAttribute('placeholder', placeholder);
	};
	
// ########################################################################################################
// ########################################################################################################
	
ef_editable_numero.prototype = new ef_editable();
ef_editable_numero.prototype.constructor = ef_editable_numero;

	/**
	 * @class Elemento editable que sólo permite ingresar números.<br>
	 * Para esto utiliza en forma predeterminada una máscara <em>###.###,##</em>
	 * @constructor
	 * @phpdoc Componentes/Efs/toba_ef_editable_numero toba_ef_editable_numero
	 */
	function ef_editable_numero(id_form, etiqueta, obligatorio, colapsado, masc, expreg, rango, mensaje) {
		ef_editable.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado, masc, expreg);
		if (masc) {
			this._forma_mascara = (masc.trim().toLowerCase() != 'no') ? masc : null;
		} else {
			this._forma_mascara = '###.###,##';
		}
		this._rango = rango;					//[0] => inferior [1] => superior
		this._mensaje = mensaje;				//Mensaje a mostrar cuando no se valida el número
	}
	
	ef_editable_numero.prototype.iniciar = function(id, controlador) { 
		ef.prototype.iniciar.call(this, id, controlador);
		if (this._forma_mascara) {
			this._mascara = new mascara_numero(this._forma_mascara);
			this._mascara.attach(this.input());
			this.set_estado(this.input().value);
		}
	};	
	
	ef_editable_numero.prototype.get_estado = function() {
		var valor = ef_editable.prototype.get_estado.call(this);
		return (valor === '') ? '' : parseFloat(valor);
	};

	/**
	 * @private
	 */
	ef_editable_numero.prototype.validar_rango = function() {
		//this._rango[0-1][0] es limite [0-1][1] determina inclusive
		var ok = true;
		var valor = this.get_estado();
		if (typeof valor != 'number' || ! this._rango) {
			return true;
		}
		if (this._rango[0][0] != '*') {
			ok = (this._rango[0][1]) ? (valor >= this._rango[0][0]) : (valor > this._rango[0][0]);
		}
		if (ok && this._rango[1][0] != '*') {
			ok = (this._rango[1][1]) ? (valor <= this._rango[1][0]) : (valor < this._rango[1][0]);
		}
		return ok;
	};	
	
	ef_editable_numero.prototype.validar = function() {
		if (! ef_editable.prototype.validar.call(this)) {
			return false;
		}
		if (isNaN(this.get_estado())) {
			this._error = 'debe ser numérico.';
		    return false;
		}
		if (!this.validar_rango()) {
			this._error = this._mensaje;
		    return false;
		}
		return true;
	};

// ########################################################################################################
// ########################################################################################################
		
ef_editable_moneda.prototype = new ef_editable_numero();
ef_editable_moneda.prototype.constructor = ef_editable_moneda;

	/**
	 * @class Elemento editable que sólo permite ingresar números que representan un valor monetario.<br>	 
	 * Para esto utiliza en forma predeterminada una máscara <em>$ ###.###,00</em>
	 * @constructor
	 * @phpdoc Componentes/Efs/toba_ef_editable_moneda toba_ef_editable_moneda
	 */
	function ef_editable_moneda(id_form, etiqueta, obligatorio, colapsado, masc, expreg, rango, mensaje)	{
		ef_editable_numero.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado, masc, expreg, rango, mensaje);
		this._forma_mascara = (masc) ? masc : '$ ###.###,00';
	}
	
	ef_editable_moneda.prototype.formatear_valor = function(valor) {
		if (this._mascara) {
			valor = parseFloat(valor).toFixed(6);
			return this._mascara.format(valor, false, true);
		} else {
			return valor;
		}
	};	

// ########################################################################################################
// ########################################################################################################
		
ef_editable_porcentaje.prototype = new ef_editable_numero();
ef_editable_porcentaje.prototype.constructor = ef_editable_porcentaje;

	/**
	 * @class Elemento editable que sólo permite ingresar números que representan un porcentaje
	 * @constructor
	 * @phpdoc Componentes/Efs/toba_ef_editable_numero_porcentaje toba_ef_editable_numero_porcentaje	 
	 */
	function ef_editable_porcentaje(id_form, etiqueta, obligatorio, colapsado, masc, exp_reg, rango, mensaje)	{
		ef_editable_numero.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado, masc, exp_reg, rango, mensaje);
	}
	
	ef_editable_porcentaje.prototype.formatear_valor = function(valor) {
		if (this._mascara) {
			valor = parseFloat(valor).toFixed(6);
			return this._mascara.format(valor, false, true) + ' %';
		} else {
			return valor;
		}
	};		
	
// ########################################################################################################
// ########################################################################################################
	
ef_editable_clave.prototype = new ef_editable();
ef_editable_clave.prototype.constructor = ef_editable_clave;

	/**
	 * @class  Elemento editable que permite ingresar contraseñas, con o sin campo de confirmación.
	 * @constructor
	 * @phpdoc Componentes/Efs/toba_ef_editable_clave toba_ef_editable_clave
	 */
	function ef_editable_clave(id_form, etiqueta, obligatorio, colapsado, masc, exp_reg)	{
		ef_editable.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado, masc, exp_reg);
		
		//Variables para el password strength meter
		// -- Toggle to true or false, if you want to change what is checked in the password
		this.bCheckNumbers = true;
		this.bCheckUpperCase = true;
		this.bCheckLowerCase = true;
		this.bCheckPunctuation = true;
		this.nPasswordLifetime = 365;
	}

	ef_editable_clave.prototype.validar = function() {
		if (! ef_editable.prototype.validar.call(this)) {
			return false;
		}
		var orig = this.input();
		var test = this.input_hermano();		
		if (test && orig.value != test.value) {
			this._error = ': Las contraseñas no coinciden.';
		    return false;
		}		
		return true;
	};
	
	ef_editable_clave.prototype.set_estado = function (nuevo) {
		var input = this.input();
		input.value = nuevo;
		var test = this.input_hermano();
		if (test) {
			test.value = nuevo;
		}
		if (input.onblur) {
			input.onblur();
		}
	};
	
	/**
	 *	Si en el editor se definio el elemento con confirmación de clave, este método retorna el input html asociado al campo de confirmación
	 */
	ef_editable_clave.prototype.input_hermano = function() {
		return document.getElementById(this._id_form + '_test');
	};
	
	ef_editable_clave.prototype.set_tab_index = function(tab_index) {
		this.input().tabIndex = tab_index;
		var test = this.input_hermano();
		if (test) {
			test.tabIndex = tab_index+1;
		}
	};	
	
	//cuando_cambia_valor (disparar_callback)
	ef_editable_clave.prototype.cuando_cambia_valor = function(callback) { 
		addEvent(this.input(), 'onblur', callback);
		var test = this.input_hermano();
		if (test) {		
			addEvent(test, 'onblur', callback);
		}
	};
	
	// Check password
	ef_editable_clave.prototype.checkPassword = function(strPassword) {
		// Reset combination count
		var nCombinations = 0;

		// Check numbers
		if (this.bCheckNumbers) {
			strCheck = "0123456789";
			if (doesContain(strPassword, strCheck) > 0) { 
				nCombinations += strCheck.length; 
			}
		}

		// Check upper case
		if (this.bCheckUpperCase) {
			strCheck = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			if (doesContain(strPassword, strCheck) > 0) { 
				nCombinations += strCheck.length; 
			}
		}

		// Check lower case
		if (this.bCheckLowerCase) {
			strCheck = "abcdefghijklmnopqrstuvwxyz";
			if (doesContain(strPassword, strCheck) > 0) { 
				nCombinations += strCheck.length; 
			}
		}

		// Check punctuation
		if (this.bCheckPunctuation) {
			strCheck = ";:-_=+\|//?^&!.@$£#*()%~<>{}[]";
			if (doesContain(strPassword, strCheck) > 0) { 
				nCombinations += strCheck.length; 
			}
		}

		// Calculate
		// -- 500 tries per second => minutes 
		var nDays = ((Math.pow(nCombinations, strPassword.length) / 500) / 2) / 86400;

		// Number of days out of password lifetime setting
		var nPerc = nDays / this.nPasswordLifetime;

		return nPerc;
	}

	// Runs password through check and then updates GUI 
	ef_editable_clave.prototype.runPassword = function (strPassword, strFieldID) 
	{	
		 // Get controls
		var ctlBar = document.getElementById(strFieldID + "_bar"); 
		var ctlText = document.getElementById(strFieldID + "_text");
		if (!ctlBar || !ctlText)
			return;

		// Check password
		nPerc = this.checkPassword(strPassword);

		// Set new width
		var nRound = Math.log(nPerc) * 5;
		if (nRound < (strPassword.length * 5)) {		//Feedback visual para el usuario cuando el porcentaje es demasiado pequeño 
			nRound = strPassword.length * 6; 
		}

		if (nRound > 100) {
			nRound = 100;
		}

		// Color and text
		if (nRound > 95) {
			strText = "Muy Seguro";
			strColor = "#3bce08";
		} else if (nRound > 75) {
			strText = "Seguro";
			strColor = "orange";
		} else if (nRound > 50) {
			strText = "Mediocre";
			strColor = "#ffd801";
		} else {
			strColor = "red"; 	
			if (strPassword == 'toba') {
				strText = 'definitivamente no';
			} else {
				strText = "Inseguro";
			}
		}

		ctlBar.style.width = nRound + "%";
		ctlBar.style.backgroundColor = strColor;

		ctlText.innerHTML = "<span style='white-spacen: nowrap; color: " + strColor + ";'>" + strText + "</span>";
	}
// ########################################################################################################
// ########################################################################################################

ef_editable_fecha.prototype = new ef_editable();
ef_editable_fecha.prototype.constructor = ef_editable_fecha;

	/**
	 * @class Elemento editable que permite ingresar fechas.<br>
	 * Para esto utiliza una máscara <em>dd/mm/yyyy</em>
	 * @constructor
	 * @phpdoc Componentes/Efs/toba_ef_editable_fecha toba_ef_editable_fecha
	 */
	function ef_editable_fecha(id_form, etiqueta, obligatorio, colapsable, masc, expreg, rango)	{
		ef_editable.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsable,null, expreg);
		this._forma_mascara = (masc) ? masc : 'dd/mm/yyyy';
		this._rango_valido = rango;
	}	
	
	ef_editable_fecha.prototype.iniciar = function(id, controlador) {
		ef.prototype.iniciar.call(this, id, controlador);
		this._mascara = new mascara_fecha(this._forma_mascara);
		this._mascara.attach(this.input());
	};
	
	ef_editable_fecha.prototype.get_estado = function() {
		return this.input().value;
	};
	
	/**
	 * Retorna el estado actual como un objeto javascript <a href=http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Global_Objects:Date>Date</a>
	 * @type Date
	 */
	ef_editable_fecha.prototype.fecha = function() {
		if (this.tiene_estado() && this.validar()) {
			var arr = this.get_estado().split('/');
			return new Date(arr[2], arr[1] - 1, arr[0]);
		}
		return null;
	};
	
	/**
	 *	Retorna la diferencia en años entre la fecha actual y la del campo
	 *  @param {Date} fecha_base Fecha base para el calculo, por defecto la actual del browser
	 */
	ef_editable_fecha.prototype.calcular_edad = function(fecha_base) {
		if (! isset(fecha_base)) {
	    	fecha_base=new Date();
		}
	    var array_fecha = this.get_estado().split("/");
	    //si el array no tiene tres partes, la fecha es incorrecta
	    if (array_fecha.length!=3) {
	       return false;
	    }
	    //compruebo que los ano, mes, dia son correctos
	    var ano = parseInt(array_fecha[2], 10);
	    if (isNaN(ano)) {
	       return false;
	    }
	    var mes = parseInt(array_fecha[1], 10);
	    if (isNaN(mes)) {
	       return false;
	    }
	    var dia = parseInt(array_fecha[0], 10);
	    if (isNaN(dia)) {
	       return false;
	    }
	    //si el año de la fecha que recibo solo tiene 2 cifras hay que cambiarlo a 4
	    if (ano<=99) {
	       ano +=1900;
	    }
	    //resto los años de las dos fechas
	    edad=fecha_base.getFullYear()- ano - 1; //-1 porque no se si ha cumplido años ya este año
	    //si resto los meses y me da menor que 0 entonces no ha cumplido años. Si da mayor si ha cumplido
	    if (fecha_base.getMonth() + 1 - mes < 0) { //+ 1 porque los meses empiezan en 0
	       return edad;
	    }
	    if (fecha_base.getMonth() + 1 - mes > 0) {
	       return edad+1;
	    }
	    //entonces es que eran iguales. miro los dias
	    //si resto los dias y me da menor que 0 entonces no ha cumplido años. Si da mayor o igual si ha cumplido
	    if (fecha_base.getUTCDate() - dia >= 0) {
	       return edad + 1;
	    }
	    return edad;		
	};

	/**
	 * Cambia el estado del ef en base a un objeto Date de javascript
	 */
	ef_editable_fecha.prototype.set_fecha = function(fecha) {	
		 this.set_estado(fecha.getDate() + '/' + (fecha.getMonth()+1) + '/' + fecha.getFullYear());
	};
	
	ef_editable_fecha.prototype.validar = function() {
		if (! ef_editable.prototype.validar.call(this)) {
			return false;
		}
		var estado = this.get_estado();
		try {
			var valida = validar_fecha(estado, false);
		} catch (e) {
			valida = "no contiene una fecha válida";
		}
		if (valida !== true) {
			this._error = valida;
		    return false;
		}		
		if (estado !== '' && this._rango_valido) {
			var arr = estado.split('/');
			var actual = new Date(arr[2], arr[1] - 1, arr[0]);
			if (actual < this._rango_valido[0]) {
				var piso = this._rango_valido[0].getDate() + '/' + (this._rango_valido[0].getMonth()+1) + '/' + this._rango_valido[0].getFullYear();
				this._error = 'Debe ser mayor al ' + piso;
				return false;
			}
			if (actual > this._rango_valido[1]) {
				var techo = this._rango_valido[1].getDate() + '/' + (this._rango_valido[1].getMonth()+1) + '/' + this._rango_valido[1].getFullYear();
				this._error = 'Debe ser menor al ' + techo;
				return false;
			}			
		}
		return true;
	};	
	
	/**
	 * Retorna el tag html que contiene el link para abrir el calendario gráfico de selección de fecha
	 */
	ef_editable_fecha.prototype.vinculo = function() {
		return document.getElementById('link_' + this._id_form);
	};
	
	ef_editable_fecha.prototype.set_solo_lectura = function(solo_lectura) {
		solo_lectura = (typeof solo_lectura != 'undefined' && solo_lectura);
		ef_editable.prototype.set_solo_lectura.call(this, solo_lectura);
		var vinculo = this.vinculo();
		if (isset(vinculo)) {
			vinculo.style.visibility = (solo_lectura) ? "hidden" : "visible";
		}
	};

	// ########################################################################################################
	// ########################################################################################################

ef_editable_fecha_hora.prototype = new ef_editable();
ef_editable_fecha_hora.prototype.constructor = ef_editable_fecha_hora;

	/**
	* @class Elemento editable que permite ingresar fechas.<br>
	* Para esto utiliza una m?scara <em>dd/mm/yyyy</em>
	* @constructor
	* @phpdoc Componentes/Efs/toba_ef_editable_fecha toba_ef_editable_fecha
	*/
	function ef_editable_fecha_hora(id_form, etiqueta, obligatorio, colapsable, masc, expreg, rango)	{
		ef_editable.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsable,null, expreg);						//Llamo al padre

		//Creo los dos elementos que voy a manejar aca para el caso
		this._forma_mascara_fecha = (masc) ? masc : 'dd/mm/yyyy';
		this._forma_mascara_hora = '##:##';
		this._rango_valido = rango;
	}

	ef_editable_fecha_hora.prototype.iniciar = function(id, controlador) {
		ef.prototype.iniciar.call(this, id, controlador);
		var arr_inputs = this.input();

		this._mascara_fecha = new mascara_fecha(this._forma_mascara_fecha);
		this._mascara_fecha.attach(arr_inputs[0]);

		this._mascara_hora = new mascara_generica(this._forma_mascara_hora);
		this._mascara_hora.attach(arr_inputs[1]);
	};

	/**
	 * Devuelve un arreglo con una primera componente referenciando a la componente fecha
	 * y una segunda componente referenciando a la componente hora.
	 *@type Array
	 */
	ef_editable_fecha_hora.prototype.input = function() {
		var parte_fecha = this._id_form + '_fecha';
		var parte_hora =  this._id_form + '_hora';

		var arreglo = [];
		arreglo.push(document.getElementById(parte_fecha));
		arreglo.push(document.getElementById(parte_hora));
		return arreglo;
	}

	/**
	 * Devuelve un arreglo con los valores para la fecha y hora.
	 *@type Array
	 */
	ef_editable_fecha_hora.prototype.get_estado = function() {
		var arreglo = this.input();
		return [arreglo[0].value, arreglo[1].value];
	};


	/**
	* Retorna el estado actual como un objeto javascript <a href=http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Global_Objects:Date>Date</a>
	* @type Date
	*/
	ef_editable_fecha_hora.prototype.fecha = function() {
		if (this.tiene_estado() && this.validar()) {
			var arr = this.get_estado();
			var componentes = arr[0].split('/');
			return new Date(componentes[2], componentes[1] - 1, componentes[0]);
		}
		return null;
	};

	/**
	* Retorna el estado actual como un objeto javascript <a href=http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Global_Objects:Date>Date</a>
	* @type Date
	*/
	ef_editable_fecha_hora.prototype.hora = function() {
		if (this.tiene_estado() && this.validar()) {
			var arr = this.get_estado();
			var componentes = arr[1].split(':');
			return new Date(0,0,0, componentes[1], componentes[0]);
		}
		return null;
	};

	/**
	* Retorna el estado actual como un objeto javascript <a href=http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Global_Objects:Date>Date</a>
	* @type Date
	*/
	ef_editable_fecha_hora.prototype.get_fecha_hora = function() {
		if (this.tiene_estado() && this.validar()) {
			var arr = this.get_estado();
			var comp_fecha= arr[0].split('/');
			var comp_hora = arr[1].split(':');
			return new Date(comp_fecha[2], comp_fecha[1] - 1, comp_fecha[0], comp_hora[0], comp_hora[1]);
		}
		return null;
	};

	/**
	* Cambia el estado del ef en base a un objeto Date de javascript
	*/
	ef_editable_fecha_hora.prototype.set_fecha = function(fecha) {
		var arr = this.get_estado();
		arr[0] = fecha.getDate() + '/' + (fecha.getMonth()+1) + '/' + fecha.getFullYear();
		this.set_estado(arr);
	};

	/**
	 * Cambia el estado del ef en base a un string conteniendo la hora
	 */
	ef_editable_fecha_hora.prototype.set_hora = function(hora) {
		var arr = this.get_estado();
		arr[1] = hora;
		this.set_estado(arr);
	}

	ef_editable_fecha_hora.prototype.es_oculto = function() {
		var nodo = this.nodo();
		if (! nodo) {
			nodo = this.input();
		}
		var inputs = this.input();
		var input_oculto = (inputs[0] && inputs[0].style && inputs[0].style.display == 'none');
		return nodo.style.display == 'none' || input_oculto;
	};

	/**
	 *	Recibe un arreglo cuya primera componente pertenece a la parte de la fecha
	 * y con segunda componente perteneciente a la porcion de la hora.
	 */
	ef_editable_fecha_hora.prototype.set_estado = function (nuevo) {
		if (nuevo === null) {
			return this.resetear_estado();
		}
		var componentes = this.input();
		var hay_estado = true;

		//Seteo la componente Fecha
		if (isset(nuevo[0])) {
			if (this._mascara_fecha) {
				var valor_f = this._mascara_fecha.format(nuevo[0], false, true);
				componentes[0].value = valor_f;
				var desc_f = document.getElementById(this._id_form + '_desc');
				if (desc_f) {
					desc_f.value = valor_f;
				}
			}
		}else {
			componentes[0].value = null;
			hay_estado = false;
		}

		//Seteo la componente Hora
 		if (isset(nuevo[1])) {
			if (this._mascara_hora) {
				var valor_h = this._mascara_hora.format(nuevo[1], false, true);
				componentes[1].value = valor_h;
				var desc_h = document.getElementById(this._id_form + '_desc');
				if (desc_h) {
					desc_h.value = valor_h;
				}
			}
 		} else {
			componentes[1].value = null;
			hay_estado = false;
		}
		
		if (! hay_estado && componentes[0].onchange) {
			componentes[0].onchange();
		}
		if (componentes[0].onblur) {
			componentes[0].onblur();
		}
		if (componentes[1].onblur) {
			componentes[1].onblur();
		}
	}

	/**
	 * Cambia las opciones disponibles de selección
	 * Solo se aplica si el elemento maneja una serie de opciones desde donde se elige su estado
	 */
	ef_editable_fecha_hora.prototype.set_opciones = function(opciones) {
		this.set_estado(opciones);
		this.activar();
		/*if (this.input()[0].onblur) {
			this.input()[0].onblur();
		}*/
	};

	ef_editable_fecha_hora.prototype.tiene_estado = function() {
		var estado = this.get_estado();
		return ((estado[0] != '') && (estado[1] != ''));
	};

	ef_editable_fecha_hora.prototype.resetear_estado = function() {
		this.set_estado([null,null]);
	};

	ef_editable_fecha_hora.prototype.validar = function() {
		var estado = this.get_estado();
		if (! ef.prototype.validar.call(this)) {		//Valido directamente contra ef.
			return false;
		}		

		//Valido obligatoriedad del dato
		if (this._obligatorio && (ereg_nulo.test(estado[0]) || ereg_nulo.test(estado[1]))) {
			this._error = 'es obligatorio.';
		    return false;
		}
		if (estado !== '' && isset(this._expreg) && this._expreg !== '') {
			var erv, temp = false;
			erv = this._get_er_validacion();
			if (erv !== false) {				
				temp = new RegExp(erv['er'], erv['flags']).test(estado);
			}
			if (! temp) {
				this._error = 'no es válido';
				return false;
			}
		}

		//Valido que la fecha y la hora esten dentro de los valores correctos.
		try {
			var valida = validar_fecha(estado[0], false);
		} catch (e) {
			valida = "no contiene una fecha válida";
		}
		if (valida !== true) {
			this._error = valida;
			return false;
		}
		valida = this.validar_hora(estado[1], false);
		if (valida !== true) {
			this._error = valida;
			return false;
		}

		//Valido contra un rango valido (que incluye hora) si se lo especifico
		if (estado.length == '2' && this._rango_valido) {
			var arr = estado[0].split('/');
			var hora = estado[1].split(':');
			var actual = new Date(arr[2], arr[1] - 1, arr[0], hora[0], hora[1]);
			if (actual < this._rango_valido[0]) {
				var piso = this._rango_valido[0].getDate() + '/' + (this._rango_valido[0].getMonth()+1) + '/' + this._rango_valido[0].getFullYear() + ' ' + this._rango_valido[0].getHours() + ':' + this._rango_valido[0].getMinutes();
				this._error = 'Debe ser mayor al ' + piso;
				return false;
			}
			if (actual > this._rango_valido[1]) {
				var techo = this._rango_valido[1].getDate() + '/' + (this._rango_valido[1].getMonth()+1) + '/' + this._rango_valido[1].getFullYear()+ ' ' + this._rango_valido[1].getHours() + ':' + this._rango_valido[1].getMinutes();
				this._error = 'Debe ser menor al ' + techo;
				return false;
			}
		}
		return true;
	};

	/**
	 * Valida que la hora tenga el formato y valores correctos
	 * @ignore
	 */
	ef_editable_fecha_hora.prototype.validar_hora = function(hora, mostrar_error) {
		if (mostrar_error == null) mostrar_error = true;
		if (hora.length == '0') return true;
		
		var comp = hora.split(':');
		if (comp.length != '2') {
				return cal_error ("Formato de Hora Inválido: '" + hora + "'. El Formato Aceptado es hh:mm.", mostrar_error);
		}

		var er_time =  new RegExp(/^([0-1][0-9]|[2][0-3]):([0-5][0-9])$/);
		if (! er_time.test(hora)) {
			return cal_error("Formato de Hora Inválido: '" + hora + "'. El Formato Aceptado es hh:mm.",  mostrar_error);
		}
		return true;
	}

	/**
	* Retorna el tag html que contiene el link para abrir el calendario gr?fico de selecci?n de fecha
	*/
	ef_editable_fecha_hora.prototype.vinculo = function() {
		return document.getElementById('link_' + this._id_form);
	};

	/**
	 *	Setea si el componente esta solo_lectura o no.
	 */
	ef_editable_fecha_hora.prototype.set_solo_lectura = function(solo_lectura) {
		solo_lectura = (typeof solo_lectura != 'undefined' && solo_lectura);
		var input = this.input();
		if (isset(input[0])) {
			input[0].readOnly = solo_lectura;
			input[1].readOnly = solo_lectura;
			this._solo_lectura = solo_lectura;
		}
		var vinculo = this.vinculo();
		if (isset(vinculo)) {
			vinculo.style.visibility = (solo_lectura) ? "hidden" : "visible";
		}
	};

	/**
	 *	Devuelve el valor formateado de acuerdo a la mascara
	 */
	ef_editable_fecha_hora.prototype.formatear_valor = function(valor) {
		if (this._forma_mascara_fecha) {
			return this._forma_mascara_fecha.format(valor[0], false, true) +  ' ' + this._forma_mascara_hora.format(valor[1], false, true);
		} else {
			return valor;
		}
	};

	/**
	 * Retorna el tabIndex actual del elemento.
	 * Este número es utilizado para ciclar por los distintos elementos usando la tecla TAB
	 * @type int
	 * @return string
	 */
	ef_editable_fecha_hora.prototype.get_tab_index = function () {
		var input = this.input();
		if (isset(input[0])) {
			return input[0].tabIndex;
		}
	};

	/**
	 * Cambia el tabIndex actual del elemento.
	 * Este número es utilizado para ciclar por los distintos elementos usando la tecla TAB
	 * @param {int} tab_index Nuevo orden
	 */
	ef_editable_fecha_hora.prototype.set_tab_index = function(tab_index) {
		if (this.input()) {
			this.input().tabIndex = tab_index;
		}
	};

	/**
	 * Intenta forzar el foco visual al elemento, esto generalmente pone el cursor y la atención visual en el elemento
	 * @type boolean
	 * @return Verdadero si se pudo seleccionar/dar foco, falso en caso contrario
	 */
	ef_editable_fecha_hora.prototype.seleccionar = function () {
		try {
			var inputs = this.input();
			inputs[0].focus();
			inputs[0].select();
			return true;
		} catch(e) {
			return false;
		}
	};

	// ########################################################################################################
	// ########################################################################################################

ef_editable_hora.prototype = new ef_editable();
ef_editable_hora.prototype.constructor = ef_editable_hora;

	/**
	* @class Elemento editable que permite ingresar fechas.<br>
	* Para esto utiliza una m?scara <em>dd/mm/yyyy</em>
	* @constructor
	* @phpdoc Componentes/Efs/toba_ef_editable_fecha toba_ef_editable_fecha
	*/
	function ef_editable_hora(id_form, etiqueta, obligatorio, colapsable, masc, expreg, rango)	{
		ef_editable.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsable,null, expreg);						//Llamo al padre

		//Creo los dos elementos que voy a manejar aca para el caso
		this._forma_mascara_hora = '##:##';
		this._rango_valido = rango;
	}

	ef_editable_hora.prototype.iniciar = function(id, controlador) {
		ef.prototype.iniciar.call(this, id, controlador);
		var input_obj = this.input();

		this._mascara_hora = new mascara_generica(this._forma_mascara_hora);
		this._mascara_hora.attach(input_obj);
	};

	/**
	 * Devuelve un arreglo con una primera componente referenciando a la componente fecha
	 * y una segunda componente referenciando a la componente hora.
	 *@type Array
	 */
	/*ef_editable_hora.prototype.input = function() {
		var parte_hora =  this._id_form;
		return  document.getElementById(parte_hora);
	}*/

	/**
	 * Devuelve un arreglo con los valores para la fecha y hora.
	 *@type Array
	 */
	ef_editable_hora.prototype.get_estado = function() {
		return this.input().value;
	};

	/**
	* Retorna el estado actual como un objeto javascript <a href=http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Global_Objects:Date>Date</a>
	* @type Date
	*/
	ef_editable_hora.prototype.hora = function() {
		if (this.tiene_estado() && this.validar()) {
			var estado = this.get_estado();
			var componentes = estado.split(':');
			return new Date(0,0,0, componentes[0], componentes[1]);
		}
		return null;
	};

	/**
	 * Cambia el estado del ef en base a un string conteniendo la hora
	 */
	ef_editable_hora.prototype.set_hora = function(hora) {
		this.set_estado(hora);
	}

	ef_editable_hora.prototype.es_oculto = function() {
		var nodo = this.nodo();
		if (! nodo) {
			nodo = this.input();
		}
		var inputs = this.input();
		var input_oculto = (inputs && inputs.style && inputs.style.display == 'none');
		return nodo.style.display == 'none' || input_oculto;
	};

	/**
	 *	Recibe un arreglo cuya primera componente pertenece a la parte de la fecha
	 * y con segunda componente perteneciente a la porcion de la hora.
	 */
	ef_editable_hora.prototype.set_estado = function (nuevo) {		
		var componente = this.input();
		var hay_estado = true;

		//Seteo la componente Hora
 		if (isset(nuevo)) {
			if (this._mascara_hora) {
				var valor_h = this._mascara_hora.format(nuevo, false, true);
				componente.value = valor_h;
				var desc_h = document.getElementById(this._id_form + '_desc');
				if (desc_h) {
					desc_h.value = valor_h;
				}
			}
 		} else {
			componente.value = null;
			hay_estado = false;
		}

		if (! hay_estado && componente.onchange) {
			componente.onchange();
		}
		if (componente.onblur) {
			componente.onblur();
		}
	}

	/**
	 * Cambia las opciones disponibles de selección
	 * Solo se aplica si el elemento maneja una serie de opciones desde donde se elige su estado
	 */
	ef_editable_hora.prototype.set_opciones = function(opciones) {
		this.set_estado(opciones);
		this.activar();
	};

	ef_editable_hora.prototype.resetear_estado = function() {
		this.set_estado(null);
	};

	ef_editable_hora.prototype.validar = function() {
		var estado = this.get_estado();
		if (! ef.prototype.validar.call(this)) {		//Valido directamente contra ef.
			return false;
		}

		//Valido obligatoriedad del dato
		if (this._obligatorio && ereg_nulo.test(estado)) {
			this._error = 'es obligatorio.';
		    return false;
		}
		if (estado !== '' && isset(this._expreg) && this._expreg !== '') {
			var erv, temp = false;
			erv = this._get_er_validacion();
			if (erv !== false) {				
				temp = new RegExp(erv['er'], erv['flags']).test(estado);
			}
			if (! temp) {
				this._error = 'no es válido';
				return false;
			}
		}

		//Valido que la hora este dentro de los valores correctos.
		valida = this.validar_hora(estado, false);
		if (valida !== true) {
			this._error = valida;
			return false;
		}

		//Valido contra un rango valido (que incluye hora) si se lo especifico
		if (estado.length == '1' && this._rango_valido) {
			var hora = estado.split(':');
			var actual = new Date(0, 0, 0, hora[0], hora[1]);
			if (actual < this._rango_valido[0]) {
				var piso = this._rango_valido[0].getHours() + ':' + this._rango_valido[0].getMinutes();
				this._error = 'Debe ser mayor al ' + piso;
				return false;
			}
			if (actual > this._rango_valido[1]) {
				var techo = this._rango_valido[1].getHours() + ':' + this._rango_valido[1].getMinutes();
				this._error = 'Debe ser menor al ' + techo;
				return false;
			}
		}
		return true;
	};

	/**
	 * Valida que la hora tenga el formato y valores correctos
	 * @ignore
	 */
	ef_editable_hora.prototype.validar_hora = function(hora, mostrar_error) {
		if (mostrar_error == null) mostrar_error = true;
		if (hora.length == '0') return true;

		var comp = hora.split(':');
		if (comp.length != '2') {
				return cal_error ("Formato de Hora Inválido: '" + hora + "'. El Formato Aceptado es hh:mm.", mostrar_error);
		}

		var er_time =  new RegExp(/^([0-1][0-9]|[2][0-3]):([0-5][0-9])$/);
		if (! er_time.test(hora)) {
			return cal_error("Formato de Hora Inválido: '" + hora + "'. El Formato Aceptado es hh:mm.",  mostrar_error);
		}
		return true;
	}

	/**
	 *	Setea si el componente esta solo_lectura o no.
	 */
	ef_editable_hora.prototype.set_solo_lectura = function(solo_lectura) {
		solo_lectura = (typeof solo_lectura != 'undefined' && solo_lectura);
		var input = this.input();
		if (isset(input)) {
			input.readOnly = solo_lectura;
			this._solo_lectura = solo_lectura;
		}
	};

	/**
	 *	Devuelve el valor formateado de acuerdo a la mascara
	 */
	ef_editable_hora.prototype.formatear_valor = function(valor) {
		if (this._forma_mascara_hora) {
			return this._forma_mascara_hora.format(valor, false, true);
		} else {
			return valor;
		}
	};

	/**
	 * Retorna el tabIndex actual del elemento.
	 * Este número es utilizado para ciclar por los distintos elementos usando la tecla TAB
	 * @type int
	 * @return string
	 */
	ef_editable_hora.prototype.get_tab_index = function () {
		var input = this.input();
		if (isset(input)) {
			return input.tabIndex;
		}
	};

	/**
	 * Cambia el tabIndex actual del elemento.
	 * Este número es utilizado para ciclar por los distintos elementos usando la tecla TAB
	 * @param {int} tab_index Nuevo orden
	 */
	ef_editable_hora.prototype.set_tab_index = function(tab_index) {
		if (this.input()) {
			this.input().tabIndex = tab_index;
		}
	};

	/**
	 * Intenta forzar el foco visual al elemento, esto generalmente pone el cursor y la atención visual en el elemento
	 * @type boolean
	 * @return Verdadero si se pudo seleccionar/dar foco, falso en caso contrario
	 */
	ef_editable_hora.prototype.seleccionar = function () {
		try {
			var inputs = this.input();
			inputs.focus();
			inputs.select();
			return true;
		} catch(e) {
			return false;
		}
	};

// ########################################################################################################
// ########################################################################################################

ef_textarea.prototype = new ef_editable();
ef_textarea.prototype.constructor = ef_textarea;

	/**
	 * @class Elemento editable que permite ingresar textos largos, equivalente a un tag textarea
	 * @constructor
	 * @phpdoc Componentes/Efs/toba_ef_editable_textarea toba_ef_editable_textarea
	 */
	function ef_textarea(id_form, etiqueta, obligatorio, colapsado, masc, exp_reg, maximo, ajustable)	{
		ef_editable.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado, masc, exp_reg);
		this._maximo = maximo;
		this._ajustable = ajustable;
	}	
	
	ef_textarea.prototype.iniciar = function(id, controlador) { 
		ef.prototype.iniciar.call(this, id, controlador);
		if (this._ajustable && !ie) {
			resizeTa.agregar_elemento(this.input());
			resizeTa.init();
		}
	};
	
	ef_textarea.prototype.validar = function() {
		if (! ef_editable.prototype.validar.call(this)) {
			return false;
		}
		var elem = this.input();
		if (this._maximo !== null && elem.value.length > this._maximo) {
			elem.value = elem.value.substring(0, this._maximo);
		}
		return true;
	};

// ########################################################################################################
// ########################################################################################################
	
toba.confirmar_inclusion('efs/ef_editable');
