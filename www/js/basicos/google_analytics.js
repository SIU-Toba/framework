/* 
 * JS encargado de hacer el tracking mediante
 * Google Analytics
 */

var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));

/**
 *  @class Clase que maneja el tracking mediante Google Analytics
 *  @constructor
 */
function ga()
{
	this._codigo = null;
	this._evento = null;
	this._operacion = null;
	this._titulo = null;
	this._usuario = null;
}
ga.prototype.constructor = ga;

/**
 *  Permite setear el codigo de seguimiento entregado por GA
 *  debe ser utilizado antes de llamar a iniciar
 *  @param {string} cod_seguimiento Por Ejemplo UA-123456-1
 *  @see #iniciar
 */
ga.prototype.set_codigo = function(cod_seguimiento)
{
	this._codigo = cod_seguimiento;
}

/**
 *  Inicializa el objeto que realiza el seguimiento
 */
ga.prototype.iniciar = function()
{
	try{
		this._pageTracker = _gat._getTracker('"'+ this._codigo+'"');
		this.base_config();
	} catch(err) {
	}
}

/**
 *  @private
 */
ga.prototype.base_config = function()
{
	this._pageTracker._setDetectFlash(0);
	this._pageTracker._setDetectTitle(1);
}

/**
 *  Fija el tiempo maximo que durara la 'sesion' en Google Analytics
 *  @param {string} tiempo Tiempo maximo en segundos
 */
ga.prototype.set_timeout = function(tiempo)
{
	this._pageTracker._setSessionTimeout(tiempo);
}

/**
 *  Devuelve una referencia al objeto utilizado para realizar el seguimiento
 * @return {object}
 */
ga.prototype.get_tracker_obj = function()
{
	return this._pageTracker;
}

/**
 *  Permite  fijar el usuario que realiza la accion
 *  @param {string} usuario Codigo de usuario
 */
ga.prototype.add_usuario = function(usuario)
{
	this._usuario = usuario;
}

/**
 * Permite fijar el evento toba que realiza la accion
 * @param {evento_ei} evento
 */
ga.prototype.add_evento = function(evento)
{
	this._evento =  evento.id;
}

/**
 *  Permite setear un titulo especifico
 *  @param {string} titulo Titulo
 *  @private
 */
ga.prototype.add_titulo = function(titulo)
{
	this._titulo = titulo;
}

/**
 *  Permite setear cual es la operacion en seguimiento
 *  @param {string} operacion Operacion
 *  @private
 */
ga.prototype.add_operacion = function(operacion)
{
	this._operacion = operacion;
}

/**
 *  Dispara el envio de la informacion a Google Analytics
 *  para hacer modificaciones previas al envio utilizar
 *  el metodo <em>google_analytics_pre_envio()<em> que recibe
 *  como parametro una referencia a esta clase.
 */
ga.prototype.trace = function ()
{
	try {
		//Se utilizaria como ventana para acceder a la api
		google_analytics_pre_envio(this);
	}catch(err){
	}	
	try{
		//En los ultimos parametros de los eventos podriamos poner el titulo o algo mas.. ver
		this._pageTracker._trackEvent(this._operacion, this._evento, this._titulo, this._usuario);
		this._pageTracker._trackPageview();
	} catch(err) {
	}
}

var estadista = new ga();