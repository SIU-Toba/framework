<?php
php_referencia::instancia()->agregar(__FILE__);

class ci_mensajes extends toba_ci
{
	protected $s__opciones;
	
	function ini()
	{
		/*//Mensaje propio del objeto
		$m_propio = $this->get_mensaje('info_local', array('uno', 'dos', 'tres'));
		//Mensaje a nivel global del proyecto
		$m_global = toba::mensajes()->get('info_global', array('primer', date('d/M/Y')));
	
		//Notificación de los mensajes al usuario desde el mismo objeto
		$this->informar_msg($m_propio, 'info');
		//Notificación de los mensajes al usuario utilizando un mecanismo global
		toba::notificacion()->agregar($m_global, 'info');*/
	}
	
	
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
				break;
			case 'mensaje_componente':
				//Mensaje propio del componente
				$mensaje = $this->get_mensaje('info_local', array('uno', 'dos', 'tres'));
				break;
			case 'mensaje_global':
				$mensaje = toba::mensajes()->get('info_global', array('primer', date('d/M/Y')));
				break;
		}
		
		//-- Cómo lo muestro?
		if ($this->s__opciones['destino'] == 'notificacion') {
			toba::notificacion()->agregar($mensaje, $this->s__opciones['nivel']);
		} else {
			if ($this->s__opciones['componente'] == 'pantalla') {
				$this->pantalla()->set_descripcion($mensaje, $this->s__opciones['nivel']);
			} else {
				$this->dep('opciones')->set_modo_descripcion(false);
				$this->dep('opciones')->set_descripcion($mensaje, $this->s__opciones['nivel']);
			}
			
		}
		//$pant->set_descripcion()
	}
}

?>