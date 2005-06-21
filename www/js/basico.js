var ie=document.all;
var ns6=document.getElementById && !document.all;

//---STRING
//----------------------------------------------------------

//Retorna la primer ocurrencia de alguna de estas cadenas
String.prototype.primer_ocurrencia = function (cadenas) {
	var primera = this.length;
	for (var i =0 ; i < cadenas.length; i++) {
		ocurrencia = this.indexOf(cadenas[i]);
		if (ocurrencia != -1 && ocurrencia < primera) {
			primera = ocurrencia
		}
	}
	return primera;
}

//Retorna la ultima ocurrencia de alguna de estas cadenas
String.prototype.ultima_ocurrencia = function (cadenas) {
	var ultima = 0;
	for (var i =0 ; i < cadenas.length; i++) {
		ocurrencia = this.lastIndexOf(cadenas[i]);
		if (ocurrencia > ultima) {
			ultima = ocurrencia
		}
	}
	return ultima;
}

//Retorna el quote necesario para usar en una exp. regular
String.prototype.quote_exp_reg = function () {
	return this.reemplazar('$', '\\$');
}

//Reemplaza todas las ocurrencias de un string en otro
String.prototype.reemplazar = function (buscado, nuevo) {
	var srchNdx = 0; 
	var newStr = ""; 
	while (this.indexOf(buscado,srchNdx) != -1) {
	  newStr += this.substring(srchNdx,this.indexOf(buscado,srchNdx));
	  newStr += nuevo;
	  srchNdx = (this.indexOf(buscado,srchNdx) + buscado.length);
	}
	newStr += this.substring(srchNdx,this.length);
	return newStr;
}

String.prototype.intercambiar_caracteres = function(c1, c2) {
	var car_template = '_^_';
	var v1 = this.reemplazar(c2, car_template);
	var v2 = v1.reemplazar(c1, c2);
	var v3 = v2.reemplazar(car_template, c1);
	return v3;
}

String.prototype.trim = function() {
    return this.replace(/^\s*(\S*(\s+\S+)*)\s*$/,'$1');
}

//---ARRAY
//----------------------------------------------------------
function in_array (arreglo, elemento) {
	for (var i=0 ; i < arreglo.length; i++) {
		if (arreglo[i] == elemento)
			return true;
	}
	return false;
}

function array_maximo(arreglo) {
	var maximo = 0;
	for (var i=0 ; i < arreglo.length; i++) {
		if (arreglo[i] > maximo)
			maximo = arreglo[i];
	}
	return maximo;
}

//---Eventos
//--------------------------------------------

// define the addEvent(oElement, sEvent, sCmd, bAppend) function
function addEvent(o, _e, c, _b){
	var e = _e.toLowerCase();
	var b = (typeof _b == "boolean") ? _b : true;
	var x = (o[e]) ? o[e].toString() : "";

	// strip out the body of the function
	x = x.substring(x.indexOf("{")+1, x.lastIndexOf("}"));
	x = ((b) ? (x + c) : (c + x)) + "\n";
	return o[e] = (!!window.Event) ? new Function("event", x) : new Function(x);
}


//---- DOM
//Muestra u oculta un nodo
function toggle_nodo(o) {
	o.style.display = (o.style.display == 'none') ? '' : 'none';
	return true;
}

function getElementPosition(offsetTrail) {
    var offsetLeft = 0;
    var offsetTop = 0;
    while (offsetTrail) {
        offsetLeft += offsetTrail.offsetLeft;
        offsetTop += offsetTrail.offsetTop;
        offsetTrail = offsetTrail.offsetParent;
    }
    if (navigator.userAgent.indexOf("Mac") != -1 && 
        typeof document.body.leftMargin != "undefined") {
        offsetLeft += document.body.leftMargin;
        offsetTop += document.body.topMargin;
    }
    return {left:offsetLeft, top:offsetTop};
}

//----Mediciones de Performance
var mediciones = 
{
	res: new Array(),

	limpiar : function() {
		this.res = new Array();
	},
	
	marcar: function(descripcion) {
		mediciones.res.push([new Date(), descripcion]);
	},
	
	resultados: function() {
		var html = '';
		for (var i=0 ; i < mediciones.res.length; i++) {
			if (i > 0) {
				var ms = mediciones.res[i][0] - mediciones.res[i-1][0];
				html += '[' + mediciones.res[i-1][1] + ' - ' + mediciones.res[i][1] + '] = ' + ms + 'ms.<br>';
			}
		}
		return html;
	}
}

//----Reflexion
//--------------------------------------------

function getObjectClass(obj)
{
    if (obj && obj.constructor && obj.constructor.toString) {
        var arr = obj.constructor.toString().match(/function\s*(\w+)/);
        return arr && arr.length == 2 ? arr[1] : undefined;
    } else {
        return undefined;
    }
}

function existe_funcion(obj, f) {
	for (funcion in obj) {
		if (funcion == f && typeof(obj[funcion])=="function")
			return true;
	}		
	return false;
}

//----Varios
//--------------------------------------------

function ei_arbol(variable, ret) {
	DumperIndentText = "&nbsp";
	DumperNewline = "<br>";
	DumperSepPre = "<a href='javascript: ' onclick=\"o = this.nextSibling; o.style.display = (o.style.display == 'none') ? '' : 'none';\"> m�s </a><span style='display: none'>";
	DumperSepPos = "</span>";		
	DumperMaxDepth = 8;
	if (ret)
		return Dumper(variable);
	else
		DumperPopup(variable);
}


function logger(mensaje, separador) {
	separador = (separador) ? separador : "<br>";
	if (div = document.getElementById('logger_salida')) {
		div.innerHTML += mensaje + separador;
		div.style.display = '';
	}
}

function logger_limpiar() {
	document.getElementById('logger_salida').innerHTML = "";
}



