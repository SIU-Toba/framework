//--------------------------------------------------------------------------------
//Clase objeto_ei_formulario
function objeto_ei_formulario(instancia, rango_tabs, input_submit, evento_defecto) {
	this.instancia = instancia;				//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
	this.rango_tabs = rango_tabs;
	this.input_submit = input_submit;		//Campo que se setea en el submit del form
	this.evento_defecto = evento_defecto;	//Evento por defecto del submit
	
	this.efs = new Array();					//Lista de objeto_ef contenidos
	this.evento = this.evento_defecto;
}
var def = objeto_ei_formulario.prototype;
def.constructor = objeto_ei_formulario;

	def.agregar_ef  = function (ef) {
		if (ef) {
			this.efs[ef.id()] = ef;
		}
	}
	
	def.iniciar = function () {
		for (id_ef in this.efs) {
			this.efs[id_ef].cambiar_tab(this.rango_tabs[0]);
			this.rango_tabs[0]++;
		}
	}
	
	//----Submit
	def.set_evento = function(evento, huella) {
		this.evento = evento;
		document.getElementById(this.input_submit).value = huella;		//Deja la huella del evento
	}
	
	def.submit = function() {
		var evento_actual = this.evento;
		this.evento = this.evento_defecto;
		switch (evento_actual) {
			case 'E':													//Eliminar
				if (!confirm('¿Desea ELIMINAR el registro?')) {
					return false;
				} else {
					return true;
				}; break;
			case 'A':												
			case 'M':													//Agregar o Modificar
				if (!this.validar())
					return false;
				break;
			default:													//Limpiar
				document.getElementById(this.input_submit).value = '';	//Borra la huella del evento anterior 
		}
		return true;
	}
	
	//----Validación
	def.validar = function() {
		for (id_ef in this.efs) {
			var ef = this.efs[id_ef];
			if (! this.efs[id_ef].validar()) {
				ef.seleccionar();
				alert(ef.obtener_error());
				ef.resetear_error();
				return false;
			}
		}
		return true;
	}