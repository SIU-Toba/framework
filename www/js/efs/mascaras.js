function _MaskAPI(){
	this.version = "0.4a-mod";
	this.instances = 0;
	this.objects = {};
}
MaskAPI = new _MaskAPI();


/*************************************
*			MASCARA
**************************************/
function mascara(m, t, param){
	this._mascara = m;
	this.type = (typeof t == "string") ? t : "string";
	this.error = [];
	this.errorCodes = [];
	this.value = "";
	this.strippedValue = "";
	this.allowPartial = false;
	this.id = MaskAPI.instances++;
	this.ref = "MaskAPI.objects['" + this.id + "']";
	this.param = param;
	MaskAPI.objects[this.id] = this;
}

mascara.prototype.attach = function (o){
	addEvent(o, "onkeydown", "return " + this.ref + ".pre_evento_tecla(event, this);", true);	
	addEvent(o, "onkeyup", "return " + this.ref + ".getKeyPress(event, this);", true);
	addEvent(o, "onblur", "this.value = " + this.ref + ".format(this.value);", true);
};

mascara.prototype.getKeyPress = function (e, o, _u){
	if (o.readOnly || o.disabled) {
		return false;	
	}
	this.allowPartial = true;
	var xe = new qEvent(e);
//	var k = String.fromCharCode(xe.keyCode);
	if( (xe.keyCode > 47) || (_u === true) || (xe.keyCode == 8 || xe.keyCode == 46) ){
		this.onKeyPress(o.value, xe.isDelete());
		o.value = this.value;
	}
	this.allowPartial = false;
	return true;
};

//Permite ajustar el valor previo a la ejecución al key press
mascara.prototype.pre_evento_tecla = function (evento, objeto) {
	return true;
};

mascara.prototype.throwError = function (c, e, v) {
	this.error[this.error.length] = e;
	this.errorCodes[this.errorCodes.length] = c;
	if( typeof v == "string" ) { return v; }
	return true;
};

mascara.prototype.ok = function () {
	return this.error.length === 0;
};

mascara.prototype.valor_sin_formato = function () {
	return this.strippedValue;
};


/*************************************
*		MASCARA NUMERO
**************************************/

mascara_numero.prototype = new mascara();
def = mascara_numero.prototype;
def.constructor = mascara_numero;

function mascara_numero (_mascara, modelo) {
	mascara.prototype.constructor.call(this, _mascara, 'number');
	this.modelo = (modelo) ? modelo : "ar";
	if (this.modelo == 'ar') {
		this._mascara = this._mascara.intercambiar_caracteres(',', '.');
	}
	this.dec = '.';														//Simbolo Decimal
	this.componentes = ['#', '0', '+', '-', ')', '(']; 			//Componentes disponibles para mascaras numericas	
	this.moneda = this.get_moneda();
//	this.validar_mascara();
}

def.get_moneda = function () {
	var inicio_num = this._mascara.primer_ocurrencia(this.componentes);
	if (this._mascara.substring(0, inicio_num) !== '') {
		return this._mascara.substring(0, inicio_num);
	} else {
		return null;
	}
};

def.format = function (s, d, inicial){
	s = s.toString();
	if (inicial) {
		s = s.intercambiar_caracteres(',', '.');
	}
	this.value = this.setNumber(s, d);
	return this.value;
};


//Permite ajustar el valor previo a la ejecución al key press
def.pre_evento_tecla = function (evento, objeto) {
	var xe = new qEvent(evento);
	if (! xe.isDelete()) {
		objeto.value = this.ajustar_puntuacion(objeto.value);
	}
	return true;
};

def.ajustar_puntuacion = function(valor) {
	var nuevo_valor = valor;
	if (this.modelo == 'ar') {
		//Se toma el ultimo punto como una coma
		var ultimo = valor.lastIndexOf('.');
		var primer = valor.indexOf('.');
		if (ultimo > -1 && ultimo == valor.length - 1) {
			nuevo_valor = valor.substring(0, ultimo) + ',';
		} else if (primer === 0) {
			nuevo_valor = ',' + valor.substring(1, valor.length);
		}
	}
	return nuevo_valor;
};


def.onKeyPress = function(valor, borro) {
	this.value = this.format(valor, borro);
};

def.validar_mascara = function() {
	var er_mask = new RegExp('^[\\' + this.moneda + ']?((\\' + this.moneda + '?[\\+-]?([0#]{1,3},)?[0#]*(\\' + this.dec + '[0#]*)?)|([\\+-]?\\([\\+-]?([0#]{1,3},)?[0#]*(\\' + this.dec + '[0#]*)?\\)))$');
	if( ! er_mask.test(this._mascara) ) {
		return this.throwError(1, "Máscara inválida.");
	}
};

