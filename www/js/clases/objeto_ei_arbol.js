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
	}

	def.ver_propiedades = function(id) {
		this.set_evento( new evento_ei('ver_propiedades', true, '', id));
/*		try	{
			var requester = new XMLHttpRequest();
		} catch (error) {
			try	{
			  var requester = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (error)	{
			  return false;
			}
		}
		var vinculo = toba.crear_vinculo(this._item_propiedades);
		requester.open("GET", vinculo);
		requester.send(null);
		var stateHandler = function()
		{
			if (requester.readyState == 4) {
				if (requester.status == 200) {
					alert(requester.responseText);
				}
				else
					alert('Error conectando usando XMLHttpRequest');
			}
		}
		
		requester.onreadystatechange = stateHandler;*/
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
