
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
	 * Este valor es retornado sin formato, aun si el elemento posee una m�scara asociada.
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
		if (estado != '' && isset(this._expreg) && this._expreg != '') {
			var temp = this._expreg + '.test("' + estado + '")';
			if (! eval(temp)) {
				this._error = 'no es v�lido';
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
		if (this._mascara) {
			var valor = this._mascara.format(nuevo, false, true);
			this.input().value = valor;
			var desc = document.getElementById(this._id_form + '_desc');
			if (desc) {
				desc.value = valor;
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

	
// ########################################################################################################
// ########################################################################################################
	
ef_editable_numero.prototype = new ef_editable();
ef_editable_numero.prototype.constructor = ef_editable_numero;

	/**
	 * @class Elemento editable que s�lo permite ingresar n�meros.<br>
	 * Para esto utiliza en forma predeterminada una m�scara <em>###.###,##</em>
	 * @constructor
	 * @phpdoc Componentes/Efs/toba_ef_editable_numero toba_ef_editable_numero
	 */
	function ef_editable_numero(id_form, etiqueta, obligatorio, colapsado, expreg, masc, rango, mensaje) {
		ef_editable.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado, expreg);
		if (masc) {
			this._forma_mascara = (masc.trim().toLowerCase() != 'no') ? masc : null;
		} else {
			this._forma_mascara = '###.###,##';
		}
		this._rango = rango;					//[0] => inferior [1] => superior
		this._mensaje = mensaje;				//Mensaje a mostrar cuando no se valida el n�mero
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
			this._error = 'debe ser num�rico.';
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

	/**
	 * @class Elemento editable que s�lo permite ingresar n�meros que representan un valor monetario.<br>	 
	 * Para esto utiliza en forma predeterminada una m�scara <em>$ ###.###,00</em>
	 * @constructor
	 * @phpdoc Componentes/Efs/toba_ef_editable_moneda toba_ef_editable_moneda
	 */
	function ef_editable_moneda(id_form, etiqueta, obligatorio, colapsado, expreg, masc, rango, mensaje)	{
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

	/**
	 * @class Elemento editable que s�lo permite ingresar n�meros que representan un porcentaje
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

	/**
	 * @class  Elemento editable que permite ingresar contrase�as, con o sin campo de confirmaci�n.
	 * @constructor
	 * @phpdoc Componentes/Efs/toba_ef_editable_clave toba_ef_editable_clave
	 */
	function ef_editable_clave(id_form, etiqueta, obligatorio, colapsado)	{
		ef_editable.prototype.constructor.call(this, id_form, etiqueta, obligatorio, colapsado);
	}

	ef_editable_clave.prototype.validar = function() {
		if (! ef_editable.prototype.validar.call(this)) {
			return false;
		}
		var orig = this.input();
		var test = this.input_hermano();		
		if (test && orig.value != test.value) {
			this._error = ': Las contrase�as no coinciden.';
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
	 *	Si en el editor se definio el elemento con confirmaci�n de clave, este m�todo retorna el input html asociado al campo de confirmaci�n
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
	
// ########################################################################################################
// ########################################################################################################

ef_editable_fecha.prototype = new ef_editable();

	/**
	 * @class Elemento editable que permite ingresar fechas.<br>
	 * Para esto utiliza una m�scara <em>dd/mm/yyyy</em>
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
	 *	Retorna la diferencia en a�os entre la fecha actual y la del campo
	 *  @param {Date} fecha_base Fecha base para el calculo, por defecto la actual del browser
	 */
	ef_editable_fecha.prototype.calcular_edad = function(fecha_base) {
		if (! isset(fecha_base)) {
	    	fecha_base=new Date()
		}
	    var array_fecha = this.get_estado().split("/")
	    //si el array no tiene tres partes, la fecha es incorrecta
	    if (array_fecha.length!=3)
	       return false
	    //compruebo que los ano, mes, dia son correctos
	    var ano
	    ano = parseInt(array_fecha[2], 10);
	    if (isNaN(ano))
	       return false
	    var mes
	    mes = parseInt(array_fecha[1], 10);
	    if (isNaN(mes))
	       return false
	    var dia
	    dia = parseInt(array_fecha[0], 10);
	    if (isNaN(dia))
	       return false
	    //si el a�o de la fecha que recibo solo tiene 2 cifras hay que cambiarlo a 4
	    if (ano<=99)
	       ano +=1900
	    //resto los a�os de las dos fechas
	    edad=fecha_base.getFullYear()- ano - 1; //-1 porque no se si ha cumplido a�os ya este a�o
	    //si resto los meses y me da menor que 0 entonces no ha cumplido a�os. Si da mayor si ha cumplido
	    if (fecha_base.getMonth() + 1 - mes < 0) //+ 1 porque los meses empiezan en 0
	       return edad
	    if (fecha_base.getMonth() + 1 - mes > 0)
	       return edad+1
	    //entonces es que eran iguales. miro los dias
	    //si resto los dias y me da menor que 0 entonces no ha cumplido a�os. Si da mayor o igual si ha cumplido
	    if (fecha_base.getUTCDate() - dia >= 0)
	       return edad + 1
	    return edad		
	}

	
	ef_editable_fecha.prototype.validar = function() {
		if (! ef_editable.prototype.validar.call(this)) {
			return false;
		}
		var estado = this.get_estado();
		try {
			var valida = validar_fecha(estado, false);
		} catch (e) {
			valida = "no contiene una fecha v�lida";
		}
		if (valida !== true) {
			this._error = valida;
		    return false;
		}		
		if (estado != '' && this._rango_valido) {
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
	 * Retorna el tag html que contiene el link para abrir el calendario gr�fico de selecci�n de fecha
	 */
	ef_editable_fecha.prototype.vinculo = function() {
		return document.getElementById('link_' + this._id_form);
	};
	
	ef_editable_fecha.prototype.set_solo_lectura = function(solo_lectura) {
		solo_lectura = (typeof solo_lectura != 'undefined' && solo_lectura);
		ef_editable.prototype.set_solo_lectura.call(this, solo_lectura);
		this.vinculo().style.visibility = (solo_lectura) ? "hidden" : "visible";
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
