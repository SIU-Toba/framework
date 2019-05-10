/* 
 * JS encargado de hacer el tracking mediante
 * Google Analytics
 */

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
};

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
};

/**
 *  Inicializa el objeto que realiza el seguimiento
 */
ga.prototype.iniciar = function()
{
	try{
		if (typeof gtag == 'undefined') {
			var loader = document.createElement('script');
			loader.src  = 'https://www.googletagmanager.com/gtag/js?id=' + this._codigo;
			document.head.appendChild(loader);
			sleep(1);
		}		
		gtag('config',  this._codigo);
	} catch(err) {
	}
};

/**
 *  @private
 *  @deprecated 
 */
ga.prototype.base_config = function()
{
	console.log('El metodo base_config ya no debe ser utilizado');
};

/**
 *  Fija el tiempo maximo que durara la 'sesion' en Google Analytics
 *  @param {string} tiempo Tiempo maximo en segundos
 */
ga.prototype.set_timeout = function(tiempo)
{
	//gtag('config', 'event_timeout', tiempo);
};

/**
 *  Devuelve una referencia al objeto utilizado para realizar el seguimiento
 * @return {object}
 * @deprecated Se cambio la libreria analytics, invoque la funcion gtag() directamente
 */
ga.prototype.get_tracker_obj = function()
{
	return null;
};

/**
 *  Permite  fijar el usuario que realiza la accion
 *  @param {string} usuario Codigo de usuario
 */
ga.prototype.add_usuario = function(usuario)
{
	this._usuario = usuario;
};

/**
 * Permite fijar el evento toba que realiza la accion
 * @param {evento_ei} evento
 */
ga.prototype.add_evento = function(evento)
{
	this._evento =  evento.id;
};

/**
 *  Permite setear un titulo especifico
 *  @param {string} titulo Titulo
 *  @private
 */
ga.prototype.add_titulo = function(titulo)
{
	this._titulo = titulo;
};

/**
 *  Permite setear cual es la operacion en seguimiento
 *  @param {string} operacion Operacion
 *  @private
 */
ga.prototype.add_operacion = function(operacion)
{
	this._operacion = operacion;
};

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
	};
	try{
		//En los ultimos parametros de los eventos podriamos poner el titulo o algo mas.. ver
		gtag('config',  this._codigo, {'operacion' : this._operacion, 'evento' : this._evento, 'titulo' : this._titulo, 'usuario' : this._usuario});
		gtag('event', 'page_view');
	} catch(err) {
	}
};

var estadista = new ga();