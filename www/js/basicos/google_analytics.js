/* 
 * JS encargado de hacer el tracking mediante
 * Google Analytics
 */

var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));

function ga()
{
	this._codigo = null;
	this._eventos = null;
}
ga.prototype.constructor = ga;

ga.prototype.set_codigo = function(cod_seguimiento)
{
	this._codigo = cod_seguimiento;
}

ga.prototype.iniciar = function()
{
	try{
		this._pageTracker = _gat._getTracker(this._codigo);
		this.base_config();
	} catch(err) {
	}
}

ga.prototype.base_config = function()
{
	this._pageTracker._setDetectFlash(0);
	this._pageTracker._setDetectTitle(1);
}

ga.prototype.set_timeout = function(tiempo)
{
	this._pageTracker._setSessionTimeout(tiempo);
}

ga.prototype.get_tracker_obj = function()
{
	return this._pageTracker;
}

ga.prototype.add_categoria = function(categoria)
{
	this._categoria = categoria;
}

ga.prototype.add_evento = function(evento)
{
	this._eventos =  evento.id;
}

ga.prototype.add_titulo = function(titulo)
{
	this._titulo = titulo;
}

ga.prototype.add_operacion = function(operacion)
{
	this._operacion = operacion;
}

ga.prototype.trace = function ()
{
	if (! isset(this._operacion)){
		this._operacion = toba_hilo_item[1];
	}
	if (! isset(this._titulo)) {
		this._titulo = toba_hilo_item[0];
	}

	try{
		//En los ultimos parametros de los eventos podriamos poner el titulo o algo mas.. ver
		this._pageTracker._trackEvent(this._categoria, this._eventos, this._titulo,this._operacion);
		this._pageTracker._trackPageview();
	} catch(err) {
	}
}

var estadista = new ga();
//toba.confirmar_inclusion('basicos/google_analytics'); esto va?