def.setNumber = function(_v, _d){
	//Intercambio inicial del punto y la coma
	var nuevo_valor = this.ajustar_puntuacion(_v);
	
	if (this.modelo == 'ar') {
		nuevo_valor = nuevo_valor.intercambiar_caracteres(',', '.');
	}

	//Se limpia el valor
	var v = String(nuevo_valor).replace(/[^\d.-]*/gi, "");
	//Asegura que solamente hay un punto decimal
	v = v.replace(new RegExp('\\' + this.dec), "d").replace(new RegExp('\\' + this.dec, 'g'), "").replace(/d/, this.dec);

	//¿Se borro?
	if( (_d === true) && (v.length == this.strippedValue.length) ) {
		v = v.substring(0, v.length-1);
	}

	if( this.allowPartial && (v.replace(/[^0-9]/, "").length === 0) ) {
		return v;
	}
	if (v==='-'){
		return v;
	}
	if (v.substring(v.length-1, v.length)=='-') {
		return v.substring(0, v.length-1);
	}
	this.strippedValue = v;

	//¿Es un número?
	if( v.length === 0 )	{//Si es vacío, retornar vacío
		return v;
	}
	var vn = Number(v);
	if( isNaN(vn) ) {
		this.strippedValue = NaN;
		return this.throwError(2, "The value entered was not a number.", _v);
	}

	// get the value before the decimal point
	var v_antes_decimal = String(Math.abs((v.indexOf(this.dec) > -1 ) ? v.split(this.dec)[0] : v));
	// get the value after the decimal point
	var v_despues_decimal = (v.indexOf(this.dec) > -1) ? v.split(this.dec)[1] : "";
	var _vd = v_despues_decimal;

	var isNegative = (vn !== 0 && Math.abs(vn)*-1 == vn);

	// check for masking operations
	var show = {
		"$" : (this.moneda !== null),
		"(": (isNegative && (this._mascara.indexOf("(") > -1)),
		"+" : ( (this._mascara.indexOf("+") != -1) && !isNegative )
	};
	show["-"] = (isNegative && (!show["("] || (this._mascara.indexOf("-") != -1)));
	
	// replace all non-place holders from the mask
	masc_limpia = this._mascara.replace(/[^#0.,]*/gi, "");

	/*
		make sure there are the correct number of decimal places
	*/
	// get number of digits after decimal point in mask
	var dm = (masc_limpia.indexOf(this.dec) > -1 ) ? masc_limpia.split(this.dec)[1] : "";
	if( dm.length === 0 ){
		v_antes_decimal = String(Math.round(Number(v_antes_decimal)));
		v_despues_decimal = "";
	} else {
		// find the last zero, which indicates the minimum number
		// of decimal places to show
		var md = dm.lastIndexOf("0")+1;
		// if the number of decimal places is greater than the mask, then trunc off
		if( v_despues_decimal.length > dm.length ) {
			v_despues_decimal = String(Math.floor(Number(v_despues_decimal.substring(0, dm.length + 1))/10));
			v_despues_decimal = v_despues_decimal.pad(dm.length, '0', 'PAD_LEFT');
		} else { // otherwise, pad the string w/the required zeros
			while( v_despues_decimal.length < md ) {
				v_despues_decimal += "0";
			}
		}
	}

	/*
		pad the int with any necessary zeros
	*/
	// get number of digits before decimal point in mask
	var dig_antes_dec = (masc_limpia.indexOf(this.dec) > -1 ) ? masc_limpia.split(this.dec)[0] : masc_limpia;
	dig_antes_dec = dig_antes_dec.replace(/[^0#]+/gi, "");
	// find the first zero, which indicates the minimum length
	// that the value must be padded w/zeros
	var mv = dig_antes_dec.indexOf("0")+1;
	// if there is a zero found, make sure it's padded
	if( mv > 0 ){
		mv = dig_antes_dec.length - mv + 1;
		while( v_antes_decimal.length < mv ) {
			v_antes_decimal = "0" + v_antes_decimal;
		}
	}

	//¿Necesita poner puntos en los miles?
	if( /[#0]+,[#0]{3}/.test(masc_limpia) ){
		// add the commas as the place holder
		var x = [], i=0, n=Number(v_antes_decimal);
		while( n > 999 ){
			x[i] = "00" + String(n%1000);
			x[i] = x[i].substring(x[i].length - 3);
			n = Math.floor(n/1000);
			i++;
		}
		x[i] = String(n%1000);
		v_antes_decimal = x.reverse().join(",");
	}

	//Combinar los valores
	if( (v_despues_decimal.length > 0 && !this.allowPartial) || ((dm.length > 0) && this.allowPartial && (v.indexOf(this.dec) > -1) && (_vd.length >= v_despues_decimal.length)) ){
		v = v_antes_decimal + "." + v_despues_decimal;
	} else if( (dm.length > 0) && this.allowPartial && (v.indexOf(this.dec) > -1) && (_vd.length < v_despues_decimal.length) ){
		v = v_antes_decimal + this.dec + _vd;
	} else {
		v = v_antes_decimal;
	}

	var er_curr = new RegExp('(^[\\' + this.moneda + '])(.+)', 'gi');
	if( show.$ ) {v = this._mascara.replace(er_curr, this.moneda) + v;}
	if( show["+"] ) {v = "+" + v;}
	if( show["-"] ) {v = "-" + v;}
	if( show["("] ) {v = "(" + v + ")";}
	
	//Itercambio final del punto y la coma
	if (this.modelo == 'ar') {	
		v = v.intercambiar_caracteres('.', ',');
	}
	return v.trim();
};

/*************************************
*		MASCARA FECHA
**************************************/

mascara_fecha.prototype = new mascara();
mascara_fecha.prototype.constructor = mascara_fecha;
function mascara_fecha (_mascara) {
	mascara.prototype.constructor.call(this, _mascara, 'date');
}

mascara_fecha.prototype.format = function (s, d){
	this.value = this.setDate(s, d);
	return this.value;
};

mascara_fecha.prototype.onKeyPress = function(valor, borro) {

	this.value = this.setDateKeyPress(valor, borro);
};

mascara_fecha.prototype.setDate = function (_v){
	var v = _v, m = this._mascara;
	var a, e, mm, dd, yy, x, s;

	// split mask into array, to see position of each day, month & year
	a = m.split(/[^mdy]+/);
	// split mask into array, to get delimiters
	s = m.split(/[mdy]+/);
	// convert the string into an array in which digits are together
	e = v.split(/[^0-9]/);
	
	if( s[0].length === 0 ) { s.splice(0, 1);}

	for( var i=0; i < a.length; i++ ){
		x = a[i].charAt(0).toLowerCase();
		if( x == "m" ) { mm = parseInt(e[i], 10)-1;}
		else if( x == "d" ) { dd = parseInt(e[i], 10); }
		else if( x == "y" ) { yy = parseInt(e[i], 10); }
	}

	// if year is abbreviated, guess at the year
	if( String(yy).length < 3 ){
		yy = 2000 + yy;
		if( (new Date()).getFullYear()+20 < yy ) {yy = yy - 100;}
	}

	// create date object
	var d = new Date(yy, mm, dd);
	if( d.getDate() != dd ) { return this.throwError(1, "An invalid day was entered.", _v);}
	else if( d.getMonth() != mm ) { return this.throwError(2, "An invalid month was entered.", _v);}

	var nv = "";

	for( i=0; i < a.length; i++ ){
		x = a[i].charAt(0).toLowerCase();
		if( x == "m" ){
			mm++;
			if( a[i].length == 2 ){
				mm = "0" + mm;
				mm = mm.substring(mm.length-2);
			}
			nv += mm;
		} else if( x == "d" ){
			if( a[i].length == 2 ){
				dd = "0" + dd;
				dd = dd.substring(dd.length-2);
			}
			nv += dd;
		} else if( x == "y" ){
			if( a[i].length == 2 ) { nv += d.getYear(); }
			else { nv += d.getFullYear(); }
		}
		if( i < a.length-1 ) { nv += s[i]; }
	}
	this.strippedValue = nv;
	return nv;
};

//Parte un fecha solo conteniendo los digitos, en reemplazo del erroneo e = v.split(/[^0-9]/);
mascara_fecha.prototype.partir_fecha = function(fecha) {
	var res = [];
	var acumulado = '';
	for (i=0; i<fecha.length; i++) {
		if (isNaN(parseInt(fecha.charAt(i), 10))) {		//¿Es numero?
			if (acumulado !== '') {
				res.push(acumulado);
				acumulado = '';
			}
		} else {
			acumulado += fecha.charAt(i);
		}
	}
	if (acumulado !== '') {
		res.push(acumulado);
	}
	return res;
};

mascara_fecha.prototype.setDateKeyPress = function (_v, _d){
	var v = _v, m = this._mascara, k = v.charAt(v.length-1);
	var a, e, c, ml, vl, mm = "", dd = "", yy = "", x, p, z;

	if( _d === true ){
		while( (/[^0-9]/gi).test(v.charAt(v.length-1)) ) {
			v = v.substring(0, v.length-1);
		}
		if( (/[^0-9]/gi).test(this.strippedValue.charAt(this.strippedValue.length-1)) ) {
			v = v.substring(0, v.length-1);
		}
		if( v.length === 0 ) {
			return "";
		}
	}

	// split mask into array, to see position of each day, month & year
	a = m.split(/[^mdy]/);
	// split mask into array, to get delimiters
	s = m.split(/[mdy]+/);
	// mozilla wants to add an empty array element which needs removed
	if( s[0].length === 0 ) { s.splice(0,1); }
	// convert the string into an array in which digits are together
	e = this.partir_fecha(v);
	// position in mask
	p = (e.length > 0) ? e.length-1 : 0;
	// determine what mask value the user is currently entering
	c = a[p].charAt(0);
	// determine the length of the current mask value
	ml = a[p].length;
	for( var i=0; i < e.length; i++ ){
		x = a[i].charAt(0).toLowerCase();
		if( x == "m" ) { mm = parseInt(e[i], 10)-1; }
		else if( x == "d" ) { dd = parseInt(e[i], 10);}
		else if( x == "y" ) { yy = parseInt(e[i], 10);}
	}
	
	var nv = "";
	var j=0;

	for( i=0; i < e.length; i++ ){
		x = a[i].charAt(0).toLowerCase();
	
		if( x == "m" ){
			z = ((/[^0-9]/).test(k) && c == "m");
			mm++;
			if( (e[i].length == 2 && mm < 10) || (a[i].length == 2 && c != "m") || (mm > 1 && c == "m") || (z && a[i].length == 2) ){
				mm = "0" + mm;
				mm = mm.substring(mm.length-2);
			}
			vl = String(mm).length;
			ml = 2;
			nv += mm;
		} else if( x == "d" ){
			z = ((/[^0-9]/).test(k) && c == "d");
			if( (e[i].length == 2 && dd < 10) || (a[i].length == 2 && c != "d") || (dd > 3 && c == "d") || (z && a[i].length == 2) ){
				dd = "0" + dd;
				dd = dd.substring(dd.length-2);
			}
			vl = String(dd).length;
			ml = 2;
			nv += dd;
		} else if( x == "y" ){
			z = ((/[^0-9]/).test(k) && c == "y");
			if( c == "y" ) { yy = String(yy); }
			else {
				if( a[i].length == 2 ) { yy = d.getYear(); }
				else { yy = d.getFullYear(); }
			}
			if( (e[i].length == 2 && yy < 10) || (a[i].length == 2 && c != "y") || (z && a[i].length == 2) ){
				yy = "0" + yy;
				yy = yy.substring(yy.length-2);
			}
			ml = a[i].length;
			vl = String(yy).length;
			nv += yy;
		}

		if( ((ml == vl || z) && (x == c) && (i < s.length)) || (i < s.length && x != c ) ) { nv += s[i]; }
	}

	if( nv.length > m.length ) { nv = nv.substring(0, m.length); }
	this.strippedValue = (nv == "NaN") ? "" : nv;
	return this.strippedValue;
};

/*************************************
*		MASCARA GENERICA
**************************************/

mascara_generica.prototype = new mascara();
mascara_generica.prototype.constructor = mascara_generica;
function mascara_generica (_mascara) {
	mascara.prototype.constructor.call(this, _mascara);
}

mascara_generica.prototype.format = function (s, d){
	this.value = this.setGeneric(s, d);
	return this.value;
};

mascara_generica.prototype.pre_evento_tecla = function (e, o){
	var xe = new qEvent(e);
	return !( ((xe.keyCode > 47) && (o.value.length >= this._mascara.length)) && !xe.ctrlKey );
};

mascara_generica.prototype.onKeyPress = function(valor, borro) {
	this.value = this.setGeneric(valor, borro);
};

mascara_generica.prototype.setGeneric = function (_v, _d){
	var v = _v, m = this._mascara;
	var r = "x#*", rt = [], nv = "", t, x, a = [], j=0, rx = {"x": "A-Za-z", "#": "0-9", "*": "A-Za-z0-9" };

	// strip out invalid characters
	v = v.replace(new RegExp("[^" + rx["*"] + "]", "gi"), "");
	if( (_d === true) && (v.length == this.strippedValue.length) ) {
		v = v.substring(0, v.length-1);
	}
	this.strippedValue = v;
	var b=[];
	for( var i=0; i < m.length; i++ ) {
		// grab the current character
		x = m.charAt(i);
		// check to see if current character is a mask, escape commands are not a mask character
		t = (r.indexOf(x) > -1);
		// if the current character is an escape command, then grab the next character
		if( x == "!" ) { x = m.charAt(i++); }
		// build a regex to test against
		if( (t && !this.allowPartial) || (t && this.allowPartial && (rt.length < v.length)) ) {
			rt[rt.length] = "[" + rx[x] + "]";
		}
		// build mask definition table
		a[a.length] = { "chr": x, "_mascara": t };
	}

	var hasOneValidChar = false;
	// if the regex fails, return an error
	if( !this.allowPartial && !(new RegExp(rt.join(""))).test(v) ) {
		 return this.throwError(1, "The value \"" + _v + "\" must be in the format " + this._mascara + ".", _v);
	}
	// loop through the mask definition, and build the formatted string
	else if( (this.allowPartial && (v.length > 0)) || !this.allowPartial ){
		for( i=0; i < a.length; i++ ){
			if( a[i]._mascara ){
				while( v.length > 0 && !(new RegExp(rt[j])).test(v.charAt(j)) ) {
					v = (v.length == 1) ? "" : v.substring(1);
				}
				if( v.length > 0 ){
					nv += v.charAt(j);
					hasOneValidChar = true;
				}
				j++;
			} else {
				nv += a[i].chr;
			}
			if( this.allowPartial && (j > v.length) ) { 
				break;
			}
		}
	}
	
	if( this.allowPartial && !hasOneValidChar ) {
		nv = "";
	}
	if( this.allowPartial ){
		if( nv.length < a.length ) {
			this.nextValidChar = rx[a[nv.length].chr];
		} else {
			this.nextValidChar = null;
		}
	}

	return nv;
};



/*************************************
*		MANEJO DE EVENTOS
**************************************/

function qEvent(e){
	// routine for NS, Opera, etc DOM browsers
	if( window.Event  && !ie){
		var isKeyPress = (e.type.substring(0,3) == "key");

		this.keyCode = (isKeyPress) ? parseInt(e.which, 10) : 0;
		this.button = (!isKeyPress) ? parseInt(e.which, 10) : 0;
		this.srcElement = e.target;
		this.type = e.type;
		this.x = e.pageX;
		this.y = e.pageY;
		this.screenX = e.screenX;
		this.screenY = e.screenY;
		if( document.layers ){
			this.altKey = ((e.modifiers & Event.ALT_MASK) > 0);
			this.ctrlKey = ((e.modifiers & Event.CONTROL_MASK) > 0);
			this.shiftKey = ((e.modifiers & Event.SHIFT_MASK) > 0);
			this.keyCode = this.translateKeyCode(this.keyCode);
		} else {
			this.altKey = e.altKey;
			this.ctrlKey = e.ctrlKey;
			this.shiftKey = e.shiftKey;
		}
	// routine for Internet Explorer DOM browsers
	} else {
		e = window.event;
		this.keyCode = parseInt(e.keyCode, 10);
		this.button = e.button;
		this.srcElement = e.srcElement;
		this.type = e.type;
		if( document.all ){
			this.x = e.clientX + document.body.scrollLeft;
			this.y = e.clientY + document.body.scrollTop;
		} else {
			this.x = e.clientX;
			this.y = e.clientY;
		}
		this.screenX = e.screenX;
		this.screenY = e.screenY;
		this.altKey = e.altKey;
		this.ctrlKey = e.ctrlKey;
		this.shiftKey = e.shiftKey;
	}
	if( this.button === 0 ){
		this.setKeyPressed(this.keyCode);
		this.keyChar = String.fromCharCode(this.keyCode);
	}
}

// this method will try to remap the keycodes so the keycode value
// returned will be consistent. this doesn't work for all cases,
// since some browsers don't always return a unique value for a
// key press.
qEvent.prototype.translateKeyCode = function (i){
	var l = {};
	// remap NS4 keycodes to IE/W3C keycodes
	if( !!document.layers ){
		if( this.keyCode > 96 && this.keyCode < 123 ) {
			return this.keyCode - 32;
		}
		l = {
			96:192,126:192,33:49,64:50,35:51,36:52,37:53,94:54,38:55,42:56,40:57,41:48,92:220,124:220,125:221,
			93:221,91:219,123:219,39:222,34:222,47:191,63:191,46:190,62:190,44:188,60:188,45:189,95:189,43:187,
			61:187,59:186,58:186,
			"null": null
		};
	}
	return (!!l[i]) ? l[i] : i;
};

// try to determine the actual value of the key pressed
qEvent.prototype.setKP = function (i, s){
	this.keyPressedCode = i;
	this.keyNonChar = (typeof s == "string");
	this.keyPressed = (this.keyNonChar) ? s : String.fromCharCode(i);
	this.isNumeric = (parseInt(this.keyPressed, 10) == this.keyPressed);
	this.isAlpha = ((this.keyCode > 64 && this.keyCode < 91) && !this.altKey && !this.ctrlKey);
	return true;
};

// try to determine the actual value of the key pressed
qEvent.prototype.setKeyPressed = function (i){
	var b = this.shiftKey;
	if( !b && (i > 64 && i < 91) ) {
		return this.setKP(i + 32);
	}
	if( i > 95 && i < 106 ) {
		return this.setKP(i - 48);
	}
	
	switch( i ){
		case 49: case 51: case 52: case 53: if( b ) {i = i - 16;} break;
		case 50: if( b ) {i = 64;} break;
		case 54: if( b ) {i = 94;} break;
		case 55: if( b ) {i = 38;} break;
		case 56: if( b ) {i = 42;} break;
		case 57: if( b ) {i = 40;} break;
		case 48: if( b ) {i = 41;} break;
		case 192: if( b ) {i = 126;} else {i = 96;} break;
		case 189: if( b ) {i = 95;} else {i = 45;} break;
		case 187: if( b ) {i = 43;} else {i = 61;} break;
		case 220: if( b ) {i = 124;} else {i = 92;} break;
		case 221: if( b ) {i = 125;} else {i = 93;} break;
		case 219: if( b ) {i = 123;} else {i = 91;} break;
		case 222: if( b ) {i = 34;} else {i = 39;} break;
		case 186: if( b ) {i = 58;} else {i = 59;} break;
		case 191: if( b ) {i = 63;} else {i = 47;} break;
		case 190: if( b ) {i = 62;} else {i = 46;} break;
		case 188: if( b ) {i = 60;} else {i = 44;} break;

		case 106: case 57379: i = 42; break;
		case 107: case 57380: i = 43; break;
		case 109: case 57381: i = 45; break;
		case 110: i = 46; break;
		case 111: case 57378: i = 47; break;

		case 8: return this.setKP(i, "[backspace]");
		case 9: return this.setKP(i, "[tab]");
		case 13: return this.setKP(i, "[enter]");
		case 16: case 57389: return this.setKP(i, "[shift]");
		case 17: case 57390: return this.setKP(i, "[ctrl]");
		case 18: case 57388: return this.setKP(i, "[alt]");
		case 19: case 57402: return this.setKP(i, "[break]");
		case 20: return this.setKP(i, "[capslock]");
		case 32: return this.setKP(i, "[space]");
		case 91: return this.setKP(i, "[windows]");
		case 93: return this.setKP(i, "[properties]");

		case 33: case 57371: return this.setKP(i*-1, "[pgup]");
		case 34: case 57372: return this.setKP(i*-1, "[pgdown]");
		case 35: case 57370: return this.setKP(i*-1, "[end]");
		case 36: case 57369: return this.setKP(i*-1, "[home]");
		case 37: case 57375: return this.setKP(i*-1, "[left]");
		case 38: case 57373: return this.setKP(i*-1, "[up]");
		case 39: case 57376: return this.setKP(i*-1, "[right]");
		case 40: case 57374: return this.setKP(i*-1, "[down]");
		case 45: case 57382: return this.setKP(i*-1, "[insert]");
		case 46: case 57383: return this.setKP(i*-1, "[delete]");
		case 144: case 57400: return this.setKP(i*-1, "[numlock]");
	}
	
	if( i > 111 && i < 124 ) {
		return this.setKP(i*-1, "[f" + (i-111) + "]");
	}

	return this.setKP(i);
};

qEvent.prototype.isDelete = function() {
	return( this.keyCode == 8 || this.keyCode == 46 );
};

toba.confirmar_inclusion('efs/mascaras');