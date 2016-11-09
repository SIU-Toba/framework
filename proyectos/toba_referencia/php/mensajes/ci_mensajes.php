<?php
php_referencia::instancia()->agregar(__FILE__);

class ci_mensajes extends toba_ci
{
	protected $s__opciones;
	
	function evt__opciones__modificacion($datos)
	{
		$this->s__opciones = $datos;
	}
	
	function conf__opciones(toba_ei_formulario $form)
	{
		if (isset($this->s__opciones)) {
			$form->set_datos($this->s__opciones);
		}
	}
	
	function evt__mostrar() 
	{
		
		//-- Cual es el mensaje a mostrar?
		$mensaje = null;
		switch ($this->s__opciones['origen']) {
			case 'mensaje_manual':
				$mensaje = $this->s__opciones['texto'];
				$pepe = null;
				break;
			case 'mensaje_componente':
				//Mensaje propio del componente
				$mensaje = $this->get_mensaje('info_local', array('uno', 'dos', 'tres'));
				break;
			case 'mensaje_global':
				$mensaje = toba::mensajes()->get('info_global', array('primer', date('d/M/Y')));
				break;
		}
		
		switch ($this->s__opciones['componente']) {
			case 'modal':
				toba::notificacion()->agregar($mensaje, $this->s__opciones['nivel']);
				break;
			case 'pantalla':
				$this->pantalla()->agregar_notificacion($mensaje, $this->s__opciones['nivel']);
				break;
			case 'formulario':
				$this->dep('opciones')->agregar_notificacion($mensaje, $this->s__opciones['nivel']);
				break;
		}
	}
	
	function extender_objeto_js()
	{
		echo toba::escaper()->escapeJs($this->objeto_js)
			.".evt__mostrar = function() {
				var opciones = this.dep('opciones');
				if (opciones.ef('contexto').get_estado() == 'php') {
					return true;
				} else {
					//Para consultar los otros tipos de mensaje abria que ir al server con ajax o similar
					var mensaje = opciones.ef('texto').get_estado();
					var nivel = opciones.ef('nivel').get_estado();
						
					switch (opciones.ef('componente').get_estado()) {
						case 'modal':
							//Durante la atención de eventos y validaciones no es necesario limpiar y mostrar la cola de notificaciones, se hace automaticamente
							notificacion.agregar(mensaje, nivel);
							break;
						case 'pantalla':
							this.agregar_notificacion(mensaje, nivel);
							break;
						case 'formulario':
							opciones.agregar_notificacion(mensaje, nivel);
							break;													
					}
					return false;
				}
			}
		";
	}
}

?>