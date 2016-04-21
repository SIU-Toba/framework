<?php
class cuadro_servicios_consumidos extends toba_ei_cuadro
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Eventos ---------------------------------------------
		
		{$this->objeto_js}.evt__test_conf = function()
		{
			if (this._evento) {
				var valor = this._evento.parametros;
				this.controlador.ajax('test_configuracion', valor, this, this.respuesta_config);
			}
			return false;			
		}
		
		{$this->objeto_js}.respuesta_config = function(respuesta)
		{
			notificacion.agregar(respuesta, respuesta.substr(0, 2) == 'Ok' ? 'info' : 'error');
			notificacion.mostrar();
		}
		";
	}
}

?>