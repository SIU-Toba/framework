
ei_archivos.prototype = new ei();
ei_archivos.prototype.constructor = ei_archivos;

/**
 * @class Permite navegar el sistema de archivos del servidor bajo una carpeta inicial
 * @constructor
 * @phpdoc Componentes/Eis/toba_ei_archivos toba_ei_archivos
 */
function ei_archivos(instancia, input_submit, path_relativo) {
	this._instancia = instancia;				//Nombre de la instancia del objeto, permite asociar al objeto con el arbol DOM
	this._input_submit = input_submit;			//Campo que se setea en el submit del form
	this._path_relativo = path_relativo;
}

	//---Submit
	ei_archivos.prototype.submit = function() {
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

	/**
	 * Dispara en el servidor la seleccion de un archivo particular
	 * Como todo evento, se puede reaccionar en javascript atrapando el metodo <em>evt__seleccionar_archivo</em>
	 * @param {string} nombre
	 */
	ei_archivos.prototype.seleccionar_archivo = function(nombre) {
		this.set_evento( new evento_ei('seleccionar_archivo', true, '', nombre));
	};

	/**
	 * Dispara en el servidor la navegacion hacia una subcarpeta
	 * @param {string} nombre
	 */
	ei_archivos.prototype.ir_a_carpeta = function(nombre) {
		this.set_evento( new evento_ei('ir_a_carpeta', true, '', nombre));
	};	
	
	/**
	 * Dispara en el servidor la creacion de una nueva carpeta (preguntando previamente el nombre de la misma al usuario)
	 * @param {string} nombre
	 */
	ei_archivos.prototype.crear_carpeta = function(nombre) {
		this._parametros = prompt('Nombre de la carpeta','nombre de la carpeta');
		if (isset(this._parametros) && this._parametros !== '' && this._parametros !== null) {
			this.set_evento( new evento_ei('crear_carpeta', true, '', this._parametros));
		}
	};
	
	/**
	 * Dispara en el servidor la creación de un archivo particular
	 * @param {string} nombre
	 */
	ei_archivos.prototype.crear_archivo = function(nombre) {
		this._parametros = prompt('Nombre del archivo','nombre del archivo');
		if (isset(this._parametros) && this._parametros !== '' && this._parametros !== null) {
			this.set_evento( new evento_ei('crear_archivo', true, '', this._parametros));
		}
	};

toba.confirmar_inclusion('componentes/ei_archivos');
