//--------------------------------------------------------------------------------
//Clase ei_archivos
ei_archivos.prototype = new ei();
var def = ei_archivos.prototype;
def.constructor = ei_archivos;

function ei_archivos(instancia, input_submit, path_relativo) {
	this._instancia = instancia;				//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
	this._input_submit = input_submit;			//Campo que se setea en el submit del form
	this._path_relativo = path_relativo;
}

	//---Submit
	def.submit = function() {
		var padre_esta_en_proceso = this.controlador && !this.controlador.en_submit();
		if (padre_esta_en_proceso) {
			return this.controlador.submit();
		}
		if (this._evento) {
			document.getElementById(this._input_submit + '__seleccion').value = this._evento.parametros;
			//Marco la ejecucion del evento para que la clase PHP lo reconozca
			document.getElementById(this._input_submit).value = this._evento.id;			
		}
	};

	def.seleccionar_archivo = function(nombre) {
		this.set_evento( new evento_ei('seleccionar_archivo', true, '', nombre));
	};

	def.ir_a_carpeta = function(nombre) {
		this.set_evento( new evento_ei('ir_a_carpeta', true, '', nombre));
	};	
	
	def.crear_carpeta = function(nombre) {
		this._parametros = prompt('Nombre de la carpeta','nombre de la carpeta');
		if (this._parametros !== '' && this._parametros !== null) {
			this.set_evento( new evento_ei('crear_carpeta', true, '', this._parametros));
		}
	};
	
	def.crear_archivo = function(nombre) {
		this._parametros = prompt('Nombre del archivo','nombre del archivo');
		if (this._parametros !== '' && this._parametros !== null) {
			this.set_evento( new evento_ei('crear_archivo', true, '', this._parametros));
		}
	};

toba.confirmar_inclusion('componentes/ei_archivos');
